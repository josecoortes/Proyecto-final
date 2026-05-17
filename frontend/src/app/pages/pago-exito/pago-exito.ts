import { Component, inject, OnInit, OnDestroy, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-pago-exito',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="pago-exito-container glass-panel">
      @if (cargando()) {
        <div class="loading-state">
          <div class="spinner"></div>
          <p>Verificando pago con el banco...</p>
        </div>
      } @else if (error()) {
        <div class="icono-cancelado mb-4">❌</div>
        <h1>Error en el Pago</h1>
        <div class="mensaje-error">
          <p>{{ error() }}</p>
        </div>
        <button routerLink="/" class="btn-primary mt-4">Volver al Inicio</button>
      } @else {
        <div class="icono-exito">✅</div>
        <h1>¡Pedido Confirmado!</h1>
        <p class="subtitulo">Número de pedido: <strong>#{{ pedidoId() }}</strong></p>
        
        <!-- TRACKER EN TIEMPO REAL -->
        <div class="tracker-container">
          <h3 class="tracker-title">Sigue tu pedido en tiempo real</h3>
          
          <div class="tracker-steps">
            <div class="step" [class.active]="estadoActual() === 'pendiente'" [class.completed]="estadoIndex() > 0">
              <div class="step-icon">📝</div>
              <p>Recibido</p>
            </div>
            <div class="step-line" [class.filled]="estadoIndex() > 0"></div>
            
            <div class="step" [class.active]="estadoActual() === 'preparando'" [class.completed]="estadoIndex() > 1">
              <div class="step-icon">🍳</div>
              <p>Cocina</p>
            </div>
            <div class="step-line" [class.filled]="estadoIndex() > 1"></div>
            
            <div class="step" [class.active]="estadoActual() === 'listo'" [class.completed]="estadoIndex() > 2">
              <div class="step-icon">🛍️</div>
              <p>Listo</p>
            </div>
            <div class="step-line" [class.filled]="estadoIndex() > 2"></div>
            
            @if(metodoEntrega() === 'domicilio') {
              <div class="step" [class.active]="estadoActual() === 'en_reparto'" [class.completed]="estadoIndex() > 3">
                <div class="step-icon">🛵</div>
                <p>Reparto</p>
              </div>
              <div class="step-line" [class.filled]="estadoIndex() > 3"></div>
            }
            
            <div class="step" [class.active]="estadoActual() === 'entregado'" [class.completed]="estadoIndex() > (metodoEntrega() === 'domicilio' ? 4 : 3)">
              <div class="step-icon">🍔</div>
              <p>Entregado</p>
            </div>
          </div>
          
          <div class="tracker-status">
            @if(estadoActual() === 'pendiente') {
              <p class="status-pulse">Esperando a que la cocina acepte tu pedido...</p>
            } @else if(estadoActual() === 'preparando') {
              <p class="status-pulse">¡Nuestros chefs están preparando tus hamburguesas!</p>
            } @else if(estadoActual() === 'listo') {
              <p class="status-pulse">¡Tu pedido está listo! {{ metodoEntrega() === 'domicilio' ? 'Esperando al repartidor.' : 'Ven a recogerlo cuando quieras.' }}</p>
            } @else if(estadoActual() === 'en_reparto') {
              <p class="status-pulse">El repartidor está de camino. ¡Sal a la puerta!</p>
            } @else if(estadoActual() === 'entregado') {
              <p class="status-success">¡Disfruta de tu comida! Gracias por elegir Marina.</p>
            } @else if(estadoActual() === 'cancelado') {
              <p class="status-error">El restaurante ha tenido que cancelar tu pedido. Contacta con nosotros.</p>
            }
          </div>
        </div>

        <button routerLink="/" class="btn-primary mt-4">Ir a la tienda</button>
      }
    </div>
  `,
  styles: [`
    .pago-exito-container {
      max-width: 600px;
      margin: 80px auto;
      padding: 50px;
      text-align: center;
      background: var(--color-surface);
    }
    .icono-exito {
      font-size: 5rem;
      margin-bottom: 20px;
      animation: pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    h1 { color: var(--color-white); font-size: 2.5rem; }
    .subtitulo { color: var(--color-text-muted); font-size: 1.1rem; margin-bottom: 30px; }
    .detalles-pedido {
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 12px;
      margin: 20px 0;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .mensaje-error { color: #ff5252; padding: 20px; font-weight: bold; background: rgba(255,82,82,0.1); border-radius: 8px; }
    .mt-4 { margin-top: 30px; }
    
    /* ESTILOS DEL TRACKER */
    .tracker-container {
      background: rgba(0, 0, 0, 0.2);
      border-radius: 16px;
      padding: 30px 20px;
      margin: 30px 0;
      border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .tracker-title {
      color: var(--color-white);
      margin-bottom: 30px;
      font-size: 1.2rem;
      font-weight: 600;
    }
    .tracker-steps {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
      position: relative;
    }
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 2;
      width: 60px;
      transition: all 0.3s;
      opacity: 0.5;
      filter: grayscale(1);
    }
    .step.active {
      opacity: 1;
      filter: grayscale(0);
      transform: scale(1.1);
    }
    .step.active .step-icon {
      border-color: var(--color-primary);
      box-shadow: 0 0 15px rgba(255, 107, 107, 0.5);
    }
    .step.completed {
      opacity: 1;
      filter: grayscale(0);
    }
    .step.completed .step-icon {
      background: var(--color-primary);
      color: white;
      border-color: var(--color-primary);
    }
    .step-icon {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: var(--color-surface);
      border: 2px solid rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 8px;
      transition: all 0.3s;
    }
    .step p {
      font-size: 0.8rem;
      color: var(--color-white);
      margin: 0;
    }
    .step-line {
      flex-grow: 1;
      height: 4px;
      background: rgba(255,255,255,0.1);
      margin: 0 -10px;
      margin-bottom: 25px; /* Para alinear con el círculo y no con el texto */
      z-index: 1;
      border-radius: 2px;
      transition: all 0.5s;
    }
    .step-line.filled {
      background: var(--color-primary);
    }
    .tracker-status {
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    .status-pulse {
      color: var(--color-primary);
      font-weight: 500;
      animation: pulse 2s infinite;
    }
    .status-success { color: #4ade80; font-weight: bold; }
    .status-error { color: #ff5252; font-weight: bold; }
    @keyframes pulse {
      0% { opacity: 0.7; }
      50% { opacity: 1; }
      100% { opacity: 0.7; }
    }
  `]
})
export class PagoExitoComponent implements OnInit, OnDestroy {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private http = inject(HttpClient);
  private cartService = inject(CartService);

  cargando = signal(true);
  error = signal('');
  pedidoId = signal('');
  metodoEntrega = signal('domicilio');
  
  estadoActual = signal('pendiente');
  estadoIndex = computed(() => {
    if (this.metodoEntrega() === 'recoger') {
      const map: any = { 'pendiente': 0, 'preparando': 1, 'listo': 2, 'entregado': 3, 'cancelado': -1 };
      return map[this.estadoActual()] ?? 0;
    } else {
      const map: any = { 'pendiente': 0, 'preparando': 1, 'listo': 2, 'en_reparto': 3, 'entregado': 4, 'cancelado': -1 };
      return map[this.estadoActual()] ?? 0;
    }
  });

  private pollingInterval: any;

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      const sessionId = params['session_id'];
      const pedido = params['pedido_id'];
      
      if (!sessionId || !pedido) {
        this.error.set('Faltan datos de verificación del pago.');
        this.cargando.set(false);
        return;
      }

      this.pedidoId.set(pedido);
      this.confirmarPagoBackend(sessionId, pedido);
    });
  }

  confirmarPagoBackend(sessionId: string, pedidoId: string) {
    if (typeof window === 'undefined' || !window.localStorage) return;
    
    const token = localStorage.getItem('token_auth');
    if (!token) return;

    const headers = new HttpHeaders({ 'Authorization': `Bearer ${token}` });

    this.http.post(`${environment.apiUrl}/confirmar-pago`, {
      session_id: sessionId,
      pedido_id: parseInt(pedidoId)
    }, { headers }).subscribe({
      next: () => {
        this.cargando.set(false);
        // ¡Pago exitoso! Ahora sí vaciamos el carrito
        this.cartService.clearCart();
        // Iniciamos el tracking del pedido
        this.iniciarTracking();
      },
      error: (err) => {
        console.error('Error verificando pago:', err);
        this.error.set('No pudimos verificar el pago con el banco, contacta con soporte si te han cobrado.');
        this.cargando.set(false);
      }
    });
  }

  iniciarTracking() {
    this.obtenerEstadoPedido(); // Llama inmediatamente
    // Y luego cada 10 segundos
    this.pollingInterval = setInterval(() => {
      if (this.estadoActual() !== 'entregado' && this.estadoActual() !== 'cancelado') {
        this.obtenerEstadoPedido();
      }
    }, 10000);
  }

  obtenerEstadoPedido() {
    if (typeof window === 'undefined' || !window.localStorage) return;
    
    const token = localStorage.getItem('token_auth');
    if (!token) return;

    const headers = new HttpHeaders({ 'Authorization': `Bearer ${token}` });
    
    // Consultamos la lista de pedidos del usuario
    this.http.get<any[]>(`${environment.apiUrl}/pedidos`, { headers }).subscribe({
      next: (pedidos) => {
        // Buscamos el pedido actual
        const miPedido = pedidos.find(p => p.id === parseInt(this.pedidoId()));
        if (miPedido) {
          this.metodoEntrega.set(miPedido.metodo_entrega);
          this.estadoActual.set(miPedido.estado);
        }
      }
    });
  }

  ngOnDestroy() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
    }
  }
}
