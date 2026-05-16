#!/bin/sh
set -e

: "${WP_ADMIN_USER:?Set WP_ADMIN_USER before running seed.sh}"
: "${WP_ADMIN_PASSWORD:?Set WP_ADMIN_PASSWORD before running seed.sh}"
: "${WP_ADMIN_EMAIL:?Set WP_ADMIN_EMAIL before running seed.sh}"

wp core install --url=http://localhost:8082 --title="MultiRent Test" --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" --admin_email="$WP_ADMIN_EMAIL" --skip-email || true
wp theme activate MultiRent
wp plugin activate multirent-companion
wp option update permalink_structure '/%postname%/'
wp rewrite flush
wp eval 'multirent_companion_create_starter_site();'
wp option update blogdescription "Configurable rental template"
