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
  template: `
    <div class="login-wrapper">
      <div class="login-card">
        
        <div class="login-header">
          <h2>🍔 ¡Únete a la familia!</h2>
          <p>Crea tu cuenta y empieza a pedir tus favoritas.</p>
        </div>

        <div *ngIf="error" class="error-msg">
          {{ error }}
        </div>
        
        <div *ngIf="mensajeExito" class="success-msg">
          {{ mensajeExito }}
        </div>

        <form (ngSubmit)="registrarse()" class="login-form">
          <div class="form-group">
            <label for="name">Nombre Completo</label>
            <input type="text" id="name" [(ngModel)]="datos.name" name="name" 
                   placeholder="Ej: Carlos Hamburguesas" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" [(ngModel)]="datos.email" name="email" 
                   placeholder="tu@email.com" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="password">Contraseña (Mín. 8 caracteres)</label>
            <input type="password" id="password" [(ngModel)]="datos.password" name="password" 
                   placeholder="••••••••" class="form-control" required minlength="8">
          </div>

          <button type="submit" class="btn-primary w-100 login-btn" [disabled]="cargando">
            <span *ngIf="cargando" class="spinner-inline"></span>
            {{ cargando ? 'Creando cuenta...' : 'Registrarse' }}
          </button>
        </form>

        <div class="login-footer">
          <p>¿Ya tienes una cuenta? <a href="/login" class="text-accent">Inicia sesión aquí</a></p>
        </div>

      </div>
    </div>
  `
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
