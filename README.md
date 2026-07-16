# Tienda Virtual

Aplicación de comercio electrónico (e-commerce) construida con **Laravel 13** y **Vue 3** mediante **Inertia.js**, con pasarela de pagos integrada de **MercadoPago**. Incluye una tienda pública completa y un panel de administración para gestionar el catálogo, pedidos, cupones y reseñas.

## Características

### Tienda pública
- Página de inicio con productos destacados.
- Catálogo de productos con detalle, imágenes y variantes.
- Navegación por categorías y buscador.
- Carrito de compras funcional para invitados y usuarios autenticados.
- Aplicación de cupones de descuento.
- Lista de deseos (wishlist).
- Reseñas y calificaciones de productos.
- Gestión de direcciones de envío.

### Checkout y pedidos
- Proceso de checkout con pago vía **MercadoPago**.
- Confirmación de pago mediante webhook.
- Historial de pedidos del cliente ("Mis pedidos").

### Panel de administración (`/admin`)
- Dashboard con métricas.
- Gestión de productos y variantes (crear, editar, eliminar).
- Gestión de categorías.
- Gestión de pedidos y actualización de estado.
- Gestión de cupones de descuento.
- Moderación de reseñas (aprobar / eliminar).

## Stack tecnológico

| Capa        | Tecnología                                   |
|-------------|----------------------------------------------|
| Backend     | Laravel 13 (PHP 8.3+)                         |
| Frontend    | Vue 3 + Inertia.js                           |
| Estilos     | Tailwind CSS 4                               |
| Build       | Vite 8                                       |
| Autenticación | Laravel Breeze + Sanctum                   |
| Estado (cliente) | Pinia                                   |
| Pagos       | MercadoPago (`mercadopago/dx-php`)           |
| Rutas JS    | Ziggy                                        |

## Requisitos previos

- PHP >= 8.3
- Composer
- Node.js y npm
- Una base de datos (MySQL/MariaDB, o SQLite)
- Credenciales de MercadoPago (para el checkout)

## Instalación

1. Clona el repositorio e instala las dependencias:

```bash
git clone <url-del-repositorio>
cd tienda-virtual-main
composer install
npm install
```

2. Copia el archivo de entorno y genera la clave de la aplicación:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configura la base de datos y las credenciales de MercadoPago en el archivo `.env` (ver sección [Variables de entorno](#variables-de-entorno)).

4. Ejecuta las migraciones y los seeders (carga datos de ejemplo: usuarios, categorías, productos y cupones):

```bash
php artisan migrate --seed
```

5. Compila los assets del frontend:

```bash
npm run build
```

> Alternativamente, puedes ejecutar el script de instalación completo con `composer setup`.

## Variables de entorno

Además de la configuración estándar de Laravel, esta aplicación utiliza las siguientes variables para MercadoPago:

```env
MP_ACCESS_TOKEN=tu-access-token
MP_PUBLIC_KEY=tu-public-key
MP_WEBHOOK_SECRET=tu-webhook-secret
MP_SANDBOX=true
```

Configuración de la base de datos (ejemplo MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda_virtual
DB_USERNAME=root
DB_PASSWORD=
```

## Ejecución sin base de datos (modo demo)

La tienda pública puede ejecutarse como **SPA independiente** con Vue Router y datos estáticos en JSON, sin PHP, Laravel ni base de datos.

```bash
cd tienda-virtual
npm install
npm run dev
```

Abrí `http://localhost:5173` en el navegador.

### Datos de ejemplo

Los archivos JSON están en `public/data/`:

| Archivo           | Contenido                          |
|-------------------|------------------------------------|
| `categories.json` | Categorías y subcategorías         |
| `products.json`   | Productos, variantes y reseñas     |
| `coupons.json`    | Cupones de descuento               |

Podés editar estos archivos para cambiar el catálogo sin tocar código.

### Rutas disponibles (Vue Router)

| Ruta                    | Página              |
|-------------------------|---------------------|
| `/`                     | Inicio              |
| `/productos`            | Catálogo            |
| `/productos/:slug`      | Detalle de producto |
| `/categoria/:slug`      | Categoría           |
| `/buscar?q=...`         | Búsqueda            |
| `/carrito`              | Carrito             |

El carrito se guarda en `localStorage`. Cupones de prueba: `BIENVENIDA10`, `ENVIOGRATIS`.

### Build para producción

```bash
npm run build
npm run preview
```

---

## Ejecución con Laravel (backend completo)

Para checkout, autenticación, admin y MercadoPago, seguí usando el backend Laravel:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
composer dev
```

La aplicación estará disponible en `http://localhost:8000`.

> **Nota:** El frontend de la tienda ahora usa Vue Router y JSON. Las rutas Laravel en `routes/web.php` ya no sirven la tienda pública; el modo recomendado para ver el catálogo es `npm run dev`.

## Ejecución en desarrollo (legacy)

Para levantar el servidor, la cola, los logs y Vite simultáneamente:

```bash
composer dev
```

O bien de forma manual en terminales separadas:

```bash
php artisan serve
npm run dev
```

La aplicación estará disponible en `http://localhost:8000`.

## Usuarios de prueba

Los seeders crean dos cuentas de ejemplo:

| Rol           | Email               | Contraseña |
|---------------|---------------------|------------|
| Administrador | `admin@tienda.com`  | `password` |
| Cliente       | `cliente@tienda.com`| `password` |

El panel de administración es accesible en `/admin` con la cuenta de administrador.

## Pruebas

```bash
php artisan test
```

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── Store/         # Controladores de la tienda pública (Home, Product, Category, Search)
│   ├── Admin/         # Controladores del panel de administración
│   └── ...            # Cart, Checkout, Order, Wishlist, Review, Address
├── Models/            # Modelos Eloquent (Product, Order, Cart, Coupon, etc.)
resources/js/
├── Pages/             # Vistas Vue organizadas por sección (Store, Admin, Account, Auth...)
├── Layouts/           # Layouts (StoreLayout, AdminLayout, AuthenticatedLayout...)
└── Components/        # Componentes reutilizables
routes/
├── web.php            # Rutas de la tienda y del área de cliente
├── admin.php          # Rutas del panel de administración
├── auth.php           # Rutas de autenticación (Breeze)
└── webhooks.php       # Webhook de MercadoPago
database/
├── migrations/        # Esquema de la base de datos
└── seeders/           # Datos de ejemplo
```

## Licencia

Este proyecto se distribuye bajo la licencia [MIT](https://opensource.org/licenses/MIT).