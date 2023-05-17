## Laravel PDF AI

![demo.png](/demo.png)

## Required ENV

```bash
APP_NAME=
APP_ENV=
APP_KEY=base64:wx+...
APP_DEBUG=
APP_URL=http://localhost:8000

HOME_DIR=

OPENAI_API_KEY=

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

## Setup Postgresql

```sql
CREATE USER pdfpintar WITH PASSWORD 'password';
CREATE DATABASE pdfpintar OWNER pdfpintar;
ALTER USER pdfpintar WITH SUPERUSER;
```

## Install Pgvector extensions

```bash
sudo apt-get install postgresql-server-dev-14 libpq-dev gcc make -y
sudo apt-get install php8.1-pgsql
cd /tmp
git clone --branch v0.4.2 https://github.com/pgvector/pgvector.git
cd pgvector
make clean && PG_CFLAGS=-DIVFFLAT_BENCH make && make install
```

## Nginx Config

Install php fpm:

```bash
sudo apt install php8.1-fpm
```

Setup nginx config:

```bash
server {
    listen 80;
    listen [::]:80;
    server_name 103.150.197.190;
    root /home/insightq/pdfpintar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Add permission

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```
