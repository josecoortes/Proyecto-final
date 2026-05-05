# 🍔 Burguer Marina

Burguer Marina es una plataforma web integral desarrollada como proyecto intermodular para **2º DAW**. Su objetivo es digitalizar completamente un restaurante de comida rápida, ofreciendo una experiencia de compra atractiva para los clientes y un panel de gestión logística y financiera avanzado para los empleados.

El proyecto está desarrollado con una **arquitectura cliente-servidor**, utilizando **Laravel 11 (PHP)** en el backend como API REST, **MySQL** como base de datos, y una **interfaz dinámica construida en Angular 17** para los clientes, junto con un panel administrativo renderizado con **Blade**. Todo ello orquestado bajo contenedores **Docker**.

---

## ✅ Funcionalidades implementadas

- 🔐 Autenticación robusta con **Laravel Sanctum (JWT)** y contraseñas cifradas (Bcrypt).
- 👥 **Sistema de roles jerárquicos y estancos**: Cliente, Empleado (Cocinero), Repartidor, Cajero, Gestor y Administrador.
- 🛒 **Carrito de la compra reactivo** (Angular Signals) con persistencia local y cálculo dinámico de precios.
- 💳 **Simulación de pasarela de pago** con opciones de entrega a domicilio o recogida en local.
- 📦 **Panel de logística inteligente**: Los repartidores solo ven pedidos a domicilio y los cajeros solo recogidas en local.
- 💶 **Gestor de estados y cobros**: Botones de confirmación de cobro en efectivo y estados de preparación.
- 📈 **Dashboard financiero estricto**: Contabilidad real que solo suma ingresos cuando el pedido está verificado como "Pagado".
- 🏗️ Proxy inverso con **Nginx** para unificar puertos de Frontend y Backend y facilitar el despliegue.
- ☁️ Almacenamiento de imágenes optimizado preparado para **AWS**.
- 🚀 **Integración y Despliegue Continuo (CI/CD)** configurado con GitHub Actions.

---

## 🎬 Demostración en Vídeo

Haz clic en la imagen a continuación para ver el funcionamiento completo de la plataforma en YouTube:

[![Ver Video Demostrativo - Burguer Marina](https://img.youtube.com/vi/Cvq_8BQGURQ/maxresdefault.jpg)](https://youtu.be/Cvq_8BQGURQ)

---

## 🚧 Próximas mejoras planificadas

- Generación automática de facturas en PDF.
- Integración real con la pasarela de pagos Stripe.
- Control avanzado de stock de ingredientes (que oculte platos sin existencias).
- Despliegue completo en entorno nube (AWS EC2 / S3).

---

## 🧱 Tecnologías utilizadas

### Backend & Infraestructura
- **Laravel 11** (PHP 8)
- **MySQL** (MariaDB)
- **Docker** y Docker Compose
- **Nginx** (Proxy Inverso)
- **GitHub Actions** (CI/CD)
- Autenticación: JWT (Laravel Sanctum)

### Frontend
- **Angular 17** (TypeScript) — Web del cliente
- **Laravel Blade** — Panel Administrativo
- HTML5 · CSS3 · **Tailwind CSS**
- Signals (Manejo de estado en Angular)

---

## 🗂️ Base de datos

Base de datos relacional enfocada a escalabilidad:

| Tabla | Campos principales |
|---|---|
| `users` | nombre, email, password, rol (admin, gestor, repartidor, cajero, empleado, cliente) |
| `platos` | nombre, descripción, precio, imagen, categoría |
| `pedidos` | fecha, hora, metodo_entrega, direccion, estado, metodo_pago, estado_pago |
| `pedido_plato` | (Tabla Pivot) pedido_id, plato_id, cantidad |

---

## 🔐 Autenticación y seguridad

- El registro y login desde Angular devuelven un **Token JWT** guardado en el LocalStorage.
- Todas las rutas del panel `admin/` en Blade están protegidas mediante **Middlewares personalizados** de Laravel que evalúan el rol del usuario antes de cargar la vista.
- Un Empleado no puede forzar la URL para acceder a Finanzas, Laravel lo intercepta y bloquea (403).

---

## 🚀 Instrucciones de despliegue local

### Requisitos
- Docker y Docker Desktop instalados y corriendo.
- Git.

### Pasos

```bash
# 1. Clona el repositorio
git clone https://github.com/josecoortes/Proyecto-final.git

# 2. Levanta los contenedores con Docker
docker-compose up -d

# 3. Entra al contenedor del backend e instala dependencias
docker exec -it entorno_trabajo bash
composer install
npm install

# 4. Genera la clave de Laravel y migra la base de datos con datos de prueba
php artisan key:generate
php artisan migrate:fresh --seed

# 5. La aplicación estará corriendo bajo los puertos 8000 y 4200.
```

---

## 👨‍💻 Autores
**Nicolás Jiménez & Jose Cortés**  
2º DAW — Proyecto Intermodular · 2025/2026
