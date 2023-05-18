#!/bin/bash

sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
sudo supervisorctl restart all
