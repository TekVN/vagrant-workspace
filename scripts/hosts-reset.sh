#!/usr/bin/env bash

# Remove any entries from /etc/hosts and prepare for adding new ones.

sudo sed -i '/#### WORKSPACE-SITES-BEGIN/,/#### WORKSPACE-SITES-END/d' /etc/hosts

printf "#### WORKSPACE-SITES-BEGIN\n#### WORKSPACE-SITES-END\n" | sudo tee -a /etc/hosts >/dev/null
