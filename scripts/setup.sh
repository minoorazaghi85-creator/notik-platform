#!/bin/bash
set -e
echo "========================================="
echo "  Notik - Docker setup"
echo "========================================="
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  cp .env.example .env
  export $(grep -v '^#' .env | xargs)
fi
SITE_URL=${SITE_URL:-http://localhost:8080}
SITE_TITLE=${SITE_TITLE:-"Notik"}
ADMIN_USER=${ADMIN_USER:-admin}
ADMIN_PASSWORD=${ADMIN_PASSWORD:-change_this_strong_password}
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@example.com}
echo "1. Starting containers..."
docker compose up -d db wordpress
echo "2. Waiting for DB..."
sleep 15
echo "3. Install WordPress..."
docker compose --profile cli run --rm wpcli wp core is-installed 2>/dev/null || \
docker compose --profile cli run --rm wpcli wp core install \
  --url="$SITE_URL" --title="$SITE_TITLE" \
  --admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASSWORD" \
  --admin_email="$ADMIN_EMAIL" --skip-email
echo "4. Persian language..."
docker compose --profile cli run --rm wpcli wp language core install fa_IR --activate || true
echo "5. Plugins..."
docker compose --profile cli run --rm wpcli wp plugin install woocommerce --activate || true
docker compose --profile cli run --rm wpcli wp plugin install dokan-lite --activate || true
docker compose --profile cli run --rm wpcli wp plugin install waterwoo-pdf --activate || true
echo "6. Theme + Notik Core..."
docker compose --profile cli run --rm wpcli wp theme activate notik || true
docker compose --profile cli run --rm wpcli wp plugin activate notik-core || true
echo "Done. Site: $SITE_URL  Admin: $SITE_URL/wp-admin"
