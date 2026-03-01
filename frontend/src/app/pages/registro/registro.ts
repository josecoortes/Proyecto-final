import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
      <h2 style="text-align: center; color: #333;">Crear Cuenta</h2>

      <div *ngIf="error" style="color: white; background: red; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
        {{ error }}
      </div>

      <form (ngSubmit)="registrarse()">
        <div style="margin-bottom: 15px;">
          <label>Nombre:</label>
          <input type="text" [(ngModel)]="datos.name" name="name" style="width: 100%; padding: 8px;" required>
        </div>

        <div style="margin-bottom: 15px;">
          <label>Email:</label>
          <input type="email" [(ngModel)]="datos.email" name="email" style="width: 100%; padding: 8px;" required>
        </div>

        <div style="margin-bottom: 15px;">
          <label>Contraseña:</label>
          <input type="password" [(ngModel)]="datos.password" name="password" style="width: 100%; padding: 8px;" required>
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer;">
          Registrarse
        </button>
      </form>
    </div>
  `
})
export class RegistroComponent {
  private http = inject(HttpClient);
  private router = inject(Router);

  datos = { name: '', email: '', password: '' };
  error = '';

  registrarse() {
    console.log('Enviando datos...', this.datos); // CHIVATO 1

    this.http.post<any>('http://127.0.0.1:8000/api/register', this.datos)
      .subscribe({
        next: (respuesta) => {
          console.log('RESPUESTA ÉXITO:', respuesta); // CHIVATO 2
          alert('¡Usuario creado con éxito!');
          this.router.navigate(['/']);
        },
        error: (err) => {
          console.error('ERROR DETECTADO:', err); // CHIVATO 3
          this.error = 'Ocurrió un error (Mira la consola F12)';
        }
      });
  }
}
