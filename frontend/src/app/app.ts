import { Component } from '@angular/core';
import { RouterOutlet, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink], 
  template: `
    <div style="font-family: sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px;">
      
      <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <h1 style="color: #d35400; margin: 0;">🍔 FoodDelivery</h1>
        
        <nav>
          <a routerLink="/" style="margin-right: 20px; text-decoration: none; color: #333; font-weight: bold; cursor: pointer;">Inicio</a>
          <a routerLink="/registro" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; cursor: pointer;">Registrarse</a>
        </nav>
      </header>

      <router-outlet></router-outlet>

    </div>
  `,
  styles: []
})
export class App {}