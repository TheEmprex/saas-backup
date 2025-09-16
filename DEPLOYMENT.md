# OnlyVerified SaaS — Production Deployment (Hetzner Ubuntu)

This guide makes your app deployment-ready on a fresh Hetzner Ubuntu server (22.04/24.04). It covers Nginx + PHP-FPM, Redis, Horizon, Laravel Reverb websockets, SSL, systemd services, and log rotation.

Quick checklist
- Domain: app.example.com (app) and ws.example.com (websocket)
- DNS A records point to your Hetzner server IP
- Ubuntu 22.04/24.04 LTS server

1) Install base packages
sudo apt update && sudo apt -y upgrade
sudo apt -y install nginx redis-server git curl unzip zip gnupg2 software-properties-common ca-certificates lsb-release

# PHP 8.3 (preferred) — use 8.4 if you have a repo providing it
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt -y install php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-redis

# Node.js (optional if you build assets locally)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt -y install nodejs

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

2) Create app directory and clone code
sudo mkdir -p /var/www/onlyverified
sudo chown -R $USER:$USER /var/www/onlyverified
cd /var/www/onlyverified

git clone <YOUR_REPO_URL> .

3) Environment (copy and edit)
cp .env.production.example .env

Edit .env and set:
- APP_URL=https://app.example.com
- DB_* (host, database, username, password)
- REDIS_* (host = 127.0.0.1)
- BROADCAST_DRIVER=pusher
- Reverb + Pusher settings (see section 6)
- MAIL_* production credentials
- CORS_ALLOWED_ORIGINS=https://app.example.com
- HORIZON_ADMINS=you@yourdomain.com

Generate APP_KEY if empty
php artisan key:generate

4) Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage -type d -exec chmod 775 {} \;
sudo chmod -R 775 bootstrap/cache

5) Install dependencies and build assets
# On server (or build locally and copy public/build)
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan storage:link

# Option A: build on server
npm ci
npm run build

# Option B: build locally and upload
# rsync -avz --delete public/build/ server:/var/www/onlyverified/public/build/

6) Websockets (Laravel Reverb via Pusher protocol)
We recommend a dedicated websocket subdomain (ws.example.com) reverse-proxied to a local Reverb server on port 8080.

Set these env vars (match .env.production.example):
- REVERB_SERVER_HOST=127.0.0.1
- REVERB_SERVER_PORT=8080
- REVERB_HOST=ws.example.com
- REVERB_PORT=443
- REVERB_SCHEME=https
- REVERB_ALLOWED_ORIGINS=https://app.example.com
- REVERB_APP_KEY, REVERB_APP_SECRET, REVERB_APP_ID
- PUSHER_* mapped to REVERB_* (see .env.production.example)
- VITE_PUSHER_* mapped to PUSHER_*

Generate secure keys (example):
REVERB_APP_KEY=$(openssl rand -hex 16)
REVERB_APP_SECRET=$(openssl rand -hex 32)
REVERB_APP_ID=onlyverified

Update .env with these values.

7) Nginx
Copy and edit deploy/nginx/app.conf and deploy/nginx/reverb.conf:
- Replace app.example.com and ws.example.com with your real domains

sudo cp deploy/nginx/app.conf /etc/nginx/sites-available/onlyverified.app
sudo cp deploy/nginx/reverb.conf /etc/nginx/sites-available/onlyverified.ws
sudo ln -s /etc/nginx/sites-available/onlyverified.app /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/onlyverified.ws /etc/nginx/sites-enabled/

sudo nginx -t && sudo systemctl reload nginx

8) SSL with Let’s Encrypt
sudo apt -y install certbot python3-certbot-nginx
sudo certbot --nginx -d app.example.com -d ws.example.com

Certbot will add SSL blocks; ensure proxy headers/timeouts in websocket site remain.

9) Systemd services
Copy unit files and enable them:

sudo cp deploy/systemd/horizon.service /etc/systemd/system/
sudo cp deploy/systemd/reverb.service /etc/systemd/system/
sudo cp deploy/systemd/schedule.service /etc/systemd/system/
sudo cp deploy/systemd/schedule.timer /etc/systemd/system/

sudo systemctl daemon-reload
sudo systemctl enable horizon reverb schedule.timer
sudo systemctl start horizon reverb schedule.timer

Check status:
sudo systemctl status horizon reverb schedule.timer

10) PHP-FPM and Nginx reload
sudo systemctl enable php8.3-fpm nginx redis-server
sudo systemctl restart php8.3-fpm nginx redis-server

11) Database migrations and caches
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache

12) Horizon access
Set HORIZON_ADMINS in .env with comma-separated admin emails. Horizon is at /horizon (configurable via HORIZON_PATH).

13) Logrotate
sudo mkdir -p /etc/logrotate.d
sudo cp deploy/logrotate/laravel.conf /etc/logrotate.d/laravel

Force a rotation test:
sudo logrotate -f /etc/logrotate.d/laravel

14) Health check
We added GET /health which returns { ok: true }. Use in uptime monitoring and load balancers.

15) Rolling deploy (manual)
cd /var/www/onlyverified
git pull --rebase
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci && npm run build   # or skip if building elsewhere
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl reload php8.3-fpm
sudo systemctl restart horizon
sudo systemctl restart reverb

16) Security notes
- Ensure APP_DEBUG=false in production
- CORS_ALLOWED_ORIGINS should list only your domains
- Session cookies secure (SESSION_SECURE_COOKIE=true) and proper SESSION_DOMAIN
- Disable or remove all local-only debug routes before production (already guarded)
- Keep packages patched; Livewire >= 3.6.4 already satisfied
- Consider pinning tymon/jwt-auth to a stable version in composer.json before production (or migrate to php-open-source-saver/jwt-auth)

Troubleshooting
- Websocket 403 at /broadcasting/auth → check session cookies, CSRF, auth middleware, and Nginx proxy headers
- Echo connects but no events → verify BROADCAST_DRIVER=pusher, REVERB_* and PUSHER_* match, and events broadcastAs()/channels are correct
- Horizon 403 → set HORIZON_ADMINS and sign in as that email
- Mixed content → ensure APP_URL uses https and VITE_PUSHER_FORCE_TLS=true
- 502 from PHP → check php-fpm pool socket path in Nginx config matches your PHP version

