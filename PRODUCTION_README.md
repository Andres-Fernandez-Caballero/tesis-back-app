# Guía de Despliegue en Producción

Esta guía cubre el despliegue completo de la aplicación en un servidor de producción usando Docker y Apache.

---

## Requisitos del servidor

- Docker >= 24
- Docker Compose >= 2.20
- Git
- 2 GB RAM mínimo recomendado
- Puertos disponibles: `80`, `3306`, `1025`, `8025`, `9000`

---

## Estructura de archivos relevante

```
.
├── Dockerfile                   # Imagen de producción (PHP 8.3 + Apache)
├── docker-compose.prod.yml      # Stack completo de producción
├── docker/
│   └── apache/
│       └── 000-default.conf     # Virtual host de Apache
└── .env                         # Variables de entorno (NO subir a git)
```

---

## Primer despliegue

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> tesis-back
cd tesis-back
```

### 2. Crear el archivo `.env`

Copiar el ejemplo y ajustar los valores para producción:

```bash
cp .env.example .env
```

Editar `.env` con los valores reales:

```env
APP_NAME=BodyFix
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<ip-o-dominio-del-servidor>

WWWUSER=1000
WWWGROUP=1000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=bodyfix
DB_USERNAME=<usuario_db>
DB_PASSWORD=<contraseña_segura>

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@tudominio.com"
MAIL_FROM_NAME="BodyFix"

VAPID_PUBLIC_KEY=<clave_publica_vapid>
VAPID_PRIVATE_KEY=<clave_privada_vapid>
VAPID_ADMIN_EMAIL="admin@tudominio.com"
```

> **Importante:** nunca subas el `.env` al repositorio. Está incluido en `.gitignore`.

### 3. Generar la APP_KEY

```bash
# Opción A: si tenés PHP instalado en el host
php artisan key:generate --show
# Copiar el resultado y pegarlo en APP_KEY= del .env

# Opción B: con Docker directamente
docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

### 4. Construir y levantar los servicios

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

La primera vez tarda varios minutos porque descarga las imágenes base y compila las dependencias de Composer.

### 5. Ejecutar migraciones y optimizaciones

```bash
# Correr migraciones
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Optimizar configuración, rutas y vistas para producción
docker compose -f docker-compose.prod.yml exec app php artisan optimize

# Crear el enlace simbólico para storage público (si se usa)
docker compose -f docker-compose.prod.yml exec app php artisan storage:link
```

La aplicación ya está disponible en `http://<ip-del-servidor>`.

---

## Servicios disponibles

| Servicio    | URL / Puerto                          | Descripción                          |
|-------------|---------------------------------------|--------------------------------------|
| App Laravel | `http://<servidor>:80`                | API / aplicación principal (Apache)  |
| MySQL       | `<servidor>:3306`                     | Base de datos                        |
| phpMyAdmin  | `http://<servidor>:9000`              | Administración de la base de datos   |
| Mailpit UI  | `http://<servidor>:8025`              | Bandeja de entrada de correos de prueba |

---

## Actualizar la aplicación (re-deploy)

```bash
# 1. Obtener los últimos cambios
git pull origin main

# 2. Reconstruir la imagen e reiniciar el contenedor de la app
docker compose -f docker-compose.prod.yml up -d --build app

# 3. Aplicar migraciones nuevas (si las hay)
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

# 4. Limpiar y regenerar cachés
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan optimize
```

> MySQL, phpMyAdmin y Mailpit no se reconstruyen, solo el contenedor `app`.

---

## Comandos útiles

### Ver estado de los servicios

```bash
docker compose -f docker-compose.prod.yml ps
```

### Ver logs en tiempo real

```bash
# Todos los servicios
docker compose -f docker-compose.prod.yml logs -f

# Solo la aplicación
docker compose -f docker-compose.prod.yml logs -f app

# Solo MySQL
docker compose -f docker-compose.prod.yml logs -f mysql
```

### Acceder al contenedor de la app

```bash
docker compose -f docker-compose.prod.yml exec app bash
```

### Ejecutar comandos Artisan

```bash
docker compose -f docker-compose.prod.yml exec app php artisan <comando>

# Ejemplos:
docker compose -f docker-compose.prod.yml exec app php artisan queue:work
docker compose -f docker-compose.prod.yml exec app php artisan tinker
docker compose -f docker-compose.prod.yml exec app php artisan about
```

### Detener todos los servicios

```bash
docker compose -f docker-compose.prod.yml down
```

### Detener y eliminar volúmenes (⚠️ borra la base de datos)

```bash
docker compose -f docker-compose.prod.yml down -v
```

---

## Backup de la base de datos

### Crear un dump

```bash
docker compose -f docker-compose.prod.yml exec mysql \
  mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar un dump

```bash
docker compose -f docker-compose.prod.yml exec -T mysql \
  mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < backup.sql
```

---

## Modo mantenimiento

```bash
# Activar
docker compose -f docker-compose.prod.yml exec app php artisan down

# Desactivar
docker compose -f docker-compose.prod.yml exec app php artisan up
```

---

## Solución de problemas frecuentes

### La app muestra error 500
Revisar los logs de Laravel:
```bash
docker compose -f docker-compose.prod.yml exec app cat storage/logs/laravel.log
```

### Error de permisos en storage
```bash
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

### MySQL tarda en arrancar y la app falla al conectar
El servicio `app` espera a que MySQL pase su healthcheck antes de iniciarse. Si aun así falla, esperá unos segundos y reiniciá solo la app:
```bash
docker compose -f docker-compose.prod.yml restart app
```

### Caché vieja después de un deploy
```bash
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan optimize
```
