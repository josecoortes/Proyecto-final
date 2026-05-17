import { Component, inject, OnInit, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CartService } from '../../services/cart.service';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [],
  styleUrls: ['./home.css'],
  templateUrl: './home.html'
})
export class HomeComponent implements OnInit {
  private http = inject(HttpClient);
  public cartService = inject(CartService);

  platos = signal<any[]>([]);
  cargando = signal(true);
  error = signal('');

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
            this.platos.set(res.data);
          } else if (Array.isArray(res)) {
            this.platos.set(res);
          }

          this.cargando.set(false);
        },
        error: (e) => {
          console.error('Error al cargar platos:', e);
          this.error.set('Hubo un problema al conectar con el servidor de platos.');
          this.cargando.set(false);
        }
      });
  }
}
