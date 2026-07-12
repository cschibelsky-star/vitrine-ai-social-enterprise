#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/srv/apps/vitrine-ai-social-enterprise"
cd "$APP_DIR"

echo "[1/8] Atualizando código"
git pull origin main

echo "[2/8] Reconstruindo imagens"
docker compose -f compose.studio.yml build --no-cache studio_app studio_worker studio_scheduler

echo "[3/8] Subindo Studio"
docker compose -f compose.studio.yml up -d --force-recreate

echo "[4/8] Aguardando aplicação"
sleep 10

echo "[5/8] Banco e permissões"
docker exec -u root studio_app chown -R www-data:www-data storage bootstrap/cache
docker exec -u root studio_app chmod -R 775 storage bootstrap/cache
docker exec studio_app php artisan migrate --force

echo "[6/8] Assets e caches"
docker exec studio_app php artisan optimize:clear
docker exec studio_app php artisan filament:upgrade || true
docker exec studio_app php artisan optimize

echo "[7/8] Validações BUILD 009"
docker exec studio_app php -l app/Services/AI/ContentRefinementService.php
docker exec studio_app php -l app/Filament/Widgets/ContentStudioPreview.php
docker exec studio_app php -l app/Filament/Resources/ContentProjects/Pages/EditContentProject.php
curl -fsSI http://127.0.0.1:8090/admin/login >/dev/null

echo "[8/8] Estado final"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep studio

echo "BUILD 009 AI Content Studio aplicada com sucesso."
