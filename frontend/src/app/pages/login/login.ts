import { Component, inject } from '@angular/core';
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

        <div *ngIf="error" class="error-msg">
          {{ error }}
        </div>

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
  // Ojo: He quitado el HttpClient de aquí. Ahora inyectamos el AuthService que he creado
  // para que él se encargue de hablar con Laravel. El componente solo pinta la vista.
  private authService = inject(AuthService);
  private router = inject(Router);

  credenciales = { email: '', password: '' };
  error = '';
  cargando = false;

  iniciarSesion() {
    this.cargando = true;
    this.error = '';

    // Llamamos a la función login de NUESTRO servicio, pasándole los datos del formulario.
    this.authService.login(this.credenciales).subscribe({
      next: (respuesta) => {
        // Tuvimos éxito. El auth.service.ts ya se ha encargado de guardar el token en el LocalStorage
        this.cargando = false;
        alert('¡Bienvenido de nuevo, ' + respuesta.user.name + '!');
        this.router.navigate(['/']); // Redirigir al Inicio
      },
      error: (err) => {
        this.cargando = false;
        console.error('Error de login:', err);
        // Personalizar el mensaje si el usuario/contraseña no existe
        if (err.status === 401) {
          this.error = 'Correo o contraseña incorrectos.';
        } else {
          this.error = 'No pudimos conectar con los servidores de Marina. Intenta más tarde.';
        }
      }
    });
  }
}
