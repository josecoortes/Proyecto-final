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

  // Usamos BehaviorSubject (una especie de variable en tiempo real) para que la barra 
  // superior (Navbar) sepa al instante cuando el usuario inicia o cierra sesión sin tener que recargar la página.
  private loggedInSubject = new BehaviorSubject<boolean>(this.hasToken());
  public isLoggedIn$ = this.loggedInSubject.asObservable(); // Observable público

  // Comprueba si ya hay un token guardado (al refrescar la página)
  private hasToken(): boolean {
    return !!localStorage.getItem('token_auth');
  }

  // Realizar la petición de Login
  login(credenciales: { email: string; password: string }) {
    return this.http.post<any>(`${this.apiUrl}/login`, credenciales).pipe(
      tap(respuesta => {
        // Al recibir la respuesta, guardamos los datos
        localStorage.setItem('token_auth', respuesta.access_token);
        localStorage.setItem('usuario_nombre', respuesta.user.name);
        // Notificamos que el estado ha cambiado a "Conectado"
        this.loggedInSubject.next(true);
      })
    );
  }

  // Para cerrar sesión limpiamos los datos
  logout() {
    localStorage.removeItem('token_auth');
    localStorage.removeItem('usuario_nombre');
    this.loggedInSubject.next(false);
  }

  // Obtener nombre del usuario actual
  getUsername(): string | null {
    return localStorage.getItem('usuario_nombre');
  }
}
