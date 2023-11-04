#!/usr/bin/env bash

if [[ -n "$1" ]]; then
    cp -i resources/Devweb.json Devweb.json
else
    cp -i resources/Devweb.yaml Devweb.yaml
fi

cp -i resources/after.sh after.sh
cp -i resources/aliases aliases

echo "Devweb initialized!"
