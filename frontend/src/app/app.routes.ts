import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home';
import { RegistroComponent } from './pages/registro/registro';
import { LoginComponent } from './pages/login/login';

export const routes: Routes = [
    { path: '', component: HomeComponent },
    { path: 'registro', component: RegistroComponent },
    { path: 'login', component: LoginComponent }
];
