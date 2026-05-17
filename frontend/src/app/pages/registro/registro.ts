import { Component, inject, ChangeDetectorRef } from '@angular/core';
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
  private cdr = inject(ChangeDetectorRef);

  datos = { name: '', email: '', password: '' };
  error = '';
  mensajeExito = '';
  cargando = false;

  registrarse() {
    this.cargando = true;
    this.error = '';
    this.mensajeExito = '';

    this.authService.registro(this.datos).subscribe({
      next: (respuesta) => {
        this.cargando = false;
        this.mensajeExito = '¡Bienvenido a Burguer Marina, ' + respuesta.user.name + '! Entrando...';
        this.cdr.detectChanges();

        setTimeout(() => {
          window.location.href = '/';
        }, 1500);
      },
      error: (err) => {
        this.cargando = false;
        if (err.status === 422) {
          // Si Laravel nos devuelve los errores detallados, sacamos el primero
          if (err.error && err.error.errors) {
            const errores = err.error.errors;
            if (errores.password) {
              this.error = 'La contraseña no es segura. Debe tener 8 caracteres, una mayúscula, un número y un símbolo.';
            } else if (errores.email) {
              this.error = 'Este correo electrónico ya está registrado o no es válido.';
            } else {
              // Coger cualquier otro error de validación
              this.error = Object.values(errores)[0] as string;
            }
          } else {
            this.error = 'Revisa los datos. Asegúrate de usar una contraseña segura.';
          }
        } else {
          this.error = 'Ocurrió un error en los servidores de Marina. Intenta más tarde.';
        }
        this.cdr.detectChanges();
      }
    });
  }
}
