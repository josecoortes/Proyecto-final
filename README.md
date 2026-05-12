# 🍔 Burguer Marina

Burguer Marina es una plataforma web integral desarrollada como proyecto intermodular para **2º DAW**. Su objetivo es digitalizar completamente un restaurante de comida rápida, ofreciendo una experiencia de compra atractiva para los clientes y un panel de gestión logística y financiera avanzado para los empleados.

El proyecto está desarrollado con una **arquitectura de microservicios en dos instancias AWS separadas**, utilizando **Laravel 11 (PHP)** en el backend como API REST, **MySQL** como base de datos, y una **interfaz dinámica construida en Angular 21** para los clientes, junto con un panel administrativo renderizado con **Blade**. Todo ello orquestado bajo contenedores **Docker** y aprovisionado automáticamente con **Terraform**.

---

## ✅ Funcionalidades implementadas

- 🔐 Autenticación robusta con **Laravel Sanctum (JWT)** y contraseñas cifradas (Bcrypt).
- 👥 **Sistema de roles jerárquicos y estancos**: Cliente, Empleado (Cocinero), Repartidor, Cajero, Gestor y Administrador.
- 🛒 **Carrito de la compra reactivo** (Angular Signals) con persistencia local y cálculo dinámico de precios.
- 💳 **Simulación de pasarela de pago** con opciones de entrega a domicilio o recogida en local.
- 📦 **Panel de logística inteligente**: Los repartidores solo ven pedidos a domicilio y los cajeros solo recogidas en local.
- 💶 **Gestor de estados y cobros**: Botones de confirmación de cobro en efectivo y estados de preparación.
- 📈 **Dashboard financiero estricto**: Contabilidad real que solo suma ingresos cuando el pedido está verificado como "Pagado".
- 🏗️ **Proxy inverso con Nginx** para unificar el tráfico del Frontend y enrutar las peticiones `/api` al Backend de forma interna.
- ☁️ **Arquitectura de dos instancias EC2 en AWS**: Frontend y Backend en máquinas separadas, comunicadas a través de una zona DNS privada de Route 53.
- 🏭 **Infraestructura como Código (IaC)** con **Terraform**: aprovisionamiento automático de instancias, grupos de seguridad, IPs elásticas y DNS interno.
- 🚀 **Integración y Despliegue Continuo (CI/CD)** con **GitHub Actions**: despliegue paralelo y automatizado en ambas instancias al hacer `push` a `main`.
- 🌐 Acceso público a través de dominio personalizado con **DuckDNS** (`burguermarina.duckdns.org`).

---

## 🎬 Demostración en Vídeo

Haz clic en la imagen a continuación para ver el funcionamiento completo de la plataforma en YouTube:

[![Ver Video Demostrativo - Burguer Marina](https://img.youtube.com/vi/Cvq_8BQGURQ/maxresdefault.jpg)](https://youtu.be/Cvq_8BQGURQ)

---

## 🏛️ Arquitectura del sistema

```
Internet
    │
    ▼
[ burguermarina.duckdns.org ]
    │
    ▼
[ EC2 Frontend (IP Elástica) ]
  ├── Nginx (Puerto 80) ← Proxy Inverso
  │     ├── /          → Angular 21 SSR (Puerto 4200)
  │     └── /api, /admin, /sanctum... → Backend (DNS interno)
  └── Angular 21 SSR
         │
         │ (Red privada AWS / Route 53 internal DNS)
         ▼
[ EC2 Backend (IP Elástica) ]
  ├── Laravel 11 API (Puerto 8000)
  └── MySQL 8 (Puerto 3306)
```

---

## 🚧 Próximas mejoras planificadas

- Generación automática de facturas en PDF.
- Integración real con la pasarela de pagos Stripe.
- Control avanzado de stock de ingredientes (que oculte platos sin existencias).
- Activar HTTPS con certificado SSL/TLS gratuito mediante **Let's Encrypt / Certbot**.

---

## 🧱 Tecnologías utilizadas

### Backend & Infraestructura
- **Laravel 11** (PHP 8.2)
- **MySQL 8.0**
- **Docker** y Docker Compose
- **Nginx** (Proxy Inverso)
- **Terraform** (IaC — Infraestructura como Código)
- **AWS EC2**, **AWS Route 53**, **AWS Elastic IP**
- **GitHub Actions** (CI/CD)
- Autenticación: JWT (Laravel Sanctum)

### Frontend
- **Angular 21** (TypeScript) — Web del cliente
- **Angular SSR** (Server Side Rendering)
- **Laravel Blade** — Panel Administrativo
- HTML5 · CSS3 · **Tailwind CSS**
- Signals (Manejo de estado reactivo en Angular)

### Herramientas de despliegue
- **DuckDNS** (Dominio público gratuito)
- **Docker Compose** (Orquestación de contenedores)
- **GitHub Actions** (Automatización CI/CD)

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
- La comunicación entre instancias se realiza a través de la **red privada interna de AWS**, sin exponer el Backend directamente a Internet.

---

## 🚀 Instrucciones de despliegue

### Despliegue local (desarrollo)

#### Requisitos
- Docker y Docker Desktop instalados y corriendo.
- Git.

```bash
# 1. Clona el repositorio
git clone https://github.com/josecoortes/Proyecto-final.git

# 2. Levanta los contenedores con Docker
docker compose up -d

# 3. Entra al contenedor del backend e instala dependencias
docker exec -it entorno_trabajo bash
cd backend && composer install
npm install && npm run build

# 4. Genera la clave de Laravel y migra la base de datos con datos de prueba
php artisan key:generate
php artisan migrate:fresh --seed

# 5. La aplicación estará corriendo en http://localhost (Nginx)
```

### Despliegue en producción (AWS con Terraform)

#### Requisitos
- Terraform instalado.
- Credenciales de AWS configuradas en la terminal.
- Clave SSH de AWS (`vockey.pem`) disponible.

```bash
# 1. Entra en la carpeta de Terraform
cd terraform

# 2. Inicializa y aplica la infraestructura
terraform init
terraform apply

# 3. Copia las IPs públicas del output y añádelas como Secrets en GitHub:
#    EC2_FRONTEND_HOST → IP del Frontend
#    EC2_BACKEND_HOST  → IP del Backend
#    EC2_SSH_KEY       → Contenido de tu clave .pem

# 4. Haz un git push a main y GitHub Actions desplegará automáticamente
git push origin main
```

---

## 👨‍💻 Autores
**Nicolás Jiménez & Jose Cortés**  
2º DAW — Proyecto Intermodular · 2025/2026
