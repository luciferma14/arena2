# Roig Arena — Sistema de Venta de Entradas

Sistema completo de venta de entradas desarrollado con **Laravel 12 + Sail** como proyecto de fin de módulo de DAW2.

---

## Descripción

Roig Arena es una aplicación web que permite gestionar y vender entradas para eventos (conciertos, partidos, teatro, festivales) en un recinto de gran capacidad. Los usuarios pueden consultar eventos, seleccionar asientos en un mapa interactivo, reservarlos temporalmente y confirmar la compra. Los administradores pueden crear y gestionar eventos y sectores desde un panel dedicado.

---

## Tecnologías

| Tecnología | Versión | Uso |
|---|---|---|
| Laravel | 12 | Framework PHP principal |
| Laravel Sail | Latest | Entorno Docker para desarrollo |
| MySQL | 8.4 | Base de datos |
| Laravel Sanctum | Latest | Autenticación (sesión web + token API) |
| PHPUnit | Latest | Tests automatizados |
| Docker | Latest | Contenedores |
| CSS puro + JS vanilla | — | Frontend sin frameworks externos |

---

## Arquitectura

El proyecto sigue una arquitectura en capas:

```
routes/api.php        → 21 rutas API (públicas, protegidas, admin)
routes/web.php        → Rutas web con controladores Web

Controllers/API/      → Lógica delgada, devuelven JSON
Controllers/Web/      → Llaman a los controladores API en memoria (sin HTTP interno)
Services/             → Lógica de negocio compleja (reservas, compras)
Models/               → Eloquent ORM con relaciones completas
Resources/            → Formateo de respuestas JSON
Middleware/           → IsAdmin para rutas protegidas
```

Los controladores Web nunca hacen llamadas HTTP internas — invocan directamente los controladores API en memoria mediante `app(XController::class)->method()`, evitando deadlocks en entornos de un solo worker PHP-FPM.

---

## Base de Datos

8 tablas con un total de ~15.000 registros de prueba:

| Tabla | Registros | Descripción |
|---|---|---|
| `users` | 4 | Usuarios (1 admin + 3 normales) |
| `sectores` | 71 | Sectores del recinto |
| `asientos` | 14.896 | Asientos por sector |
| `eventos` | 4 | Eventos programados |
| `precios` | 284 | Precio por sector y evento |
| `estado_asientos` | 0 | Reservas activas (se generan en uso) |
| `entradas` | 0 | Entradas vendidas (se generan en uso) |
| `personal_access_tokens` | — | Tokens Sanctum |

---

## Funcionalidades

### Usuario normal
- Registro e inicio de sesión
- Consulta de eventos y sectores disponibles
- Mapa interactivo de asientos por sector
- Reserva de asientos (bloqueados 15 minutos)
- Cancelación de reservas
- Confirmación de compra
- Consulta de entradas con código QR

### Administrador
- Todo lo anterior más:
- Crear, editar y eliminar eventos
- Crear, editar y eliminar sectores
- Panel de administración con tablas de gestión

### Sistema automático
- Comando programado que libera reservas expiradas cada minuto
- Protección contra race condition con `lockForUpdate`
- Transacciones de base de datos para operaciones críticas

---

## Seguridad

- Autenticación con Laravel Sanctum (token Bearer + sesión web)
- Middleware `IsAdmin` para rutas de administración
- Validación en todos los endpoints de la API
- Protección CSRF en todas las peticiones mutables desde el frontend
- Soft deletes en usuarios y eventos
- Hash de contraseñas con bcrypt
- Ocultación de datos sensibles en respuestas JSON

---

## Tests

43 tests automatizados con 100% de éxito:

| Tipo | Tests |
|---|---|
| Feature (AuthTest, EventoTest, ReservaTest, CompraTest, SectorTest, AsientoTest) | 31 |
| Unit (ModeloTest, ReservaServiceTest, CompraServiceTest, LiberarReservasServiceTest) | 12 |
| **Total** | **43** |

---

## Instalación y puesta en marcha

### Requisitos
- Docker Desktop con WSL2
- Git

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/luciferma14/arena2.git
cd arena2

# 2. Copiar el fichero de entorno
cp .env.example .env

# 3. Instalar dependencias PHP (desde el contenedor)
docker run --rm -v $(pwd):/var/www/html -w /var/www/html \
  laravelsail/php83-composer:latest composer install

# 4. Levantar los contenedores
./vendor/bin/sail up -d

# 5. Generar clave de aplicación
./vendor/bin/sail artisan key:generate

# 6. Ejecutar migraciones y seeders
./vendor/bin/sail artisan migrate:fresh --seed

# 7. Iniciar el scheduler (en un terminal aparte)
./vendor/bin/sail artisan schedule:work
```

O usar el script que automatiza los pasos 4-6 más los tests:

```bash
bash ~/arena.sh
```

### Accesos

| Servicio | URL |
|---|---|
| Aplicación web | http://localhost |
| API | http://localhost/api/eventos |
| phpMyAdmin | http://localhost:8080 |

phpMyAdmin: usuario `sail`, contraseña `password`.

---

## Credenciales de prueba

**Administrador:**
- Email: `admin@roigarena.com`
- Contraseña: `admin123`

**Usuarios normales:**
- `juan@example.com` / `password`
- `maria@example.com` / `password`
- `carlos@example.com` / `password`

---

## Estructura del proyecto

```
app/
├── Console/Commands/       # Comando de liberación de reservas
├── Http/
│   ├── Controllers/        # Controladores API
│   │   ├── Auth/
│   │   └── Web/            # Controladores Web (frontend)
│   ├── Middleware/         # IsAdmin
│   └── Resources/          # API Resources (formateo JSON)
├── Models/                 # Modelos Eloquent
└── Services/               # Lógica de negocio

database/
├── migrations/             # 8 migraciones
├── seeders/                # 5 seeders (~15.000 registros)
└── factories/              # 6 factories para tests

public/
├── css/arena.css           # Estilos (tema dark cinema, CSS puro)
└── images/                 # Imágenes de eventos

resources/views/
├── layouts/app.blade.php   # Layout principal con interceptor CSRF
├── home.blade.php
├── eventos/
├── auth/
├── dashboard/
├── entradas/
└── admin/

routes/
├── api.php                 # 21 rutas API
└── web.php                 # Rutas web

tests/
├── Feature/                # 31 tests de integración
└── Unit/                   # 12 tests unitarios
```

---

## Notas de diseño

- **Frontend**: CSS puro con tema *dark cinema* (negro + dorado), sin frameworks externos. JS vanilla con Fetch API.
- **Sin Vite ni npm**: los assets se sirven directamente desde `public/`.
- **CSRF**: interceptor global en el layout que añade `X-CSRF-TOKEN` y `Authorization: Bearer` a todas las peticiones mutables a `/api`.
- **Un solo worker**: los controladores Web llaman a los API controllers en memoria para evitar deadlocks por HTTP interno en PHP-FPM.
