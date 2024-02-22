#!/usr/bin/env bash

influx bucket create --token="workspace_secret" --name="$1" --org="workspace"
