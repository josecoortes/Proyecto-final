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
  templateUrl: './app.html',
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
