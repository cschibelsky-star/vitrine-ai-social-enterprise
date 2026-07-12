#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/srv/apps/vitrine-ai-social-enterprise"
DB_CONTAINER="vitrine_mariadb"
REDIS_CONTAINER="vitrine_redis"
NETWORK="vitrine_net"
DB_NAME="vitrine_social"
DB_USER="vitrine_social"
APP_URL="https://social.vitrineiapro.com.br"

cd "$APP_DIR"

if ! docker network inspect "$NETWORK" >/dev/null 2>&1; then
    echo "ERRO: rede Docker $NETWORK não encontrada."
    exit 1
fi

if ! docker inspect "$DB_CONTAINER" >/dev/null 2>&1; then
    echo "ERRO: container $DB_CONTAINER não encontrado."
    exit 1
fi

if ! docker inspect "$REDIS_CONTAINER" >/dev/null 2>&1; then
    echo "ERRO: container $REDIS_CONTAINER não encontrado."
    exit 1
fi

ROOT_DB_PASSWORD="$(docker inspect "$DB_CONTAINER" --format '{{range .Config.Env}}{{println .}}{{end}}' | awk -F= '/^(MARIADB_ROOT_PASSWORD|MYSQL_ROOT_PASSWORD)=/{print $2; exit}')"

if [ -z "$ROOT_DB_PASSWORD" ]; then
    echo "ERRO: senha root do MariaDB não encontrada nas variáveis do container."
    exit 1
fi

if [ -f .env ]; then
    DB_PASSWORD="$(awk -F= '/^DB_PASSWORD=/{print $2}' .env | tail -1)"
else
    DB_PASSWORD="$(openssl rand -hex 24)"
fi

if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD="$(openssl rand -hex 24)"
fi

docker exec -i "$DB_CONTAINER" mariadb -uroot -p"$ROOT_DB_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASSWORD';
ALTER USER '$DB_USER'@'%' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'%';
FLUSH PRIVILEGES;
SQL

cat > .env <<EOF
APP_NAME="Vitrine IA Studio Pro"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=$APP_URL
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=$DB_CONTAINER
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASSWORD

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_CLIENT=phpredis
REDIS_HOST=$REDIS_CONTAINER
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@vitrineiapro.com.br"
MAIL_FROM_NAME="Vitrine IA Studio Pro"
EOF

chmod 600 .env
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R 33:33 storage bootstrap/cache

docker compose -f compose.studio.yml build --pull
docker compose -f compose.studio.yml up -d studio_app studio_web

docker exec studio_app php artisan key:generate --force
docker exec studio_app php artisan migrate --force
docker exec studio_app php artisan storage:link || true
docker exec studio_app php artisan optimize:clear
docker exec studio_app php artisan optimize

docker compose -f compose.studio.yml up -d studio_worker studio_scheduler

echo
echo "Studio implantado."
echo "Containers:"
docker ps --filter 'name=studio_' --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}'
echo
echo "Validação interna: curl -I http://127.0.0.1:8090/admin/login"
