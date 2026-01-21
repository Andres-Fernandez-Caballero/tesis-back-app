# Therapy App - Production Deployment Guide

## 📋 Prerequisites

- Docker and Docker Compose installed
- Git repository cloned
- Domain name (optional but recommended)

## 🚀 Quick Start

### 1. Prepare Environment Variables

```bash
cp .env.deploy.example .env
```

Edit `.env` and configure:
- `APP_KEY` - Will be generated automatically
- `APP_URL` - Your production domain
- `DB_PASSWORD` - Strong MySQL password
- `REDIS_PASSWORD` - Strong Redis password
- `MAIL_*` - Email service credentials

### 2. Run Deployment Script

```bash
chmod +x scripts/deploy.sh
./scripts/deploy.sh
```

The script will:
- Generate APP_KEY if missing
- Build Docker images
- Start all services
- Run migrations
- Seed database (optional)
- Build frontend assets
- Cache routes and config

### 3. Configure Web Server (Optional)

If using a reverse proxy (nginx, Apache):

```nginx
upstream app {
    server localhost:80;
}

server {
    server_name yourdomain.com;
    
    location / {
        proxy_pass http://app;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 📊 Services

### app (PHP-FPM)
- Main Laravel application
- Handles all business logic
- Port: 80

### mysql
- Database service
- Volume: mysql_data
- Port: 3306 (internal only, exposed via FORWARD_DB_PORT)

### redis
- Cache and queue service
- Volume: redis_data
- Port: 6379 (internal only)

### nginx
- Web server (included in docker-compose.deploy.yml)
- Handles HTTP requests
- Port: 80, 443

## 🔧 Common Commands

### View Logs
```bash
docker compose -f docker-compose.deploy.yml logs -f app
```

### Run Artisan Commands
```bash
docker compose -f docker-compose.deploy.yml exec app php artisan <command>
```

### Database Operations
```bash
# Run migrations
docker compose -f docker-compose.deploy.yml exec app php artisan migrate

# Run seeders
docker compose -f docker-compose.deploy.yml exec app php artisan db:seed

# Backup database
./scripts/backup.sh
```

### Cache Management
```bash
# Clear all caches
docker compose -f docker-compose.deploy.yml exec app php artisan cache:clear

# Optimize for production
docker compose -f docker-compose.deploy.yml exec app php artisan optimize
```

## 🔒 Security Recommendations

1. **Generate Strong Passwords**
   ```bash
   openssl rand -base64 32  # For DB_PASSWORD and REDIS_PASSWORD
   ```

2. **SSL/TLS Certificate**
   - Uncomment SSL configuration in `docker/nginx/conf.d/default.conf`
   - Use Let's Encrypt with Certbot:
   ```bash
   certbot certonly --standalone -d yourdomain.com
   ```

3. **Firewall Rules**
   - Only expose ports 80 and 443
   - Restrict database access to internal network
   - Use environment variables for sensitive data

4. **Regular Backups**
   ```bash
   # Run daily via cron
   0 2 * * * cd /path/to/app && ./scripts/backup.sh
   ```

5. **Monitor Logs**
   ```bash
   docker compose -f docker-compose.deploy.yml logs --tail=100 -f
   ```

## 📈 Performance Optimization

1. **Database Indexing**
   ```bash
   docker compose -f docker-compose.deploy.yml exec app php artisan tinker
   # Review and add indexes as needed
   ```

2. **Cache Configuration**
   - Using Redis for caching
   - Use `redis` connection for cache store
   - Configure appropriate TTLs in config/cache.php

3. **Queue Processing**
   - Using Redis for queues
   - Start queue worker:
   ```bash
   docker compose -f docker-compose.deploy.yml exec app php artisan queue:work --daemon
   ```

4. **Asset Optimization**
   ```bash
   docker compose -f docker-compose.deploy.yml exec app npm run build
   ```

## 🚨 Troubleshooting

### Application won't start
```bash
# Check logs
docker compose -f docker-compose.deploy.yml logs app

# Verify database connection
docker compose -f docker-compose.deploy.yml exec app php artisan db:show
```

### Permission issues
```bash
docker compose -f docker-compose.deploy.yml exec app chown -R www-data:www-data storage
docker compose -f docker-compose.deploy.yml exec app chmod -R 775 storage
```

### Memory issues
- Increase Docker memory limits in docker-compose.deploy.yml
- Optimize database queries
- Configure PHP memory_limit in docker/8.3/php.ini

### Redis connection issues
```bash
docker compose -f docker-compose.deploy.yml exec redis redis-cli ping
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Redis Documentation](https://redis.io/docs/)

## 🆘 Support

For issues or questions:
1. Check logs: `docker compose -f docker-compose.deploy.yml logs -f`
2. Review this guide
3. Check Laravel documentation
4. Review application error log in `storage/logs/`
