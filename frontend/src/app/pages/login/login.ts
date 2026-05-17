import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { environment } from '../../../environments/environment';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule],
  styleUrls: ['./login.css'],
  templateUrl: './login.html'
})
export class LoginComponent {
  private authService = inject(AuthService);
  private router = inject(Router);

  credenciales = { email: '', password: '' };
  error = signal('');
  mensajeExito = signal('');
  cargando = signal(false);

  iniciarSesion() {
    this.cargando.set(true);
    this.error.set('');
    this.mensajeExito.set('');

    this.authService.login(this.credenciales).subscribe({
      next: (respuesta) => {
        this.cargando.set(false);
        this.mensajeExito.set('¡Bienvenido de nuevo, ' + respuesta.user.name + '! Redirigiendo...');

        // Retrasamos la recarga para que el usuario pueda ver el mensaje de bienvenida
        setTimeout(() => {
          if (respuesta.admin_token) {
            // Usuario staff: redirigir al panel admin con auto-login en el backend
            const baseUrl = environment.apiUrl.replace('/api', '');
            window.location.href = baseUrl + '/admin/login?auto=' + encodeURIComponent(respuesta.admin_token);
          } else {
            // Usuario cliente: ir a la tienda
            window.location.href = '/';
          }
        }, 1500);
      },
      error: (err) => {
        this.cargando.set(false);
        console.error('Error de login:', err);
        if (err.status === 401) {
          this.error.set('Correo o contraseña incorrectos.');
        } else if (err.status === 422) {
          this.error.set('Por favor, rellena los campos correctamente antes de continuar.');
        } else {
          this.error.set('No pudimos conectar con los servidores de Marina. Intenta más tarde.');
        }
      }
    });
  }
}
