## Installing in your own VPS server

PDFPintar is built using the following tech stack:

-   Laravel
-   PostgresDB
-   pgvector (for embedding)
-   php-pdf
-   OpenAI (for generate vector and chatting)
-   Redis (Optional)

## Requirement

<!-- Before you run this project make sure you have this app installed in your system: -->

Before running this project ensure you have the following software installed on your system.

1. PHP >= 8.1
2. PostgresDB >= 15.3
3. [pgvector](https://github.com/pgvector/pgvector)
4. NodeJS >= 18
5. Redis (optional)

## Docker (optional and fastest way)

If you prefer not to set up everything manually, the easiest way it to use Docker. Install Docker with the following commands (if not installed yet):

```bash
sudo apt update
sudo apt install docker-ce
sudo systemctl start docker
sudo systemctl enable docker
sudo docker --version # Docker version 24.0.4
```

Once Docker is installed, start PDFPIntar using `docker-compose`:

```bash
docker-compose up -d
```

Then run database migration:

```bash
docker-compose exec server php artisan migrate
```

## Installing Postgres with pgvector extension

PDFPintar uses Postgres instead of MySQL due to its support for vector data. Below are the steps to configure Postgres with the pgvector extension on a Linux Debian environment.

```bash
sudo apt-get install postgresql-server-dev-14 postgresql-contrib libpq-dev gcc make -y
# Depending on your operating system, you might need another version of postgresql-server-dev

sudo apt-get install php8.1-pgsql php8.1-dom php8.1-curl php8.1-zip php8.1-redis
# If the above line fails, try removing the version number. For example, use 'sudo apt-get install php-pgsql php-dom php-curl' and so on. If you get PHP 8.2 or higher, then you're good to go.

cd /tmp
git clone --branch v0.4.2 https://github.com/pgvector/pgvector.git
cd pgvector
make clean && PG_CFLAGS=-DIVFFLAT_BENCH make && make install
```

## Enabling pgvector

Once pgvector ins installed, enable the extension. You can learn more about pgvector [here](https://github.com/pgvector/pgvector).

```sql
CREATE USER pdfpintar WITH PASSWORD 'password';
CREATE DATABASE pdfpintar OWNER pdfpintar;
ALTER USER pdfpintar WITH SUPERUSER;
```

## Installing PHP

The minimum requirement for php version is 8.1, here's how you can install it in ubuntu >= 20.

```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1 php8.1-fpm php8.1-pgsql -y
# Here again, you might need to remove the version number. For example, `sudo apt-get install php php-fpm php-pgsql`
```

## Installing php-pdf

By default php doesn't support pdf file extraction. We'll need [php-pdf](https://github.com/ahmadrosid/php-pdf) for this purpose.

Install required dependencies:

```bash
sudo apt install software-properties-common libfontconfig1-dev mupdf-tools gperf clang php8.1-dev build-essential autoconf unzip
```

Clone php-pdf :

```bash
git clone https://github.com/ahmadrosid/php-pdf.git
cd php-pdf
```

Ensure your system has Rust installed, (or follow this [instruction](https://www.rust-lang.org/learn/get-started) to install it) then proceed with building php-pdf:

```bash
cargo build --release
```

Copy the build binary into php extensions folder:

```bash
# get php extension folder
php -i | grep extension_dir

# copy php-pdf, change this folder `/path/to/lib/php/pecl/20210902`
cp target/release/libphp_pdf.so /path/to/lib/php/pecl/20210902
```

## Building the UI

Now let's get the laravel project up and running, we need to build the UI ready for production.

Ensure you have nodejs installed, here's how:

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

## Setting Up Laravel

Add the following environment variables to your `.env` file:

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

## Nginx Configuration

In this example I configure it to work on my domain.

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

Adjust permission for the project directory:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

## Queue Worker

A queue worker is used for indexing PDF documents in the background. Ensure Redis is available:

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

Configure the worker to point to the destination folder:

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
