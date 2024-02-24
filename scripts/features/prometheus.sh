#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/prometheus ]; then
    echo "Prometheus already installed."
    exit 0
fi

# Install prometheus
cd /opt
wget -qO https://github.com/prometheus/prometheus/releases/download/v2.50.0/prometheus-2.50.0.linux-amd64.tar.gz
tar -xf prometheus-2.50.0.linux-amd64.tar.gz
mv prometheus-2.50.0.linux-amd64 prometheus
rm prometheus-2.50.0.linux-amd64.tar.gz
useradd -M -U prometheus
cat <<EOT >/etc/systemd/system/prometheus.service
[Unit]
Description=Prometheus Server
Documentation=https://prometheus.io/docs/introduction/overview/
After=network-online.target

[Service]
User=prometheus
Group=prometheus
Restart=on-failure
ExecStart=/opt/prometheus/prometheus \
  --config.file=/opt/prometheus/prometheus.yml \
  --storage.tsdb.path=/opt/prometheus/data \
  --storage.tsdb.retention.time=30d

[Install]
WantedBy=multi-user.target
EOT

chown prometheus:prometheus -R /opt/prometheus
systemctl daemon-reload
systemctl start prometheus.service

touch /home/$WSL_USER_NAME/.features/prometheus
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
