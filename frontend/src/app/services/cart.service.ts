import { Injectable, signal, computed, effect, inject } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../../environments/environment';

export interface Plato {
  id: number;
  nombre: string;
  descripcion: string;
  precio: number;
  imagen: string;
  categoria_id?: number;
}

export interface CartItem {
  plato: Plato;
  cantidad: number;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private http = inject(HttpClient);
  private router = inject(Router);

  // Estado Reactivo del Carrito
  // He metido Signals aquí en vez de liarnos con Subjects de RxJS. 
  // Da menos dolores de cabeza para renderizar las cosas asíncronas en el navbar.
  public cart = signal<CartItem[]>([]);

  // Estado derivado: Cantidad total de items
  public totalItems = computed(() => {
    return this.cart().reduce((acc, item) => acc + item.cantidad, 0);
  });

  // Estado derivado: Precio total
  public totalPrice = computed(() => {
    return this.cart().reduce((acc, item) => acc + (item.plato.precio * item.cantidad), 0);
  });

  // Visibilidad del panel lateral del carrito
  public isCartOpen = signal<boolean>(false);

  constructor() {
    this.cargarDeLocalStorage();

    // Efecto de Angular 16+: Se lanza solo cuando algo en cart cambia.
    // Esto lo busqué y así nos evitamos que si el usuario recarga la página, 
    // se le vacíe el carrito entero y perdamos ventas (imagínate el cabreo).
    effect(() => {
      if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.setItem('marina_burguer_cart', JSON.stringify(this.cart()));
      }
    });
  }

  // --- Operaciones ---

  addToCart(plato: Plato, cantidad: number = 1) {
    const elementos = [...this.cart()];
    const index = elementos.findIndex(item => item.plato.id === plato.id);

    if (index !== -1) {
      // Si ya existe, sumamos la cantidad
      elementos[index].cantidad += cantidad;
    } else {
      // Si no existe, lo añadimos
      elementos.push({ plato, cantidad });
    }

    this.cart.set(elementos);
    
    // Abrimos el carrito como feedback visual al usuario
    this.openCart();
  }

  removeFromCart(platoId: number) {
    this.cart.update(items => items.filter(item => item.plato.id !== platoId));
  }

  updateQuantity(platoId: number, nuevaCantidad: number) {
    if (nuevaCantidad <= 0) {
      this.removeFromCart(platoId);
      return;
    }

    this.cart.update(items => items.map(item => 
      item.plato.id === platoId ? { ...item, cantidad: nuevaCantidad } : item
    ));
  }

  clearCart() {
    this.cart.set([]);
  }

  // --- UI del Carrito ---
  
  openCart() {
    this.isCartOpen.set(true);
  }

  closeCart() {
    this.isCartOpen.set(false);
  }

  toggleCart() {
    this.isCartOpen.set(!this.isCartOpen());
  }

  // --- Persistencia ---

  private cargarDeLocalStorage() {
    try {
      if (typeof window !== 'undefined' && window.localStorage) {
        const guardado = localStorage.getItem('marina_burguer_cart');
        if (guardado) {
          this.cart.set(JSON.parse(guardado));
        }
      }
    } catch (e) {
      console.error('Error cargando carrito local', e);
    }
  }

  // --- Comunicación con la API (Checkout) ---
  
  // Nuevo estado para notificaciones bonitas en vez de alert()
  public notification = signal<{type: 'success'|'error', message: string} | null>(null);
  
  // Bloqueo de UI mientras se procesa para que el usuario no cierre sin querer
  public isProcessing = signal<boolean>(false);
  public isSimulatingPayment = signal<boolean>(false);

  procesarPedido(metodoEntrega: string = 'recoger', direccion: string = '', metodoPago: string = 'efectivo') {
    if (typeof window === 'undefined') return;
    if (this.isProcessing()) return; // Evita doble click

    const token = localStorage.getItem('token_auth');
    if (!token) {
        this.notification.set({ type: 'error', message: 'Debes iniciar sesión primero para poder hacer el pedido.' });
        setTimeout(() => this.router.navigate(['/login']), 2000);
        return;
    }

    if (this.cart().length === 0) {
        this.notification.set({ type: 'error', message: 'Tu carrito está vacío.' });
        return;
    }

    if (metodoEntrega === 'domicilio' && !direccion) {
        this.notification.set({ type: 'error', message: 'Por favor indica tu dirección de entrega.' });
        setTimeout(() => this.notification.set(null), 3000);
        return;
    }

    this.isProcessing.set(true);

    const payload = {
      metodo_entrega: metodoEntrega,
      direccion_empresa: direccion,
      metodo_pago: metodoPago,
      platos: this.cart().map(item => ({
        id: item.plato.id,
        cantidad: item.cantidad
      }))
    };

    // Si pagan con tarjeta, redirigimos a Stripe
    if (metodoPago === 'tarjeta') {
      this.isSimulatingPayment.set(true); // Esto mostrará el loader en la UI
      
      const headers = new HttpHeaders({
        'Authorization': `Bearer ${token}`
      });

      this.http.post(`${environment.apiUrl}/crear-sesion-pago`, payload, { headers })
        .subscribe({
          next: (res: any) => {
            if (res.url) {
              // ¡Magia! Redirigimos a la pasarela de Stripe
              // El carrito NO se vacía aquí, se vaciará en la página de éxito si todo va bien.
              window.location.href = res.url;
            }
          },
          error: (err) => {
            console.error('Error conectando con Stripe:', err);
            this.notification.set({ type: 'error', message: 'El banco no responde. ¿Llevas suelto para pagar en efectivo?' });
            this.isProcessing.set(false);
            this.isSimulatingPayment.set(false);
            setTimeout(() => this.notification.set(null), 4000);
          }
        });
    } else {
      this.enviarPedidoApi(payload, token);
    }
  }

  private enviarPedidoApi(payload: any, token: string) {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });

    this.http.post(`${environment.apiUrl}/pedidos`, payload, { headers })
      .subscribe({
        next: (res: any) => {
          if (payload.metodo_pago === 'tarjeta') {
             this.notification.set({ type: 'success', message: '¡Pago aceptado! Tu pedido ya está en cocina 🔥' });
          } else {
             this.notification.set({ type: 'success', message: '¡Pedido confirmado! Prepara el efectivo 💶' });
          }
          
          this.clearCart();
          
          // Cerramos el carrito y la notificación después de 3.5 segundos
          setTimeout(() => {
            this.notification.set(null);
            this.isProcessing.set(false);
            this.closeCart();
          }, 3500);
        },
        error: (err) => {
          console.error('Fallo al cobrar el pedido:', err);
          if (err.status === 401) {
             this.notification.set({ type: 'error', message: 'Tu sesión ha caducado, por favor vuelve a entrar.' });
             setTimeout(() => { this.isProcessing.set(false); this.router.navigate(['/login']); }, 2000);
          } else {
             this.notification.set({ type: 'error', message: 'Uy, parece que hubo un problema. Inténtalo de nuevo.' });
          }
          
          setTimeout(() => {
             this.notification.set(null);
             this.isProcessing.set(false);
          }, 4000);
        }
      });
  }
}
