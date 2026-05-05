import { Component, inject, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { CartService } from '../../services/cart.service';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  styleUrls: ['./home.css'],
  template: './home.html'
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
    this.http.get<any>(`${environment.apiUrl}/platos`)
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
