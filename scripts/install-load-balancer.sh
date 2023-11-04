#!/usr/bin/env bash

block="
upstream devwebup {
    server 127.0.1.1:8111;
}

server {
    listen 80;
    listen 443 ssl default_server;

    location / {
        proxy_pass http://devwebup;
        proxy_set_header HOST \$host;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
    }

    ssl_certificate     /etc/nginx/ssl/devweb.test.crt;
    ssl_certificate_key /etc/nginx/ssl/devweb.test.key;

}
"

echo "$block" >"/etc/nginx/sites-available/default"
ln -fs "/etc/nginx/sites-available/default" "/etc/nginx/sites-enabled/default"
