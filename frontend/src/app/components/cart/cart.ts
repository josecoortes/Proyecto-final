import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule],
  styleUrls: ['./cart.css'],
  template: `
    <!-- Overlay del background (cierra el carrito si haces clic) -->
    <div class="cart-overlay" 
         [class.open]="cartService.isCartOpen()" 
         (click)="!cartService.isProcessing() && cartService.closeCart()">
    </div>

    <!-- Panel lateral del Carrito -->
    <div class="cart-panel" [class.open]="cartService.isCartOpen()">
      <div class="cart-header">
        <h2>Tu Pedido</h2>
        <button class="close-btn" 
                (click)="!cartService.isProcessing() && cartService.closeCart()" 
                [disabled]="cartService.isProcessing()">×</button>
      </div>

      <!-- NOTIFICACIONES (Sustituto de alert()) -->
      @if (cartService.notification(); as notif) {
        <div class="cart-notification" 
             [class.success]="notif.type === 'success'" 
             [class.error]="notif.type === 'error'">
          {{ notif.message }}
        </div>
      }

      <div class="cart-body">
        <!-- Estado Vacío -->
        @if (cartService.cart().length === 0) {
          <div class="empty-cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            <p>Aún no has añadido ninguna Marina Burguer a tu pedido.</p>
          </div>
        }

        <!-- Lista de Productos -->
        @if (cartService.cart().length > 0) {
          <div class="cart-items">
            @for (item of cartService.cart(); track item.plato.id) {
              <div class="cart-item">
                <img [src]="item.plato.imagen || 'https://via.placeholder.com/80?text=BM'" alt="plato" class="cart-item-img">
                
                <div class="cart-item-details">
                  <h4>{{ item.plato.nombre }}</h4>
                  <p class="cart-item-price">
                     {{ item.plato.precio }} €/u 
                     <span class="subtotal-item">→ Subtotal: {{ item.plato.precio * item.cantidad | number:'1.2-2' }} €</span>
                  </p>
                  
                  <div class="quantity-controls">
                    <button (click)="cartService.updateQuantity(item.plato.id, item.cantidad - 1)">-</button>
                    <span>{{ item.cantidad }}</span>
                    <button (click)="cartService.updateQuantity(item.plato.id, item.cantidad + 1)">+</button>
                  </div>
                </div>

                <button class="remove-btn" (click)="cartService.removeFromCart(item.plato.id)">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
              </div>
            }
          </div>
        }
      </div>

      <!-- Footer del Carrito -->
      @if (cartService.cart().length > 0) {
        <div class="cart-footer">
          
          <div class="delivery-options">
            <label for="metodoEntrega">Método de entrega:</label>
            <select id="metodoEntrega" class="delivery-select" (change)="cambiarMetodo($event)">
              <option value="recoger">Recoger en local</option>
              <option value="domicilio">A domicilio</option>
            </select>
            
            @if (metodo === 'domicilio') {
              <input type="text" 
                     placeholder="Ej. Calle Falsa 123, 4ºB" 
                     class="delivery-input mt-2"
                     (input)="cambiarDireccion($event)">
            }
          </div>

          <div class="delivery-options mt-3">
            <label for="metodoPago">Método de Pago:</label>
            <select id="metodoPago" class="delivery-select" (change)="cambiarPago($event)">
              <option value="efectivo">{{ metodo === 'domicilio' ? 'Efectivo al recibir' : 'Pagar en caja (Efectivo)' }}</option>
              <option value="tarjeta">💳 Tarjeta Bancaria (Seguro)</option>
            </select>
          </div>

          <div class="cart-total mt-4">
            <span>Total a pagar:</span>
            <span class="total-price">{{ cartService.totalPrice() | number:'1.2-2' }} €</span>
          </div>
          <button class="btn-primary w-100 checkout-btn" 
                  [disabled]="cartService.isProcessing()"
                  (click)="cartService.procesarPedido(metodo, direccion, metodoPago)">
            @if (cartService.isProcessing() && cartService.isSimulatingPayment()) {
              <span>
                <svg class="spinner inline-block w-4 h-4 mr-2 text-white animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Conectando con el banco...
              </span>
            } @else if (cartService.isProcessing() && !cartService.isSimulatingPayment()) {
              <span>Procesando...</span>
            } @else {
              <span>{{ metodoPago === 'tarjeta' ? 'Confirmar y Pagar' : 'Confirmar Pedido' }}</span>
            }
          </button>
        </div>
      }
    </div>
  `
})
export class CartComponent {
  cartService = inject(CartService);
  metodo = 'recoger';
  direccion = '';
  metodoPago = 'efectivo';

  cambiarMetodo(event: any) {
    this.metodo = event.target.value;
  }

  cambiarDireccion(event: any) {
    this.direccion = event.target.value;
  }

  cambiarPago(event: any) {
    this.metodoPago = event.target.value;
  }
}
