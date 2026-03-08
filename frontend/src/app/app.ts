import { Component } from '@angular/core';
import { RouterOutlet, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';

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
          <li><a routerLink="/login" class="nav-link">Iniciar Sesión</a></li>
          <li><a routerLink="/registro" class="btn-primary btn-sm">Regístrate</a></li>
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
export class App {}