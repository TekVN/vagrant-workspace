#!/usr/bin/env bash

if [[ -n "$1" ]]; then
    cp -i resources/Workspace.json Workspace.json
else
    cp -i resources/Workspace.yaml Workspace.yaml
fi

cp -i resources/after.sh after.sh
cp -i resources/aliases aliases

echo "Workspace initialized!"
