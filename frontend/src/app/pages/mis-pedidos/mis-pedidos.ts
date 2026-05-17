import { Component, inject, OnInit, OnDestroy, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-mis-pedidos',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="container mx-auto px-4 py-8 max-w-4xl pt-24">
      <h1 class="text-3xl font-bold text-white mb-8 text-center">Mis Pedidos</h1>

      @if (cargando()) {
        <div class="flex justify-center my-12">
          <div class="spinner"></div>
        </div>
      } @else if (error()) {
        <div class="bg-red-500/10 border border-red-500 text-red-500 p-4 rounded-lg text-center">
          {{ error() }}
        </div>
      } @else if (pedidos().length === 0) {
        <div class="text-center bg-white/5 border border-white/10 rounded-2xl p-12 glass-panel">
          <div class="text-6xl mb-4">🍔</div>
          <h2 class="text-2xl text-white font-bold mb-2">Aún no tienes pedidos</h2>
          <p class="text-gray-400 mb-6">¿A qué esperas para probar nuestras increíbles hamburguesas?</p>
          <button routerLink="/" class="btn-primary">Ver la Carta</button>
        </div>
      } @else {
        
        <!-- PEDIDOS ACTIVOS (Con Tracker) -->
        @if (pedidosActivos().length > 0) {
          <h2 class="text-xl font-bold text-green-400 mb-4 border-b border-green-500/30 pb-2">Pedidos en Curso</h2>
          <div class="grid gap-6 mb-12">
            @for (pedido of pedidosActivos(); track pedido.id) {
              <div class="glass-panel p-6 border-green-500/30">
                <div class="flex justify-between items-center mb-6">
                  <div>
                    <h3 class="text-xl font-bold text-white">Pedido #{{ pedido.id }}</h3>
                    <p class="text-sm text-gray-400">{{ pedido.fecha }} - {{ pedido.hora }}</p>
                  </div>
                  <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-white">
                      {{ pedido.metodo_entrega === 'domicilio' ? '🛵 A Domicilio' : '🏪 Recogida' }}
                    </span>
                  </div>
                </div>

                <!-- TRACKER MINIFICADO -->
                <div class="tracker-steps my-8">
                  <div class="step" [class.active]="pedido.estado === 'pendiente'" [class.completed]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 0">
                    <div class="step-icon">📝</div>
                    <p>Recibido</p>
                  </div>
                  <div class="step-line" [class.filled]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 0"></div>
                  
                  <div class="step" [class.active]="pedido.estado === 'preparando'" [class.completed]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 1">
                    <div class="step-icon">🍳</div>
                    <p>Cocina</p>
                  </div>
                  <div class="step-line" [class.filled]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 1"></div>
                  
                  <div class="step" [class.active]="pedido.estado === 'listo'" [class.completed]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 2">
                    <div class="step-icon">🛍️</div>
                    <p>Listo</p>
                  </div>
                  <div class="step-line" [class.filled]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 2"></div>

                  @if (pedido.metodo_entrega === 'domicilio') {
                    <div class="step" [class.active]="pedido.estado === 'en_reparto'" [class.completed]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 3">
                      <div class="step-icon">🛵</div>
                      <p>Reparto</p>
                    </div>
                    <div class="step-line" [class.filled]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > 3"></div>
                  }
                  
                  <div class="step" [class.active]="pedido.estado === 'entregado'" [class.completed]="getEstadoIndex(pedido.estado, pedido.metodo_entrega) > (pedido.metodo_entrega === 'domicilio' ? 4 : 3)">
                    <div class="step-icon">🍔</div>
                    <p>Entregado</p>
                  </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-white/10">
                  <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Tu Comanda</h4>
                  <ul class="space-y-1">
                    @for (plato of pedido.platos; track plato.nombre) {
                      <li class="flex justify-between text-gray-300 text-sm">
                        <span>{{ plato.cantidad }}x {{ plato.nombre }}</span>
                        <span>{{ (plato.precio_unitario * plato.cantidad).toFixed(2) }}€</span>
                      </li>
                    }
                  </ul>
                </div>
              </div>
            }
          </div>
        }

        <!-- HISTORIAL DE PEDIDOS -->
        @if (pedidosPasados().length > 0) {
          <h2 class="text-xl font-bold text-gray-400 mb-4 border-b border-gray-700 pb-2">Historial de Pedidos</h2>
          <div class="grid gap-4">
            @for (pedido of pedidosPasados(); track pedido.id) {
              <div class="glass-panel p-4 flex justify-between items-center opacity-70 hover:opacity-100 transition-opacity">
                <div>
                  <h3 class="font-bold text-white">Pedido #{{ pedido.id }}</h3>
                  <p class="text-xs text-gray-400">{{ pedido.fecha }} - {{ pedido.hora }}</p>
                </div>
                <div class="text-right">
                  @if (pedido.estado === 'entregado') {
                    <span class="text-green-400 font-bold text-sm">✅ Completado</span>
                  } @else if (pedido.estado === 'cancelado') {
                    <span class="text-red-500 font-bold text-sm">❌ Cancelado</span>
                  }
                  <p class="text-xs text-gray-400 mt-1">{{ pedido.metodo_entrega === 'domicilio' ? 'A Domicilio' : 'Recogida' }}</p>
                </div>
              </div>
            }
          </div>
        }

      }
    </div>
  `,
  styles: [`
    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid rgba(255, 255, 255, 0.1);
      border-left-color: var(--color-primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* ESTILOS DEL TRACKER COPIADOS DE PAGO-EXITO */
    .tracker-steps {
      display: flex;
      align-items: center;
      justify-content: space-between;
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
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--color-surface);
      border: 2px solid rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      margin-bottom: 8px;
      transition: all 0.3s;
    }
    .step p {
      font-size: 0.75rem;
      color: var(--color-white);
      margin: 0;
    }
    .step-line {
      flex-grow: 1;
      height: 3px;
      background: rgba(255,255,255,0.1);
      margin: 0 -10px;
      margin-bottom: 25px;
      z-index: 1;
      border-radius: 2px;
      transition: all 0.5s;
    }
    .step-line.filled {
      background: var(--color-primary);
    }
  `]
})
export class MisPedidosComponent implements OnInit, OnDestroy {
  private http = inject(HttpClient);

  cargando = signal(true);
  error = signal('');
  pedidos = signal<any[]>([]);
  
  pedidosActivos = signal<any[]>([]);
  pedidosPasados = signal<any[]>([]);

  private pollingInterval: any;

  ngOnInit() {
    this.cargarPedidos();

    // Actualizamos automáticamente el estado de los pedidos cada 10 segundos
    this.pollingInterval = setInterval(() => {
      // Solo recargamos si hay pedidos activos, para no gastar recursos a lo tonto
      if (this.pedidosActivos().length > 0) {
        this.cargarPedidos(false); // false para no mostrar el spinner grande de nuevo
      }
    }, 10000);
  }

  cargarPedidos(mostrarLoader: boolean = true) {
    if (mostrarLoader) this.cargando.set(true);
    
    if (typeof window === 'undefined' || !window.localStorage) {
      this.cargando.set(false);
      return;
    }
    
    const token = localStorage.getItem('token_auth');
    if (!token) {
      this.error.set('Debes iniciar sesión para ver tus pedidos.');
      this.cargando.set(false);
      return;
    }

    const headers = new HttpHeaders({ 'Authorization': `Bearer ${token}` });

    this.http.get<any[]>(`${environment.apiUrl}/pedidos`, { headers }).subscribe({
      next: (data) => {
        this.pedidos.set(data);
        
        // Separar entre activos e histórico
        // Limitamos los activos a los 3 últimos (por si en modo de pruebas se acumulan muchos)
        this.pedidosActivos.set(data.filter(p => p.estado !== 'entregado' && p.estado !== 'cancelado').slice(0, 3));
        // Limitamos el historial a los 3 últimos para no saturar la pantalla
        this.pedidosPasados.set(data.filter(p => p.estado === 'entregado' || p.estado === 'cancelado').slice(0, 3));
        
        if (mostrarLoader) this.cargando.set(false);
      },
      error: (err) => {
        console.error('Error cargando pedidos', err);
        if (mostrarLoader) this.error.set('No pudimos cargar tus pedidos.');
        if (mostrarLoader) this.cargando.set(false);
      }
    });
  }

  getEstadoIndex(estado: string, metodo: string): number {
    if (metodo === 'recoger') {
      const map: any = { 'pendiente': 0, 'preparando': 1, 'listo': 2, 'entregado': 3, 'cancelado': -1 };
      return map[estado] ?? 0;
    } else {
      const map: any = { 'pendiente': 0, 'preparando': 1, 'listo': 2, 'en_reparto': 3, 'entregado': 4, 'cancelado': -1 };
      return map[estado] ?? 0;
    }
  }

  ngOnDestroy() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
    }
  }
}
