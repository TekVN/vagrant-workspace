#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/clickhouse ]; then
    echo "Clickhouse already installed."
    exit 0
fi

# https://clickhouse.com/docs/en/install
GNUPGHOME=$(mktemp -d)
GNUPGHOME="$GNUPGHOME" gpg --no-default-keyring --keyring /usr/share/keyrings/clickhouse-keyring.gpg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys 8919F6BD2B48D754
rm -rf "$GNUPGHOME"
chmod +r /usr/share/keyrings/clickhouse-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/clickhouse-keyring.gpg] https://packages.clickhouse.com/deb stable main" | sudo tee /etc/apt/sources.list.d/clickhouse.list
apt-get update
apt-get install -y clickhouse-server clickhouse-client xmlstarlet

# change port default
sed -i "s|9000|9003|g" /etc/clickhouse-server/config.xml
xmlstarlet ed -L -u /clickhouse/users/default/password -v "secret" /etc/clickhouse-server/users.xml
xmlstarlet ed -L -r /clickhouse/users/default -v "workspace" /etc/clickhouse-server/users.xml

mv /usr/lib/systemd/system/clickhouse-server.service /usr/lib/systemd/system/clickhouse.service
systemctl daemon-reload
service clickhouse start

touch /home/$WSL_USER_NAME/.features/clickhouse
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
