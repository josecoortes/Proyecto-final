import { Component, inject, OnInit, ChangeDetectorRef, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser, CommonModule } from '@angular/common';
import { RouterOutlet, RouterLink, Router } from '@angular/router';
import { AuthService } from './services/auth.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink], 
  template: `
    <nav class="navbar">
      <div class="navbar-container">
        <a routerLink="/" class="navbar-logo">
          🍔 Burguer Marina
        </a>
        <ul class="navbar-menu">
          <li><a routerLink="/" class="nav-link">Inicio</a></li>
          
          <!-- Botones cuando NO estamos conectados -->
          <ng-container *ngIf="!isLoggedIn">
            <li><a routerLink="/login" class="nav-link">Iniciar Sesión</a></li>
            <li><a routerLink="/registro" class="btn-primary btn-sm">Regístrate</a></li>
          </ng-container>

          <!-- Botones cuando SÍ estamos conectados -->
          <ng-container *ngIf="isLoggedIn">
            <li><span class="user-greeting">👤 Hola, {{ userName }}</span></li>
            <li><button (click)="cerrarSesion()" class="btn-outline-danger btn-sm">Cerrar Sesión</button></li>
          </ng-container>
        </ul>
      </div>
    </nav>

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
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);
  private platformId = inject(PLATFORM_ID);

  isLoggedIn = false;
  userName = 'Invitado';

  ngOnInit() {
    // 1. Verificación Inicial Segura para el navegador
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
      // Solo actualizamos si cambia realmente (para no machacar el estado inicial que leímos arriba si status llega como false por defecto en el constructor del AuthService)
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