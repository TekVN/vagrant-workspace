#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

SERVICE_STATUS=$(systemctl is-enabled php7.4-fpm.service)

if [ "$SERVICE_STATUS" == "disabled" ]; then
    systemctl enable php7.4-fpm
    service php7.4-fpm restart
fi
