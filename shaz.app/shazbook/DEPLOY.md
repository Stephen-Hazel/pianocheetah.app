# shazbook — Deployment Guide

## Prerequisites
- GoDaddy hosting with SSH access
- PHP 8.0+ (set via cPanel > MultiPHP Manager)
- MySQL database created in cPanel
- Google Cloud project with OAuth 2.0 credentials

---

## 1. Google Cloud Console setup

1. Go to https://console.cloud.google.com/
2. Create a project (or use existing)
3. APIs & Services → Credentials → Create Credentials → OAuth 2.0 Client ID
4. Application type: **Web application**
5. Authorized redirect URIs:
   - `https://shaz.app/shazbook/auth/callback`
6. Copy **Client ID** and **Client Secret**
7. APIs & Services → OAuth consent screen:
   - Add your email as a test user while in development

---

## 2. Upload files to GoDaddy via SSH

```bash
# On your local machine — upload the app (excluding public/)
rsync -avz --exclude='public/' --exclude='vendor/' --exclude='.git/' \
  /path/to/shazbook/ \
  USERNAME@yourdomain.com:~/shazbook/

# Upload public/ contents to the web root
rsync -avz public/ \
  USERNAME@yourdomain.com:~/public_html/shaz.app/shazbook/
```

**Result on server:**
```
/home/z8wo4irg6pxb/
├── shazbook/           ← app/, writable/, composer.json, .env
└── public_html/
    └── shaz.app/
        └── shazbook/  ← index.php, .htaccess, css/, uploads/
```

---

## 3. Install Composer dependencies (SSH)

```bash
ssh USERNAME@yourdomain.com
cd ~/shazbook
composer install --no-dev --optimize-autoloader
```

If Composer isn't available:
```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader
```

---

## 4. Configure .env on the server

```bash
cp ~/shazbook/.env.example ~/shazbook/.env   # if you have one
nano ~/shazbook/.env
```

Set these values:
```
CI_ENVIRONMENT = production
app.baseURL    = 'https://shaz.app/shazbook/'

database.default.hostname = localhost
database.default.database = YOUR_CPANEL_DB_NAME
database.default.username = YOUR_CPANEL_DB_USER
database.default.password = YOUR_CPANEL_DB_PASS

GOOGLE_CLIENT_ID     = paste-from-google-console
GOOGLE_CLIENT_SECRET = paste-from-google-console
GOOGLE_REDIRECT_URI  = https://shaz.app/shazbook/auth/callback

encryption.key = hex2bin(GENERATE_64_HEX_CHARS_BELOW)
```

Generate encryption key:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

---

## 5. Create database tables

In cPanel → phpMyAdmin, select your database and run:
```
~/shazbook/sql/schema.sql
```

Or via SSH:
```bash
mysql -u DB_USER -p DB_NAME < ~/shazbook/sql/schema.sql
```

---

## 6. Set permissions

```bash
chmod 755 ~/shazbook/writable/
chmod 755 ~/shazbook/writable/session/
chmod 755 ~/shazbook/writable/logs/
chmod 755 ~/shazbook/writable/cache/
chmod 755 ~/public_html/shaz.app/shazbook/uploads/
chmod 755 ~/public_html/shaz.app/shazbook/uploads/avatars/
chmod 755 ~/public_html/shaz.app/shazbook/uploads/posts/
```

---

## 7. Test

1. Visit https://shaz.app/shazbook → login page
2. Click "Continue with Google" → OAuth flow
3. You should land on the feed
4. Create a post with and without an image
5. Edit your profile and upload an avatar
6. Create a second Google account, sign in, add as friend

---

## Local development

```bash
cd shazbook
composer install
# Edit .env — set CI_ENVIRONMENT=development, local DB, localhost redirect URI
php spark serve
# Visit http://localhost:8080
```

For local .htaccess, change `RewriteBase /shazbook/` to `RewriteBase /`
in `public/.htaccess`.

---

## Updating production

```bash
# Upload changed files
rsync -avz --exclude='public/' --exclude='vendor/' --exclude='.git/' \
  /path/to/shazbook/ USERNAME@yourdomain.com:~/shazbook/

# Re-sync public assets if CSS/JS changed
rsync -avz public/css/ \
  USERNAME@yourdomain.com:~/public_html/shaz.app/shazbook/css/
```
