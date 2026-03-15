import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // He sacado toda la lógica de conexión con el backend a este servicio (AuthService)
  // para no mezclar el código visual (HTML/CSS) con las peticiones a Laravel.
  // Así el código queda mucho más limpio y podemos reutilizar este servicio en el Registro.
  private http = inject(HttpClient);
  private apiUrl = 'http://127.0.0.1:8000/api';

  // Inicializamos el estado siempre en FALSE para que el servidor (SSR) y 
  // la primera carga del cliente generen exactamente el mismo HTML (y no se rompa la Hidratación de Angular).
  private loggedInSubject = new BehaviorSubject<boolean>(false);
  public isLoggedIn$ = this.loggedInSubject.asObservable(); // Observable público

  constructor() {
    // Una vez construido, si estamos en el navegador, comprobamos si tenemos token
    // y actualizamos el estado silenciosamente (usamos setTimeout para que ocurra DESPUÉS de hidratar el HTML inicial)
    if (typeof window !== 'undefined') {
      setTimeout(() => {
        if (this.hasToken()) {
          this.loggedInSubject.next(true);
        }
      }, 0);
    }
  }

  // Comprueba si ya hay un token guardado (al refrescar la página)
  // Ojo: Angular intenta renderizar la página en el servidor (SSR) antes de mandarla al navegador.
  // En el servidor, "localStorage" no existe, por eso tenemos que comprobar si typeof window !== 'undefined'.
  private hasToken(): boolean {
    if (typeof window !== 'undefined' && typeof window.localStorage !== 'undefined') {
      return !!localStorage.getItem('token_auth');
    }
    return false;
  }

  // Realizar la petición de Login
  login(credenciales: { email: string; password: string }) {
    return this.http.post<any>(`${this.apiUrl}/login`, credenciales).pipe(
      tap(respuesta => {
        // Al recibir la respuesta, guardamos los datos
        if (typeof window !== 'undefined') {
          localStorage.setItem('token_auth', respuesta.access_token);
          localStorage.setItem('usuario_nombre', respuesta.user.name);
        }
        // Notificamos que el estado ha cambiado a "Conectado"
        this.loggedInSubject.next(true);
      })
    );
  }

  // Realizar la petición de Registro (¡NUEVO!)
  // Igual que en el login, le pasamos los datos y si Laravel dice OK, guardamos el token
  // para que no tenga que iniciar sesión a mano justo después de registrarse. 
  // (¡Es un auto-login!)
  registro(datos: { name: string; email: string; password: string }) {
    return this.http.post<any>(`${this.apiUrl}/register`, datos).pipe(
      tap(respuesta => {
        if (typeof window !== 'undefined') {
          localStorage.setItem('token_auth', respuesta.access_token);
          localStorage.setItem('usuario_nombre', respuesta.user.name);
        }
        this.loggedInSubject.next(true);
      })
    );
  }

  // Para cerrar sesión limpiamos los datos
  logout() {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('token_auth');
      localStorage.removeItem('usuario_nombre');
    }
    this.loggedInSubject.next(false);
  }

  // Obtener nombre del usuario actual
  getUsername(): string | null {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('usuario_nombre');
    }
    return null;
  }
}
