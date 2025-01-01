#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/victoriametrics ]; then
    echo "Victoria Metrics already installed."
    exit 0
fi

# https://docs.victoriametrics.com/quick-start/#starting-vm-single-from-a-binary
mkdir -p /opt/victoriametrics
cd /opt/victoriametrics

wget -q https://github.com/VictoriaMetrics/VictoriaMetrics/releases/download/v1.108.1/victoria-metrics-linux-amd64-v1.108.1.tar.gz
tar -xf victoria-metrics-linux-amd64-v1.108.1.tar.gz -C /usr/local/bin
rm victoria-metrics-linux-amd64-v1.108.1.tar.gz
chmod +x /usr/local/bin/victoria-metrics-prod
cat <<EOT >/etc/systemd/system/victoriametrics.service
Description=VictoriaMetrics service
After=network.target

[Service]
Type=simple
User=victoriametrics
Group=victoriametrics
ExecStart=/usr/local/bin/victoria-metrics-prod -storageDataPath=/opt/victoriametrics -retentionPeriod=30d -selfScrapeInterval=10s
SyslogIdentifier=victoriametrics
Restart=always

PrivateTmp=yes
ProtectHome=yes
NoNewPrivileges=yes

ProtectSystem=full

[Install]
WantedBy=multi-user.target
EOT

systemctl daemon-reload
systemctl start victoriametrics.service

touch /home/$WSL_USER_NAME/.features/victoriametrics
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP $HOMEUSER/.features
