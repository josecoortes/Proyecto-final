import { Component, inject, OnInit, ChangeDetectorRef, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser, CommonModule } from '@angular/common';
import { RouterOutlet, RouterLink, Router } from '@angular/router';
import { AuthService } from './services/auth.service';
import { CartComponent } from './components/cart/cart';
import { CartService } from './services/cart.service';
import { environment } from '../environments/environment';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, CartComponent],
  template: `
    <nav class="navbar">
      <div class="navbar-container">
        <a routerLink="/" class="navbar-logo">
           Burguer Marina
        </a>
        <ul class="navbar-menu">
          <li><a routerLink="/" class="nav-link">Inicio</a></li>
          
          <!-- Botones cuando NO estamos conectados -->
          @if (!isLoggedIn) {
            <li><a routerLink="/login" class="nav-link">Iniciar Sesión</a></li>
            <li><a routerLink="/registro" class="btn-primary btn-sm">Regístrate</a></li>
          }

          <!-- Botones cuando SÍ estamos conectados -->
          @if (isLoggedIn) {
            <li><span class="user-greeting"> Hola, {{ userName }}</span></li>
            <li><button (click)="cerrarSesion()" class="btn-outline-danger btn-sm">Cerrar Sesión</button></li>
          }

          <!-- Botón de Carrito (solo visible en el menú principal) -->
          @if (router.url === '/') {
            <li>
              <button class="cart-trigger-btn" (click)="cartService.openCart()">
                🛒
                @if (cartService.totalItems() > 0) {
                  <span class="cart-badge">
                    {{ cartService.totalItems() }}
                  </span>
                }
              </button>
            </li>
          }
        </ul>
      </div>
    </nav>

    <!-- Componente del Carrito Lateral -->
    <app-cart></app-cart>

    <main class="main-content">
      <router-outlet></router-outlet>
    </main>

    <footer class="footer">
      <div class="footer-content">
        <p>&copy; 2026 Burguer Marina. Todos los derechos reservados.</p>
        <p>El sabor que te mereces. Rápido, fresco y directo a tu puerta.</p>
      </div>
    </footer>
  `,
  styleUrls: ['./app.css']
})
export class App implements OnInit {
  public authService = inject(AuthService);
  public cartService = inject(CartService);
  public router = inject(Router);
  private cdr = inject(ChangeDetectorRef);
  private platformId = inject(PLATFORM_ID);

  isLoggedIn = false;
  userName = 'Invitado';

  ngOnInit() {
    // 1. Verificación Inicial Segura para el navegador
    // Tuve que meter el isPlatformBrowser porque al compilar con SSR 
    // en servidor (Angular Universal) me petaba el localStorage porque en Node no existe.
    if (isPlatformBrowser(this.platformId)) {
      const token = localStorage.getItem('token_auth');
      if (token) {
        this.isLoggedIn = true;
        this.userName = localStorage.getItem('usuario_nombre') || 'Usuario';
        this.cdr.detectChanges(); // Forzar actualización visual
      }
    }

    // 2. Suscripción en tiempo real a los eventos de login/logout
    this.authService.isLoggedIn$.subscribe(status => {
      // Solo actualizamos si cambia realmente
      if (isPlatformBrowser(this.platformId)) {
        if (status) {
          this.isLoggedIn = true;
          this.userName = localStorage.getItem('usuario_nombre') || 'Usuario';
        } else if (!localStorage.getItem('token_auth')) {
          this.isLoggedIn = false;
        }
        this.cdr.detectChanges();
      }
    });
  }

  cerrarSesion() {
    this.authService.logout();
    window.location.href = '/login'; // Recarga completa al salir
  }
}