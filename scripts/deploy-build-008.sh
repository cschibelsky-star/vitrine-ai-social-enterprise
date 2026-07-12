#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/srv/apps/vitrine-ai-social-enterprise"
COMPOSE_FILE="compose.studio.yml"

cd "$APP_DIR"

echo "[1/7] Atualizando código"
git pull origin main

echo "[2/7] Reconstruindo containers"
docker compose -f "$COMPOSE_FILE" build --pull

echo "[3/7] Subindo Studio"
docker compose -f "$COMPOSE_FILE" up -d --force-recreate

echo "[4/7] Aguardando aplicação"
sleep 8

echo "[5/7] Banco e caches"
docker exec studio_app php artisan migrate --force
docker exec studio_app php artisan optimize:clear
docker exec studio_app php artisan filament:upgrade || true
docker exec studio_app php artisan optimize

echo "[6/7] Validações"
docker exec studio_app php -l app/Filament/Resources/ContentProjects/Schemas/ContentProjectForm.php
docker exec studio_app php -l app/Filament/Widgets/StudioQuickCreate.php
docker exec studio_app php -l app/Filament/Widgets/RecentContent.php
curl -fsSI http://127.0.0.1:8090/admin/login >/dev/null

echo "[7/7] Estado final"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep studio

echo "BUILD 008 AI Studio Experience aplicada com sucesso."
