import { Component, inject, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule],
  template: `
      <div *ngIf="cargando" style="text-align:center; font-size: 20px; color: #666; margin-top: 50px;">
        ⏳ Cargando nuestra carta...
      </div>

      <div *ngIf="error" style="background-color: #ffdddd; color: red; padding: 15px; text-align: center; margin: 20px; border-radius: 8px;">
        {{ error }}
      </div>

      <div *ngIf="!cargando && platos.length > 0"
           style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; padding: 20px;">

        <div *ngFor="let plato of platos" style="border: 1px solid #eee; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); background: white;">
          <img [src]="plato.imagen || 'https://via.placeholder.com/400x250?text=Marina+BBQ'"
               [alt]="plato.nombre"
               style="width: 100%; height: 200px; object-fit: cover;">

          <div style="padding: 20px;">
            <h2 style="margin: 0 0 10px 0; color: #2c3e50;">{{ plato.nombre }}</h2>
            <p style="color: #7f8c8d; height: 60px; overflow: hidden;">{{ plato.descripcion }}</p>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
              <span style="font-size: 1.5em; font-weight: bold; color: #27ae60;">{{ plato.precio }} €</span>
              <button style="background-color: #d35400; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                Añadir
              </button>
            </div>
          </div>
        </div>
      </div>

      <div *ngIf="!cargando && platos.length === 0 && !error" style="text-align:center; color: #999; margin-top: 50px;">
        No hay platos disponibles en este momento.
      </div>
  `
})
export class HomeComponent implements OnInit {
  private http = inject(HttpClient);
  private cd = inject(ChangeDetectorRef);

  platos: any[] = [];
  cargando = true;
  error = '';

  ngOnInit() {
    this.http.get<any>('http://127.0.0.1:8000/api/platos')
      .subscribe({
        next: (res) => {
          // CLAVE: Extraemos el array 'data' de la respuesta paginada
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
