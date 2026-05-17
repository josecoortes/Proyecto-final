import { Component, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service'; // Importamos el servicio

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
  private cdr = inject(ChangeDetectorRef);

  credenciales = { email: '', password: '' };
  error = '';
  mensajeExito = '';
  cargando = false;

  iniciarSesion() {
    this.cargando = true;
    this.error = '';
    this.mensajeExito = '';

    this.authService.login(this.credenciales).subscribe({
      next: (respuesta) => {
        this.cargando = false;
        this.mensajeExito = '¡Bienvenido de nuevo, ' + respuesta.user.name + '! Redirigiendo...';
        this.cdr.detectChanges();

        // Retrasamos la recarga para que el usuario pueda ver el bonito mensaje y el throbber desaparezca
        setTimeout(() => {
          if (respuesta.magic_url) {
            window.location.href = respuesta.magic_url;
          } else {
            window.location.href = '/';
          }
        }, 1500);
      },
      error: (err) => {
        this.cargando = false;
        console.error('Error de login:', err);
        if (err.status === 401) {
          this.error = 'Correo o contraseña incorrectos.';
        } else if (err.status === 422) {
          this.error = 'Por favor, rellena los campos correctamente antes de continuar.';
        } else {
          this.error = 'No pudimos conectar con los servidores de Marina. Intenta más tarde.';
        }
        this.cdr.detectChanges();
      }
    });
  }
}
