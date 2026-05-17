# 🍔 Burguer Marina

Burguer Marina es una plataforma web integral desarrollada como **proyecto intermodular para 2º DAW**. Su objetivo es digitalizar completamente un restaurante de comida rápida, ofreciendo una experiencia de compra atractiva para los clientes y un panel de gestión logística y financiera avanzado para el equipo de trabajo.

El proyecto sigue una **arquitectura cliente-servidor desacoplada**: un backend en **Laravel 12 (PHP 8.2)** que expone una API REST, y un frontend en **Angular 21** que la consume. El panel de administración está renderizado en servidor con **Blade**. Todo desplegado en **AWS EC2** con contenedores Docker y aprovisionado con **Terraform**.

---

## 🎬 Demostración en Vídeo

Haz clic en la imagen para ver el funcionamiento completo en YouTube:

[![Ver Video Demostrativo - Burguer Marina](https://img.youtube.com/vi/Cvq_8BQGURQ/maxresdefault.jpg)](https://youtu.be/Cvq_8BQGURQ)

---

## 🌐 Acceso en Producción

| Recurso | URL |
|---|---|
| 🍔 Aplicación Web | [burguermarina.duckdns.org](https://burguermarina.duckdns.org) |
| 📖 Documentación API (Swagger) | [burguermarina.duckdns.org/api-docs](https://burguermarina.duckdns.org/api-docs/) |
| 🔧 Panel Administración | [burguermarina.duckdns.org/admin/login](https://burguermarina.duckdns.org/admin/login) |

---

## ✅ Funcionalidades implementadas

- 🔐 **Autenticación robusta** con Laravel Sanctum (tokens Bearer) y contraseñas cifradas (Bcrypt) con reglas estrictas de complejidad.
- 👥 **Sistema de 6 roles jerárquicos**: Cliente, Empleado (Cocinero), Repartidor, Cajero, Gestor y Administrador.
- 🛒 **Carrito de la compra reactivo** (Angular Signals) con persistencia local y cálculo dinámico de precios.
- 💳 **Pasarela de pago Stripe Checkout** integrada: pago con tarjeta o en efectivo, con confirmación segura en backend.
- 📍 **Tracker de pedidos en tiempo real** para el cliente: barra de progreso con estados (`Recibido → Cocina → Listo → Reparto → Entregado`) adaptada al método de entrega (recogida o domicilio).
- 📦 **KDS (Kitchen Display System)** con sistema de pestañas por estado: *Nuevos, En Cocina, Listos, En Reparto, Historial*.
- 🚚 **Lógica de roles en logística**: cajeros gestionan todos los pedidos; repartidores solo ven pedidos a domicilio.
- 💶 **Dashboard financiero**: contabilidad real que solo suma ingresos cuando el pedido está verificado como "Pagado".
- 📖 **Documentación API interactiva** con Swagger UI (OpenAPI 3.0), accesible en `/api-docs`.
- 📱 **Diseño responsive** con menú hamburguesa animado para móvil.
- 🏗️ **Proxy inverso Nginx** para unificar tráfico y enrutar `/api` internamente.

---

## 🏛️ Arquitectura del sistema

```
Internet
    │
    ▼
[ burguermarina.duckdns.org ] (DuckDNS)
    │
    ▼
[ EC2 Frontend (IP Elástica) ]
  ├── Nginx (Puerto 80/443) ← Proxy Inverso + HTTPS
  │     ├── /              → Angular 21 SSR (Puerto 4200)
  │     ├── /api-docs      → Swagger UI (archivo estático)
  │     └── /api, /admin   → Backend EC2 (red privada AWS)
  └── Angular 21 SSR
         │
         │ (Red privada AWS / Route 53 internal DNS)
         ▼
[ EC2 Backend (IP Elástica) ]
  ├── Laravel 12 API (Puerto 8000)
  └── MySQL 8.0 (Puerto 3306)
```

---

## 🧱 Tecnologías utilizadas

### Backend
| Tecnología | Uso |
|---|---|
| **Laravel 12** (PHP 8.2) | Framework principal y API REST |
| **MySQL 8.0** | Base de datos relacional |
| **Laravel Sanctum** | Autenticación con tokens Bearer |
| **Laravel Breeze** | Panel de administración con sesiones Blade |
| **Stripe PHP SDK** | Pasarela de pago integrada |
| **OpenAPI 3.0 / Swagger UI** | Documentación interactiva de la API |

### Frontend
| Tecnología | Uso |
|---|---|
| **Angular 21** (TypeScript) | Framework principal del cliente |
| **Angular SSR** | Server-Side Rendering para SEO y rendimiento |
| **Angular Signals** | Gestión de estado reactivo |
| **Blade (Laravel)** | Panel administrativo renderizado en servidor |
| **CSS3 / Vanilla CSS** | Estilos propios sin dependencias externas |

### Infraestructura y DevOps
| Tecnología | Uso |
|---|---|
| **AWS EC2** | Dos instancias: Frontend y Backend |
| **AWS Elastic IP** | IP fija pública para cada instancia |
| **AWS Route 53** | DNS interno entre instancias |
| **Terraform** | Infraestructura como Código (IaC) |
| **Docker & Docker Compose** | Contenedores de desarrollo y producción |
| **Nginx** | Servidor web y proxy inverso con HTTPS |
| **GitHub Actions** | CI/CD: despliegue automático al hacer push a `main` |
| **DuckDNS** | Dominio público gratuito |

---

## 🗂️ Modelo de Base de Datos

Base de datos relacional construida íntegramente mediante **migraciones de Laravel** (sin PHPMyAdmin ni SQL manual):

| Tabla | Descripción | Campos clave |
|---|---|---|
| `users` | Usuarios del sistema | `name`, `email`, `password`, `rol`, `telefono` |
| `platos` | Carta del restaurante | `nombre`, `descripcion`, `precio`, `imagen`, `categoria_id` |
| `categorias` | Categorías de platos | `nombre` |
| `pedidos` | Pedidos realizados | `user_id`, `estado`, `metodo_entrega`, `metodo_pago`, `estado_pago`, `fecha`, `hora` |
| `pedido_plato` | Tabla pivot (N:M) | `pedido_id`, `plato_id`, `cantidad` |
| `comentarios` | Reseñas del restaurante | `user_id`, `texto`, `valoracion` |
| `gastos` | Gastos del negocio (dashboard) | `concepto`, `importe`, `fecha` |
| `personal_access_tokens` | Tokens Sanctum | `tokenable_id`, `token`, `abilities` |

---

## 🔐 Seguridad

- Los tokens **JWT (Sanctum)** se almacenan en `localStorage` del navegador.
- Las rutas del panel admin están protegidas por **middlewares personalizados** (`is_admin`) que evalúan el rol antes de cargar la vista.
- Las rutas de la API están agrupadas por nivel de acceso (`auth:sanctum`).
- Las contraseñas exigen: **8+ caracteres, mayúsculas, minúsculas, números y símbolos**.
- Los precios se validan en el **backend** al crear la sesión de Stripe (imposible falsificar precios desde el frontend).
- La comunicación entre instancias es por la **red privada de AWS** (el backend no está expuesto a Internet directamente).

---

## 📖 API REST — Documentación

La API sigue el estándar **OpenAPI 3.0** y está documentada con **Swagger UI**:

👉 **[burguermarina.duckdns.org/api-docs](https://burguermarina.duckdns.org/api-docs/)**

### Endpoints principales

| Método | Endpoint | Auth | Descripción |
|---|---|---|---|
| `POST` | `/api/register` | ❌ | Registrar nuevo usuario |
| `POST` | `/api/login` | ❌ | Iniciar sesión → devuelve token |
| `GET` | `/api/platos` | ❌ | Listar la carta completa |
| `GET` | `/api/platos/{id}` | ❌ | Ver detalle de un plato |
| `POST` | `/api/platos` | ✅ | Crear plato (Admin/Empleado) |
| `PUT` | `/api/platos/{id}` | ✅ | Editar plato (Admin/Empleado) |
| `DELETE` | `/api/platos/{id}` | ✅ | Eliminar plato (Admin/Empleado) |
| `GET` | `/api/pedidos` | ✅ | Ver mis pedidos |
| `POST` | `/api/pedidos` | ✅ | Crear pedido (efectivo) |
| `POST` | `/api/crear-sesion-pago` | ✅ | Iniciar pago con Stripe |
| `POST` | `/api/confirmar-pago` | ✅ | Confirmar pago exitoso |
| `GET` | `/api/comentarios` | ❌ | Ver reseñas |
| `POST` | `/api/comentarios` | ✅ | Publicar reseña |

---

## 🚀 Instrucciones de despliegue local

### Requisitos
- Docker y Docker Desktop instalados y corriendo.
- Git.

```bash
# 1. Clona el repositorio
git clone https://github.com/josecoortes/Proyecto-final.git
cd Proyecto-final

# 2. Levanta los contenedores
docker compose up -d

# 3. Entra al contenedor e instala dependencias
docker exec -it entorno_trabajo bash
cd backend && composer install

# 4. Configura el entorno y migra la base de datos
php artisan key:generate
php artisan migrate:fresh --seed

# 5. La app estará en http://localhost:4200 (Angular) y http://localhost:8000 (API)
```

## ☁️ Despliegue en producción (AWS + Terraform)

```bash
# 1. Inicializa y aplica la infraestructura con Terraform
cd terraform
terraform init
terraform apply

# 2. Añade las IPs del output como Secrets en GitHub:
#    EC2_FRONTEND_HOST → IP del Frontend
#    EC2_BACKEND_HOST  → IP del Backend
#    EC2_SSH_KEY       → Contenido de tu clave .pem

# 3. Cualquier push a main dispara el despliegue automático
git push origin main
```

---

## 👨‍💻 Autores

**Nicolás Jiménez & Jose Cortés**
2º DAW — Proyecto Intermodular · 2025/2026
