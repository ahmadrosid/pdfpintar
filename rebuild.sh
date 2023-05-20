#!/bin/sh
set -e

git pull
pnpm build

# restart
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
sudo supervisorctl restart all
