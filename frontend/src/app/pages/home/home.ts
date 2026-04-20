import { Component, inject, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  styleUrls: ['./home.css'],
  template: `
      <!-- HERO BANNER -->
      <section class="hero-section">
        <div class="hero-content">
          <h1>Sabor que te hace sonreír</h1>
          <p>Disfruta de las mejores hamburguesas de la ciudad, elaboradas con ingredientes frescos todos los días. 100% Vacuno Marina.</p>
          <button class="btn-accent hero-btn" (click)="scrollToMenu()">Ver Menú Completo</button>
        </div>
      </section>

      <!-- SECCIÓN DE PRODUCTOS -->
      <section id="menu-section" class="menu-section">
        <div class="menu-header">
          <h2>Nuestros Clásicos</h2>
          <p>Calidad en cada bocado.</p>
        </div>

        <div *ngIf="cargando" class="loading-state">
          <div class="spinner"></div>
          <p>Preparando la parrilla...</p>
        </div>

        <div *ngIf="error" class="error-msg">
          {{ error }}
        </div>

        <div *ngIf="!cargando && platos.length > 0" class="products-grid">
          <div *ngFor="let plato of platos" class="product-card">
            <div class="product-img-wrapper">
              <img [src]="plato.imagen || 'https://via.placeholder.com/400x250/FFC72C/27251F?text=Burguer+Marina'"
                   [alt]="plato.nombre" class="product-img">
              <div class="product-price-badge">{{ plato.precio }} €</div>
            </div>

            <div class="product-info">
              <h3>{{ plato.nombre }}</h3>
              <p class="product-desc">{{ plato.descripcion }}</p>

              <div class="product-actions">
                <button class="btn-primary w-100" (click)="cartService.addToCart(plato)">Añadir al Pedido</button>
              </div>
            </div>
          </div>
        </div>

        <div *ngIf="!cargando && platos.length === 0 && !error" class="empty-state">
          No hay platos disponibles en este momento. Volvemos enseguida.
        </div>
      </section>
  `
})
export class HomeComponent implements OnInit {
  private http = inject(HttpClient);
  private cd = inject(ChangeDetectorRef);
  public cartService = inject(CartService);

  platos: any[] = [];
  cargando = true;
  error = '';

  scrollToMenu() {
    const element = document.getElementById('menu-section');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  }

  ngOnInit() {
    this.http.get<any>('http://127.0.0.1:8000/api/platos')
      .subscribe({
        next: (res) => {
          // Ojo aquí: Laravel hace una paginación que te mete los platos dentro 
          // de un array 'data' (res.data). Si dejamos res a secas como antes, revienta el ngFor de Angular.
          if (res && res.data && Array.isArray(res.data)) {
            this.platos = res.data;
          } else if (Array.isArray(res)) {
            this.platos = res;
          }

          this.cargando = false;
          this.cd.detectChanges();
        },
        error: (e) => {
          console.error('Error al cargar platos:', e);
          this.error = 'Hubo un problema al conectar con el servidor de platos.';
          this.cargando = false;
          this.cd.detectChanges();
        }
      });
  }
}
