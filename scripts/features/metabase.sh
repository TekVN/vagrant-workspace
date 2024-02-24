#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/metabase ]; then
    echo "Metabase already installed."
    exit 0
fi

wget -qO - https://packages.adoptium.net/artifactory/api/gpg/key/public | gpg --dearmor | tee /etc/apt/trusted.gpg.d/adoptium.gpg >/dev/null
echo "deb https://packages.adoptium.net/artifactory/deb $(awk -F= '/^VERSION_CODENAME/{print$2}' /etc/os-release) main" | tee /etc/apt/sources.list.d/adoptium.list
apt update -y
apt install temurin-11-jdk -y
wget -q https://downloads.metabase.com/v0.48.7/metabase.jar
groupadd -r metabase
useradd -r -s /bin/false -g metabase metabase
mkdir -p /opt/metabase
mv metabase.jar /opt/metabase
chown -R metabase:metabase /opt/metabase

/vagrant/scripts/create-mysql.sh metabase
/vagrant/scripts/create-postgres.sh metabase

cat <<EOT >/etc/default/metabase
MB_JETTY_HOST=0.0.0.0
MB_JETTY_PORT=9200
MB_API_KEY=apisecretkey
MB_PASSWORD_COMPLEXITY=weak
MB_PASSWORD_LENGTH=3
MB_DB_TYPE=mysql
MB_DB_DBNAME=metabase
MB_DB_PORT=3306
MB_DB_USER=workspace
MB_DB_PASS=secret
MB_DB_HOST=localhost
MB_EMOJI_IN_LOGS=false
EOT
chmod 640 /etc/default/metabase
cat <<EOT >/etc/systemd/system/metabase.service
[Unit]
Description=Metabase server
After=syslog.target
After=network.target

[Service]
WorkingDirectory=/opt/metabase
ExecStart=/usr/bin/java -jar /opt/metabase/metabase.jar
EnvironmentFile=/etc/default/metabase
User=metabase
Type=simple
Restart=always

[Install]
WantedBy=multi-user.target
EOT

systemctl daemon-reload
systemctl start metabase.service

touch /home/$WSL_USER_NAME/.features/metabase
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
