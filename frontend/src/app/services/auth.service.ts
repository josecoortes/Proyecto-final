import { Injectable, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { tap } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // He sacado toda la lógica de conexión con el backend a este servicio (AuthService)
  // para no mezclar el código visual (HTML/CSS) con las peticiones a Laravel.
  // Así el código queda mucho más limpio y podemos reutilizar este servicio en el Registro.
  private http = inject(HttpClient);
  private apiUrl = environment.apiUrl;

  // Inicializamos el estado siempre en FALSE para que el servidor (SSR) y
  // la primera carga del cliente generen exactamente el mismo HTML (y no se rompa la Hidratación de Angular).
  public isLoggedIn = signal<boolean>(false);
  public userName = signal<string>('Invitado');

  constructor() {
    // Una vez construido, si estamos en el navegador, comprobamos si tenemos token
    // y actualizamos el estado silenciosamente (usamos setTimeout para que ocurra DESPUÉS de hidratar el HTML inicial)
    if (typeof window !== 'undefined') {
      setTimeout(() => {
        if (this.hasToken()) {
          this.isLoggedIn.set(true);
          const name = localStorage.getItem('usuario_nombre');
          if (name) this.userName.set(name);
        }
      }, 0);
    }
  }

  // Comprueba si ya hay un token guardado (al refrescar la página)
  // Angular intenta renderizar la página en el servidor (SSR) antes de mandarla al navegador.
  // En el servidor, "localStorage" no existe, por eso tenemos que comprobar si typeof window !== 'undefined'.
  private hasToken(): boolean {
    if (typeof window !== 'undefined' && typeof window.localStorage !== 'undefined') {
      return !!localStorage.getItem('token_auth');
    }
    return false;
  }

  // Realizar la petición de Login
  login(credenciales: { email: string; password: string }) {
    return this.http.post<any>(`${this.apiUrl}/login`, credenciales, {
      headers: { 'Accept': 'application/json' }
    }).pipe(
      tap(respuesta => {
        // Al recibir la respuesta, guardamos los datos
        if (typeof window !== 'undefined') {
          localStorage.setItem('token_auth', respuesta.access_token);
          localStorage.setItem('usuario_nombre', respuesta.user.name);
          this.userName.set(respuesta.user.name);
        }
        // Notificamos que el estado ha cambiado a "Conectado"
        this.isLoggedIn.set(true);
      })
    );
  }

  // Realizar la petición de Registro
  registro(datos: { name: string; email: string; password: string }) {
    return this.http.post<any>(`${this.apiUrl}/register`, datos, {
      headers: { 'Accept': 'application/json' }
    }).pipe(
      tap(respuesta => {
        if (typeof window !== 'undefined') {
          localStorage.setItem('token_auth', respuesta.access_token);
          localStorage.setItem('usuario_nombre', respuesta.user.name);
          this.userName.set(respuesta.user.name);
        }
        this.isLoggedIn.set(true);
      })
    );
  }

  // Para cerrar sesión limpiamos los datos
  logout() {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('token_auth');
      localStorage.removeItem('usuario_nombre');
    }
    this.userName.set('Invitado');
    this.isLoggedIn.set(false);
  }

  // Obtener nombre del usuario actual
  getUsername(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('usuario_nombre');
    }
    return null;
  }
}
