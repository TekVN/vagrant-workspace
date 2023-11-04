#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/grafana ]; then
    echo "grafana already installed."
    exit 0
fi

touch /home/$WSL_USER_NAME/.features/grafana
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features

echo "deb https://packages.grafana.com/oss/deb stable main" >/etc/apt/sources.list.d/grafana.list
curl -s https://packages.grafana.com/gpg.key | apt-key add -

apt-get update -y
apt-get install -y grafana

systemctl enable grafana-server
systemctl daemon-reload
systemctl start grafana-server
