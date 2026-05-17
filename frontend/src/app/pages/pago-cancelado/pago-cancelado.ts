import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-pago-cancelado',
  standalone: true,
  imports: [CommonModule, RouterModule],
  template: `
    <div class="pago-cancelado-container glass-panel">
      <div class="icono-cancelado">❌</div>
      <h1>Pago Cancelado</h1>
      <p class="subtitulo">Has cancelado el proceso de pago seguro de Stripe.</p>
      
      <div class="detalles-info">
        <p>No te preocupes, <strong>no se ha realizado ningún cargo</strong> en tu tarjeta.</p>
        <p>Tu carrito sigue intacto por si quieres volver a intentarlo o añadir más cosas.</p>
      </div>

      <button routerLink="/" class="btn-primary mt-4">Volver a la tienda</button>
    </div>
  `,
  styles: [`
    .pago-cancelado-container {
      max-width: 600px;
      margin: 80px auto;
      padding: 50px;
      text-align: center;
      background: var(--color-surface);
      border-color: rgba(255, 82, 82, 0.2) !important;
    }
    .icono-cancelado {
      font-size: 5rem;
      margin-bottom: 20px;
      animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }
    h1 { color: #ff5252; font-size: 2.5rem; }
    .subtitulo { color: var(--color-text-muted); font-size: 1.1rem; margin-bottom: 30px; }
    .detalles-info {
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 12px;
      margin: 20px 0;
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: var(--color-text-main);
    }
    .mt-4 { margin-top: 30px; }

    @keyframes shake {
      10%, 90% { transform: translate3d(-1px, 0, 0); }
      20%, 80% { transform: translate3d(2px, 0, 0); }
      30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
      40%, 60% { transform: translate3d(4px, 0, 0); }
    }
  `]
})
export class PagoCanceladoComponent {
}
