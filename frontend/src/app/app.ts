import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
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
export class App {
  public authService = inject(AuthService);
  public cartService = inject(CartService);
  public router = inject(Router);

  cerrarSesion() {
    this.authService.logout();
    window.location.href = '/login'; // Recarga completa al salir
  }
}
