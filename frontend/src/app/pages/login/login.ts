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
  template: `
    <div class="login-wrapper">
      <div class="login-card">
        
        <div class="login-header">
          <h2>🍔 ¡Bienvenido de vuelta!</h2>
          <p>La comida que amas, a un clic de distancia.</p>
        </div>

        @if (error) {
          <div class="error-msg">
            {{ error }}
          </div>
        }
        
        @if (mensajeExito) {
          <div class="success-msg">
            {{ mensajeExito }}
          </div>
        }

        <form (ngSubmit)="iniciarSesion()" class="login-form">
          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" [(ngModel)]="credenciales.email" name="email" 
                   placeholder="tu@email.com" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" [(ngModel)]="credenciales.password" name="password" 
                   placeholder="••••••••" class="form-control" required>
          </div>

          <button type="submit" class="btn-primary w-100 login-btn" [disabled]="cargando">
            @if (cargando) {
              <span class="spinner-inline"></span>
            }
            {{ cargando ? 'Conectando...' : 'Iniciar Sesión' }}
          </button>
        </form>

        <div class="login-footer">
          <p>¿No tienes una cuenta aún? <a href="/registro" class="text-accent">Regístrate gratis</a></p>
        </div>

      </div>
    </div>
  `
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
        } else {
          this.error = 'No pudimos conectar con los servidores de Marina. Intenta más tarde.';
        }
        this.cdr.detectChanges(); 
      }
    });
  }
}
