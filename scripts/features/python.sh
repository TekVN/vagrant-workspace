#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/pythontools ]; then
    echo "pythontools already installed."
    exit 0
fi

# Install Python
apt-get update
apt-get install -y python3-pip build-essential libssl-dev libffi-dev python3-dev python3-venv
sudo -H -u vagrant bash -c 'pip3 install django'
sudo -H -u vagrant bash -c 'pip3 install numpy'
sudo -H -u vagrant bash -c 'pip3 install masonite'

touch /home/$WSL_USER_NAME/.features/pythontools
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
