import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home';
import { RegistroComponent } from './pages/registro/registro';
import { LoginComponent } from './pages/login/login';
import { ContactoComponent } from './pages/contacto/contacto';
import { PagoExitoComponent } from './pages/pago-exito/pago-exito';
import { PagoCanceladoComponent } from './pages/pago-cancelado/pago-cancelado';
import { MisPedidosComponent } from './pages/mis-pedidos/mis-pedidos';

export const routes: Routes = [
    { path: '', component: HomeComponent },
    { path: 'registro', component: RegistroComponent },
    { path: 'login', component: LoginComponent },
    { path: 'contacto', component: ContactoComponent },
    { path: 'pago-exito', component: PagoExitoComponent },
    { path: 'pago-cancelado', component: PagoCanceladoComponent },
    { path: 'mis-pedidos', component: MisPedidosComponent },
    { path: '**', redirectTo: '' }
];
