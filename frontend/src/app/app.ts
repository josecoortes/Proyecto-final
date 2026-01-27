import { Component, inject, ChangeDetectorRef, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div style="font-family: sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px;">
      
      <header style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #d35400; font-size: 3em;">🍔 FoodDelivery</h1>
        <p style="color: #7f8c8d; font-size: 1.2em;">Tus platos favoritos a un clic</p>
      </header>

      <div *ngIf="cargando" style="text-align:center; font-size: 20px; color: #666;">
        ⏳ Cargando carta...
      </div>

      <div *ngIf="error" style="background-color: #ffdddd; color: red; padding: 15px; text-align: center;">
        {{ error }}
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <div *ngFor="let plato of platos" style="border: 1px solid #eee; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
          
          <img [src]="plato.imagen" [alt]="plato.nombre" style="width: 100%; hieght: 200px; object-fit: cover;">
          
          <div style="padding: 20px;">
            <h2 style="margin: 0 0 10px 0; color: #2c3e50;">{{ plato.nombre }}</h2>
            <p style="color: #7f8c8d; height: 60px;">{{ plato.descripcion }}</p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
              <span style="font-size: 1.5em; font-weight: bold; color: #27ae60;">{{ plato.precio }} €</span>
              <button style="background-color: #d35400; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                Añadir
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>
  `,
  styles: []
})
export class App implements OnInit {
  private http = inject(HttpClient);
  private cd = inject(ChangeDetectorRef);

  platos: any[] = []; // Aquí guardaremos los datos del JSON
  cargando = true;
  error = '';

  // ngOnInit se ejecuta solo al arrancar la página
  ngOnInit() {
    this.cargarPlatos();
  }

  cargarPlatos() {
    // Llamamos a la API de Laravel
    this.http.get<any[]>('http://127.0.0.1:8000/api/platos')
      .subscribe({
        next: (data) => {
          console.log('Platos recibidos:', data);
          this.platos = data; // Guardamos el JSON en la variable
          this.cargando = false;
          this.cd.detectChanges(); // Forzamos actualización de pantalla
        },
        error: (e) => {
          console.error(e);
          this.error = 'Error al cargar los platos. Comprueba que el backend funciona.';
          this.cargando = false;
          this.cd.detectChanges();
        }
      });
  }
}