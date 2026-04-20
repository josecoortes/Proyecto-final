import { Injectable, signal, computed, effect } from '@angular/core';

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
      localStorage.setItem('marina_burguer_cart', JSON.stringify(this.cart()));
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
      const guardado = localStorage.getItem('marina_burguer_cart');
      if (guardado) {
        this.cart.set(JSON.parse(guardado));
      }
    } catch (e) {
      console.error('Error cargando carrito local', e);
    }
  }
}
