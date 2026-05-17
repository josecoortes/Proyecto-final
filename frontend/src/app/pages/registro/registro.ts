import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [FormsModule],
  // Trucazo: Reutilizamos el CSS del login para no repetir el código (la tarjeta es igual)
  styleUrls: ['../login/login.css'],
  templateUrl: './registro.html'
})
export class RegistroComponent {
  private authService = inject(AuthService);
  private router = inject(Router);

  datos = { name: '', email: '', password: '' };
  error = signal('');
  mensajeExito = signal('');
  cargando = signal(false);

  registrarse() {
    this.cargando.set(true);
    this.error.set('');
    this.mensajeExito.set('');

    this.authService.registro(this.datos).subscribe({
      next: (respuesta) => {
        this.cargando.set(false);
        this.mensajeExito.set('¡Bienvenido a Burguer Marina, ' + respuesta.user.name + '! Entrando...');

        setTimeout(() => {
          window.location.href = '/';
        }, 1500);
      },
      error: (err) => {
        this.cargando.set(false);
        if (err.status === 422) {
          // Si Laravel nos devuelve los errores detallados, sacamos el primero
          if (err.error && err.error.errors) {
            const errores = err.error.errors;
            if (errores.password) {
              this.error.set('La contraseña no es segura. Debe tener 8 caracteres, una mayúscula, un número y un símbolo.');
            } else if (errores.email) {
              this.error.set('Este correo electrónico ya está registrado o no es válido.');
            } else {
              // Coger cualquier otro error de validación
              this.error.set(Object.values(errores)[0] as string);
            }
          } else {
            this.error.set('Revisa los datos. Asegúrate de usar una contraseña segura.');
          }
        } else {
          if (err.error && err.error.error) {
            this.error.set('Error Técnico (Pásale esto al profe): ' + err.error.error);
          } else {
            this.error.set('Ocurrió un error en los servidores de Marina. Intenta más tarde.');
          }
        }
      }
    });
  }
}
