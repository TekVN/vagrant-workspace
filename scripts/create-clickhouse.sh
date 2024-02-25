#!/usr/bin/env bash

DB=$1

clickhouse=$(ps ax | grep clickhouse-server | wc -l)

if [ "$clickhouse" -gt 1 ]; then
    clickhouse-client --port 9003 --password secret --query "CREATE DATABASE IF NOT EXISTS \`$DB\`"
else
    # Skip Creating database
    echo "We didn't find Clickhouse, skipping \`$DB\` creation"
fi
