import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
      <h2 style="text-align: center; color: #333;">Iniciar Sesión</h2>

      <div *ngIf="error" style="background-color: #ffdddd; color: red; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
        {{ error }}
      </div>

      <form (ngSubmit)="iniciarSesion()">
        <div style="margin-bottom: 15px;">
          <label>Email:</label>
          <input type="email" [(ngModel)]="credenciales.email" name="email" style="width: 100%; padding: 8px;" required>
        </div>

        <div style="margin-bottom: 15px;">
          <label>Contraseña:</label>
          <input type="password" [(ngModel)]="credenciales.password" name="password" style="width: 100%; padding: 8px;" required>
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer;">
          Entrar
        </button>
      </form>
    </div>
  `
})
export class LoginComponent {
  private http = inject(HttpClient);
  private router = inject(Router);

  credenciales = { email: '', password: '' };
  error = '';

  iniciarSesion() {
    this.http.post<any>('http://127.0.0.1:8000/api/login', this.credenciales)
      .subscribe({
        next: (respuesta) => {
          // 1. Guardamos el token en la "caja fuerte" del navegador (LocalStorage)
          localStorage.setItem('token_auth', respuesta.access_token);

          // 2. Guardamos también el nombre del usuario para saludarle luego
          localStorage.setItem('usuario_nombre', respuesta.user.name);

          alert('¡Bienvenido de nuevo, ' + respuesta.user.name + '!');
          this.router.navigate(['/']); // Volver al inicio
        },
        error: (err) => {
          console.error(err);
          this.error = 'Email o contraseña incorrectos';
        }
      });
  }
}
