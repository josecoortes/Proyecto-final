import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';

@Component({
  selector: 'app-contacto',
  standalone: true,
  imports: [CommonModule],
  template: `
    <section class="contacto-section">
      <div class="contacto-container glass-panel">
        <div class="contacto-info">
          <h1>Contáctanos</h1>
          <p class="subtitle">Estamos aquí para escucharte. Pásate por nuestro local o llámanos.</p>

          <div class="info-grid">
            <div class="info-item">
              <span class="icon">📞</span>
              <div>
                <h3>Teléfono</h3>
                <p>+34 951 123 45678</p> <!-- Número falso con un dígito de más -->
              </div>
            </div>

            <div class="info-item">
              <span class="icon">📍</span>
              <div>
                <h3>Dirección</h3>
                <p>Barriada Los Asperones, Málaga, España</p>
              </div>
            </div>

            <div class="info-item">
              <span class="icon">⏰</span>
              <div>
                <h3>Horario</h3>
                <p>Lunes - Domingo: 12:00 - 00:00</p>
              </div>
            </div>
          </div>
        </div>

        <div class="map-container">
          <iframe
            [src]="mapUrl"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </section>
  `,
  styles: [`
    .contacto-section {
      padding: 80px 20px;
      min-height: calc(100vh - 160px);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .contacto-container {
      width: 100%;
      max-width: 1100px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      padding: 40px;
    }
    @media (max-width: 768px) {
      .contacto-container {
        grid-template-columns: 1fr;
        padding: 20px;
      }
    }
    .contacto-info {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .contacto-info h1 {
      font-size: 3rem;
      margin-bottom: 10px;
      background: linear-gradient(to right, var(--color-primary), var(--color-primary-dark));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .subtitle {
      color: var(--color-text-muted);
      margin-bottom: 40px;
      font-size: 1.1rem;
    }
    .info-grid {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }
    .info-item {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 15px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.05);
      transition: transform 0.3s ease, background 0.3s ease;
    }
    .info-item:hover {
      transform: translateX(10px);
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(227, 0, 15, 0.3);
    }
    .info-item .icon {
      font-size: 1.5rem;
      background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      box-shadow: var(--shadow-glow);
    }
    .info-item h3 {
      margin: 0;
      font-size: 1.2rem;
      color: var(--color-white);
    }
    .info-item p {
      margin: 5px 0 0;
      color: var(--color-text-muted);
    }
    .map-container {
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: var(--shadow-md);
      position: relative;
    }
    .map-container::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      box-shadow: inset 0 0 20px rgba(0,0,0,0.5);
    }
  `]
})

// Mapa de google con la ubicacion
export class ContactoComponent {
  mapUrl: SafeResourceUrl;

  constructor(private sanitizer: DomSanitizer) {
    const url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3198.5!2d-4.50128!3d36.7207!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzbCsDQzJzE0LjYiTiA0wrAzMCcwNC42Ilc!5e0!3m2!1ses!2ses!4v1714920000000!5m2!1ses!2ses';
    const embedUrl = `https://maps.google.com/maps?q=36.7207269,-4.5012847&z=15&output=embed`;
    this.mapUrl = this.sanitizer.bypassSecurityTrustResourceUrl(embedUrl);
  }
}
