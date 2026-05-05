import { Component, inject, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, FormsModule],
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
          this.error = 'Revisa los datos. Es posible que el correo ya esté en uso o la contraseña sea muy corta.';
        } else {
          this.error = 'Ocurrió un error en los servidores de Marina. Intenta más tarde.';
        }
        this.cdr.detectChanges();
      }
    });
  }
}
