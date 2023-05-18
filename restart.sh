#!/bin/bash

echo "Restarting php8.1-fpm."
sudo systemctl restart php8.1-fpm

echo "Restarting NGINX."
sudo systemctl restart nginx

echo "Restarting queue."
sudo supervisorctl restart all
