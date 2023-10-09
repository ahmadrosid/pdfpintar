## Installing in your own VPS server

This project is created using this tech stack:

-   Laravel
-   PostgresDB
-   pgvector (for embedding)
-   php-pdf
-   OpenAI (for generate vector and chatting)
-   Redis (Optional)

## Requirement

Before you run this project make sure you have this app installed in your system:

1. PHP >= 8.1
1. PostgresDB >= 15.3
1. [pgvector](https://github.com/pgvector/pgvector)
1. NodeJS >= 18
1. Redis (optional)

## Docker (optional and fastest way)

If you don't want to setup everything manually the easiest way is to use docker.

```bash
sudo apt update
sudo apt install docker-ce
sudo systemctl start docker
sudo systemctl enable docker
sudo docker --version # Docker version 24.0.4
```

After docker installed you can just start docker with `docker-compose`:

```bash
docker-compose up -d
```

Then run database migration:

```bash
docker-compose exec server php artisan migrate
```

## Install Postgre with pgvector extension

We don't use mysql because it doesn't support vector data, so we will use postgre with pgvector to save embedding text. Here's how to setup in linux debian env.

```bash
sudo apt-get install postgresql-server-dev-14 postgresql-contrib libpq-dev gcc make -y
sudo apt-get install php8.1-pgsql php8.1-dom php8.1-curl php8.1-zip php8.1-redis
cd /tmp
git clone --branch v0.4.2 https://github.com/pgvector/pgvector.git
cd pgvector
make clean && PG_CFLAGS=-DIVFFLAT_BENCH make && make install
```

## Enable pgvector

Once pgvector installed we need to enable the extension, here's how to enable it. You can learn more about pgvector [here](https://github.com/pgvector/pgvector).

```sql
CREATE USER pdfpintar WITH PASSWORD 'password';
CREATE DATABASE pdfpintar OWNER pdfpintar;
ALTER USER pdfpintar WITH SUPERUSER;
```

## Install PHP

The minimum requirement for php version is 8.1, here's how you can install it in ubuntu >= 20.

```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-pgsql -y
```

## Install php-pdf

By default php doesn't support extracting pdf file, so we need to use [php-pdf](https://github.com/ahmadrosid/php-pdf).

Install required dependencies:

```bash
sudo apt install software-properties-common libfontconfig1-dev mupdf-tools gperf clang php8.1-dev build-essential autoconf unzip
```

Clone php-pdf :

```bash
git clone https://github.com/ahmadrosid/php-pdf.git
cd php-pdf
```

Build release php-pdf :

```bash
cargo build --release
```

Copy the build into php extensions folder :

```bash
# get php extension folder
php -i | grep extension_dir

# copy php-pdf, change this folder `/path/to/lib/php/pecl/20210902`
cp target/release/libphp_pdf.so /path/to/lib/php/pecl/20210902
```

## Build UI

Now let's get the laravel project up and running, we need to build the UI ready for production.

Make sure you have nodejs installed, here's how to install nodejs:

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
curl -fsSL https://get.pnpm.io/install.sh | sh -
```

Now install javascript dependencies and build the ui.

```bash
npm install
npm build
```

## Setup laravel

Add this env:

```bash
APP_NAME=pdfpintar
APP_ENV=base64:...
APP_KEY=base64:wx+...
APP_DEBUG=false
APP_URL=http://localhost:8000

OPENAI_API_KEY="sx..."

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pdfpintar
DB_USERNAME=pdfpintar
DB_PASSWORD=...
```

Run database migration :

```bash
php artisan migrate:fresh
```

Enable storage link :

```bash
php artisan storage:link
```

## Nginx Config

In this example I use my domain to setup nginx

Setup nginx config:

```bash
server {
    listen 80;

    server_name pdfpintar.ahmadrosid.com;
    root /var/www/pdfpintar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

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
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ^~ /document/chat/streaming$ {
        proxy_http_version 1.1;
        add_header Connection '';

        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Add permission to project directory:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

## Queue Worker

The queue will be used to indexing the pdf document from background, make sure you have redis installed.

```bash
sudo apt install redis-server
```

Install supervisor

```bash
bash apt install supervisor
```

Add new worker config.

```bash
cd /etc/supervisor/conf.d
vim queue-worker.conf
```

Point config value to the destination project folder.

```yaml
[program:queue-worker]
process_name = %(program_name)s_%(process_num)02d
command=php /var/www/pdfpintar/artisan queue:listen
autostart=true
autorestart=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/pdfpintar/storage/logs/worker.log
```

Update supervisorctl config

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart all
```
