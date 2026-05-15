# Roig Arena — Sistema de Venta de Entradas

Sistema completo de venta de entradas desarrollado con **Laravel 12 + Sail** como proyecto de fin de módulo de DAW2.

---

## Descripción

Roig Arena es una aplicación web que permite gestionar y vender entradas para eventos (conciertos, partidos, teatro, festivales) en un recinto de gran capacidad. Los usuarios pueden consultar eventos, seleccionar asientos en un mapa interactivo, reservarlos temporalmente y confirmar la compra. Los administradores pueden crear y gestionar eventos y sectores desde un panel dedicado.

---

## Tecnologías

| Tecnología | Uso |
|---|---|
| Laravel | Framework PHP principal |
| Laravel Sail | Entorno Docker para desarrollo |
| MySQL | Base de datos |
| Laravel Sanctum | Autenticación (sesión web + token API) |
| PHPUnit | Tests automatizados |
| Docker | Contenedores |
| CSS puro + JS vanilla | Frontend sin frameworks externos |

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

52 tests automatizados con 100% de éxito:

| Archivo | Tipo | Tests |
|---|---|---|
| AuthTest | Feature | 5 |
| EventoTest | Feature | 6 |
| ReservaTest | Feature | 5 |
| CompraTest | Feature | 4 |
| SectorTest | Feature | 6 |
| AsientoTest | Feature | 3 |
| ExampleTest | Feature | 1 |
| ModeloTest | Unit | 9 |
| ReservaServiceTest | Unit | 5 |
| CompraServiceTest | Unit | 4 |
| LiberarReservasServiceTest | Unit | 3 |
| ExampleTest | Unit | 1 |
| **Total** | | **52** |

---

## Puesta en marcha

### Primera vez (entorno nuevo)

La carpeta `vendor/` debe existir antes de poder usar Sail. Solo hay que hacer esto una vez:

```bash
# 1. Clonar el repositorio
git clone https://github.com/luciferma14/arena2.git
cd arena2

# 2. Copiar las variables de entorno
cp .env.example .env

# 3. Instalar dependencias PHP sin necesitar PHP instalado localmente
docker run --rm \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# 4. Levantar Sail y generar la clave de aplicación
./vendor/bin/sail up -d && ./vendor/bin/sail artisan key:generate && ./vendor/bin/sail down
```

### Uso habitual

Una vez configurado el entorno, basta con ejecutar el script desde la carpeta home:

```bash
bash ~/arena.sh
```

El script reinicia los contenedores, espera a que MySQL esté completamente disponible, ejecuta las migraciones con los seeders y lanza la batería de tests automáticamente.

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
