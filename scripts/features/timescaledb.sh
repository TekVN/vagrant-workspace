#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/timescale ]; then
    echo "TimescaleDB already installed."
    exit 0
fi

touch /home/$WSL_USER_NAME/.features/timescale

curl -fsSL https://packagecloud.io/timescale/timescaledb/gpgkey | sudo gpg --dearmor -o /etc/apt/keyrings/timescaledb.gpg
echo 'deb [signed-by=/etc/apt/keyrings/timescaledb.gpg] https://packagecloud.io/timescale/timescaledb/ubuntu/ jammy main' | sudo tee /etc/apt/sources.list.d/timescaledb.list

sudo apt-get update
sudo apt-get -y install timescaledb-2-postgresql-15

sudo timescaledb-tune --quiet --yes
printf "\ntimescaledb.telemetry_level=off\n" | sudo tee -a /etc/postgresql/15/main/postgresql.conf

sudo service postgresql restart

chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
