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
