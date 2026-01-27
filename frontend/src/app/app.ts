import { Component, inject, ChangeDetectorRef } from '@angular/core'; // <--- Importamos esto
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule], // NECESARIO para que funcione *ngIf
  template: `
    <div style="text-align:center; padding: 50px; font-family: sans-serif;">
      <h1>Proyecto Integrado DAW</h1>
      
      <div *ngIf="mensaje" style="background-color: #d4edda; color: #155724; padding: 20px; margin: 20px auto; border: 1px solid green; max-width: 500px;">
        <h3>¡ÉXITO!</h3>
        <p>{{ mensaje }}</p>
      </div>

      <div *ngIf="error" style="background-color: #f8d7da; color: #721c24; padding: 20px; margin: 20px auto; border: 1px solid red; max-width: 500px;">
        <h3>ERROR</h3>
        <p>{{ error }}</p>
      </div>

      <button (click)="conectarConLaravel()" style="font-size: 18px; padding: 10px 20px; cursor: pointer;">
        Probar conexión
      </button>
      
      <p style="margin-top: 20px; color: grey;">(Mira la consola F12 si no cambia nada)</p>
    </div>
  `,
  styles: []
})
export class App {
  private http = inject(HttpClient);
  private cd = inject(ChangeDetectorRef); // <--- La herramienta para forzar el repintado

  mensaje: string = '';
  error: string = '';

  conectarConLaravel() {
    console.log('Iniciando petición...');
    this.mensaje = '';
    this.error = '';

    this.http.get<any>('http://127.0.0.1:8000/api/saludo')
      .subscribe({
        next: (data) => {
          console.log('Datos recibidos:', data);
          
          // 1. Asignamos el valor
          this.mensaje = data.mensaje;
          
          // 2. OBLIGAMOS a Angular a actualizar la pantalla
          this.cd.detectChanges(); 
        },
        error: (e) => {
          console.error('Error:', e);
          this.error = 'Falló la conexión. Revisa la consola.';
          
          // 2. OBLIGAMOS a Angular a actualizar la pantalla
          this.cd.detectChanges();
        }
      });
  }
}