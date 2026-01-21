## 🐳 Docker Compose Deploy - Guía Rápida

He generado una configuración completa de Docker para deploy en producción. Aquí te muestro qué se ha creado:

### 📁 Archivos Generados

#### 1. **docker-compose.deploy.yml** 
Configuración principal para producción con:
- **app**: Servicio PHP-FPM con Laravel
- **mysql**: Base de datos MySQL 8.0
- **redis**: Cache y queue management
- **nginx**: Servidor web optimizado

**Características:**
- Health checks en todos los servicios
- Reinicio automático en caso de fallo
- Gzip habilitado
- SSL listo para producción
- Volúmenes persistentes

#### 2. **Archivos de Configuración Nginx**
- `docker/nginx/nginx.conf` - Configuración principal
- `docker/nginx/conf.d/default.conf` - Vhost de aplicación

**Incluye:**
- Headers de seguridad
- Caché de assets
- Compresión gzip
- Configuración SSL comentada (descomentar cuando tengas certificado)

#### 3. **Scripts de Automatización**

**📦 scripts/deploy.sh**
```bash
./scripts/deploy.sh
```
Ejecuta automáticamente:
- Generación de APP_KEY
- Build de imágenes Docker
- Inicio de servicios
- Migraciones de BD
- Seeders (opcional)
- Build de assets
- Optimizaciones de caché

**💾 scripts/backup.sh**
```bash
./scripts/backup.sh
```
- Backup automático de BD
- Compresión gzip
- Mantiene últimos 7 backups

**🔍 scripts/health-check.sh**
```bash
./scripts/health-check.sh
```
- Verifica estado de servicios
- Uso de recursos
- Conectividad de BD y Redis

#### 4. **Archivos de Configuración**

**.env.deploy.example**
- Template de variables de entorno
- Documentado con todos los valores necesarios

**DEPLOY.md**
- Guía completa de deploy
- Troubleshooting
- Recomendaciones de seguridad
- Optimizaciones

### 🚀 Cómo Usar

#### Opción 1: Deploy Automático (Recomendado)
```bash
# 1. Copiar y configurar variables
cp .env.deploy.example .env

# 2. Editar .env con tus valores
nano .env

# 3. Ejecutar deploy
./scripts/deploy.sh
```

#### Opción 2: Comandos Manuales
```bash
# Copiar env
cp .env.deploy.example .env

# Generar APP_KEY
docker compose -f docker-compose.deploy.yml run --rm app php artisan key:generate

# Iniciar servicios
docker compose -f docker-compose.deploy.yml up -d

# Ejecutar migraciones
docker compose -f docker-compose.deploy.yml exec app php artisan migrate --force

# Build assets
docker compose -f docker-compose.deploy.yml exec app npm run build
```

### 🔧 Comandos Útiles Diarios

```bash
# Ver logs en tiempo real
docker compose -f docker-compose.deploy.yml logs -f app

# Ejecutar comando artisan
docker compose -f docker-compose.deploy.yml exec app php artisan <comando>

# Acceder a shell en el container
docker compose -f docker-compose.deploy.yml exec app bash

# Health check
./scripts/health-check.sh

# Backup de BD
./scripts/backup.sh

# Detener servicios
docker compose -f docker-compose.deploy.yml down

# Reiniciar servicios
docker compose -f docker-compose.deploy.yml restart
```

### 🔒 Seguridad - Pasos Importantes

1. **Generar contraseñas fuertes:**
   ```bash
   openssl rand -base64 32
   ```

2. **Configurar SSL/TLS (opcional pero recomendado):**
   - Obtener certificado Let's Encrypt
   - Descomenta la sección SSL en `docker/nginx/conf.d/default.conf`

3. **Variables sensibles:**
   - Nunca commitear `.env` a git
   - Usar `.env` con permisos 600
   - Guardar backup de `.env` de forma segura

4. **Firewall:**
   - Solo exponer puertos 80 y 443
   - BD y Redis internos solamente

### 📊 Arquitectura

```
┌─────────────────────────────────────────┐
│          NGINX (Reverse Proxy)          │
│         Puerto 80 y 443                 │
└────────────────┬────────────────────────┘
                 │
        ┌────────▼────────┐
        │  Laravel App    │
        │  (PHP-FPM)      │
        │ Puerto 9000     │
        └────┬────────┬───┘
             │        │
    ┌────────▼─┐  ┌──▼────────┐
    │  MySQL   │  │   Redis   │
    │  BD      │  │ Cache/Q   │
    │  Puerto  │  │ Puerto    │
    │  3306    │  │  6379     │
    └──────────┘  └───────────┘
```

### 🎯 Ambiente Recomendado

- **CPU:** 2+ cores
- **RAM:** 4GB mínimo (8GB recomendado)
- **Almacenamiento:** 20GB+
- **BD:** ~1-5GB según datos

### 📝 Variables de Entorno Críticas

```env
APP_KEY=              # Se genera automáticamente
DB_PASSWORD=          # Cambiar a contraseña fuerte
REDIS_PASSWORD=       # Cambiar a contraseña fuerte
APP_URL=              # Tu dominio (ej: https://app.com)
MAIL_*=               # Configurar con proveedor SMTP
```

### ✅ Checklist Pre-Deploy

- [ ] Copiar `.env.deploy.example` a `.env`
- [ ] Configurar todas las variables de entorno
- [ ] Generar contraseñas fuertes
- [ ] Verificar espacio en disco (20GB+)
- [ ] Hacer backup de datos existentes
- [ ] Revisar DEPLOY.md completamente
- [ ] Ejecutar `./scripts/deploy.sh`
- [ ] Verificar con `./scripts/health-check.sh`

### 🆘 Troubleshooting Rápido

```bash
# Si algo falla, revisar logs:
docker compose -f docker-compose.deploy.yml logs app

# Reiniciar servicios:
docker compose -f docker-compose.deploy.yml restart

# Limpiar y reiniciar:
docker compose -f docker-compose.deploy.yml down -v
./scripts/deploy.sh
```

### 📞 Soporte

Si encuentras problemas:
1. Revisa DEPLOY.md (guía completa)
2. Consulta logs: `docker compose -f docker-compose.deploy.yml logs -f`
3. Verifica variables .env
4. Ejecuta health-check: `./scripts/health-check.sh`

---

**Última actualización:** 21 de Enero, 2026
