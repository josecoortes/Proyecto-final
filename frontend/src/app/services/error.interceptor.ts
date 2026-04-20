import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError } from 'rxjs/operators';
import { throwError } from 'rxjs';

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req).pipe(
    catchError((error) => {
      let errorMsg = '';
      if (error.error instanceof ErrorEvent) {
        // Error del lado del cliente
        errorMsg = `Error: ${error.error.message}`;
      } else {
        // Error del lado del servidor
        errorMsg = `Error Code: ${error.status}\nMessage: ${error.message}`;
        if (error.status === 401) {
          // Token expirado o no autorizado
          console.error("No autorizado, redirigiendo al login...");
          // Opcional: inject(Router).navigate(['/login']);
        }
      }
      console.error(errorMsg);
      // Aquí se podría integrar un ToastService en un futuro para mostrar el error visualmente
      // alert("Oh no, algo salió mal con la conexión. Intenta de nuevo.");
      
      return throwError(() => new Error(errorMsg));
    })
  );
};
