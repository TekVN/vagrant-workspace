#!/usr/bin/env bash

# Remove any entries from /etc/hosts and prepare for adding new ones.

sudo sed -i '/#### DEVWEB-SITES-BEGIN/,/#### DEVWEB-SITES-END/d' /etc/hosts

printf "#### DEVWEB-SITES-BEGIN\n#### DEVWEB-SITES-END\n" | sudo tee -a /etc/hosts >/dev/null
