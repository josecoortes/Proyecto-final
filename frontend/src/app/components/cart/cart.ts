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
         (click)="cartService.closeCart()">
    </div>

    <!-- Panel lateral del Carrito -->
    <div class="cart-panel" [class.open]="cartService.isCartOpen()">
      <div class="cart-header">
        <h2>Tu Pedido</h2>
        <button class="close-btn" (click)="cartService.closeCart()">×</button>
      </div>

      <div class="cart-body">
        <!-- Estado Vacío -->
        <div *ngIf="cartService.cart().length === 0" class="empty-cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <p>Aún no has añadido ninguna Marina Burguer a tu pedido.</p>
        </div>

        <!-- Lista de Productos -->
        <div class="cart-items" *ngIf="cartService.cart().length > 0">
          <div class="cart-item" *ngFor="let item of cartService.cart()">
            <img [src]="item.plato.imagen || 'https://via.placeholder.com/80?text=BM'" alt="plato" class="cart-item-img">
            
            <div class="cart-item-details">
              <h4>{{ item.plato.nombre }}</h4>
              <p class="cart-item-price">{{ item.plato.precio }} €</p>
              
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
        </div>
      </div>

      <!-- Footer del Carrito -->
      <div class="cart-footer" *ngIf="cartService.cart().length > 0">
        <div class="cart-total">
          <span>Total a pagar:</span>
          <span class="total-price">{{ cartService.totalPrice() | number:'1.2-2' }} €</span>
        </div>
        <button class="btn-primary w-100 checkout-btn">Procesar Pedido</button>
      </div>
    </div>
  `
})
export class CartComponent {
  cartService = inject(CartService);
}
