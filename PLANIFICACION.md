# 🍔 Plan de Proyecto: Burger Marina

Este documento fusiona la propuesta original del equipo con los requisitos técnicos obligatorios de los módulos (DAWEC, DAWES, DAWEB, DIW, etc.).

## 1. Resumen Ejecutivo
**Burger Marina** es una plataforma web integral para la gestión de pedidos y reservas de una hamburguesería.
*   **Modelo:** Kiosko/Restaurante con pedidos online (Delivery/Pickup) y panel de administración.
*   **Arquitectura Híbrida (Requisito DAWES):**
    *   **Panel Administración:** Web tradicional renderizada en servidor (Laravel Blade).
    *   **Parte Pública (Clientes):** SPA (Angular) consumiendo API REST.
*   **Gestión:** Metodología Ágil (Scrum) con Sprints de 2 semanas.

---

## 2. Stack Tecnológico (Definitivo)

| Capa | Tecnología Seleccionada | Justificación (Requisitos) |
| :--- | :--- | :--- |
| **Frontend Clientes** | **Angular** (CLI v20+) + TypeScript | Requisito DAWEC (API First) |
| **Backend / Admin** | **Laravel 12** + **Blade** | Requisito DAWES (Admin en Blade) |
| **API Security** | **Laravel Sanctum** | Requisito DAWES (Tokens para clientes) |
| **Base de Datos** | **MySQL** (Docker) | Migraciones y Seeders obligatorios |
| **Despliegue** | **AWS** (EC2 + RDS/S3) + **Terraform** | Requisito DAWEB (IaC + AWS) |
| **Documentación** | **Swagger / OpenAPI** | Requisito DAWES (Docs API) |

---

## 3. Hoja de Ruta (Roadmap) y Sprints

### ✅ Sprint 1: Diseño y Prototipado (Completado)
*   [x] Identidad Visual (Moodboard, Colores #E63946 / #F9A825).
*   [x] Prototipado en Figma (Landing, Carta, Admin).
*   [x] Maquetación base HTML/CSS (DIW).

### 🔄 Sprint 2: Backend & Arquitectura Híbrida (Foco: DAWES)
*   **Base de Datos Segura:**
    *   [ ] Esquema E/R (Usuarios, Pedidos, Platos, Comentarios).
    *   [ ] **Migraciones obligatorias** (Prohibido crear tablas a mano).
    *   [ ] **Seeders y Factories** para datos de prueba.
*   **Perfil Administrador (Blade):**
    *   [ ] Login tradicional (Session Cookies) con Laravel Breeze/Filament.
    *   [ ] CRUD de Platos y Pedidos usando Vistas Blade.
*   **API REST (Para Usuarios):**
    *   [ ] Endpoints públicos: `GET /api/platos`.
    *   [ ] Autenticación Tokens: **Laravel Sanctum** para SPA Angular.
    *   [ ] Documentación de API: **Swagger**.

### 📅 Sprint 3: Frontend Angular (Foco: DAWEC)
*   [ ] **Arquitectura Angular:**
    *   Generar proyecto con `Angular CLI 20`.
    *   Consumo de la API REST creada en Sprint 2.
*   [ ] **Funcionalidad Cliente:**
    *   Landing pública y Carta Digital.
    *   Carrito de Compra (State Management).
    *   Login de Usuario (Consumiendo `/api/login` de Sanctum).

### 🚀 Sprint 4: Infraestructura y Automatización (Foco: DAWEB)
*   [ ] **Terraform (IaC):**
    *   Definir EC2 (Backend Laravel) y S3 (Frontend Angular construida).
*   [ ] **Despliegue AWS:**
    *   HTTPS obligatorio.
    *   Pipeline CI/CD.

---

## 4. Matriz de Cumplimiento de Requisitos

### DAWES (Servidor - Requisitos Estrictos)
*   [ ] **Laravel 12**: Framework base.
*   [ ] **Doble Perfil**:
    *   **Admin**: Usa Blade/Filament (Sesiones).
    *   **Usuario**: Usa API REST (Tokens Sanctum).
*   [ ] **DB**: Todo vía Migraciones/Factories (0 SQL manual).
*   [ ] **Docs**: Swagger implementado.

### DAWEC (Cliente)
*   [ ] **Angular CLI 20 LTS**: Framework frontend.
*   [ ] **Consumo API**: Conexión con Backend Laravel.
*   [ ] **Tests**: Unitarios incluidos.

### DAWEB (Despliegue)
*   [ ] **AWS + Terraform**: Infraestructura como código.
*   [ ] **HTTPS**: Certificados SSL.

---

## 5. Próximos Pasos Inmediatos
1.  **Refactorización Backend:** Instalar Laravel Breeze/Filament para el panel de administración (Blade).
2.  **API Docs:** Instalar Swagger (`l5-swagger`) en Laravel.
3.  **Seguridad:** Configurar Sanctum para separar auth de web (cookies) y api (tokens).
