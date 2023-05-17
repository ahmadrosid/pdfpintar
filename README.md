## Laravel PDF AI

## Required ENV

```bash
APP_NAME="PDF Pintar"
APP_ENV=local
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

## Install Pgvector extensions

```bash
sudo apt-get install postgresql-server-dev-14 libpq-dev gcc make -y
cd /tmp
git clone --branch v0.4.2 https://github.com/pgvector/pgvector.git
cd pgvector
make clean && PG_CFLAGS=-DIVFFLAT_BENCH make && make install
```
