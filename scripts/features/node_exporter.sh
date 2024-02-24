#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/node_exporter ]; then
    echo "Node Exporter already installed."
    exit 0
fi

# Install exporter
cd /opt
wget https://github.com/prometheus/node_exporter/releases/download/v1.7.0/node_exporter-1.7.0.linux-amd64.tar.gz
tar -xf node_exporter-1.7.0.linux-amd64.tar.gz
mv node_exporter-1.7.0.linux-amd64 node_exporter
rm node_exporter-1.7.0.linux-amd64.tar.gz
groupadd -r node_exporter
useradd -r -s /bin/false -g node_exporter node_exporter
cat <<EOT >/etc/systemd/system/node_exporter.service
[Unit]
Description=Node Exporter
Documentation=https://prometheus.io/docs/guides/node-exporter/
Wants=network-online.target
After=network-online.target

[Service]
User=node_exporter
Group=node_exporter
Type=simple
Restart=on-failure
ExecStart=/opt/node_exporter/node_exporter --web.listen-address=:9100

[Install]
WantedBy=multi-user.target
EOT

chown node_exporter:node_exporter -R /opt/node_exporter
systemctl daemon-reload
systemctl start node_exporter.service

touch /home/$WSL_USER_NAME/.features/node_exporter
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
