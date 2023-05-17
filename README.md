## Laravel PDF AI

## Install Pgvector extensions

```
sudo apt-get install postgresql-server-dev-14 libpq-dev gcc make -y
cd /tmp
git clone --branch v0.4.2 https://github.com/pgvector/pgvector.git
cd pgvector
make clean && PG_CFLAGS=-DIVFFLAT_BENCH make && make install
```
