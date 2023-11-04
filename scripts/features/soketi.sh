#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

HOMEUSER=/home/$WSL_USER_NAME

if [ -f $HOMEUSER/.features/soketi ]; then
    echo "Soketi already installed."
    exit 0
fi

# https://docs.soketi.app/getting-started/installation/cli-installation#installing
npm install -g @soketi/soketi

# config soketi
mkdir -p $HOMEUSER/.soketi
cat >$HOMEUSER/.soketi/config.json <<EOF
{
    "debug": true,
    "port": 6002,
    "appManager.array.apps": [
        {
            "id": "workspace",
            "key": "workspace",
            "secret": "secretkey"
        }
    ]
}
EOF

# supervisor
cat >/etc/supervisor/conf.d/soketi.conf <<EOF
[program:soketi]
process_name=%(program_name)s_%(process_num)02d
command=soketi start --config $HOMEUSER/.soketi/config.json
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$WSL_USER_NAME
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/soketi-supervisor.log
stopwaitsecs=60
stopsignal=sigint
minfds=10240
EOF

chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP $HOMEUSER/.features
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP $HOMEUSER/.soketi

supervisorctl reread && supervisorctl reload
