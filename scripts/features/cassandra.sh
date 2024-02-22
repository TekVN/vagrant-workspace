#!/usr/bin/env bash

if [ -f ~/.features/wsl_user_name ]; then
    WSL_USER_NAME="$(cat ~/.features/wsl_user_name)"
    WSL_USER_GROUP="$(cat ~/.features/wsl_user_group)"
else
    WSL_USER_NAME=vagrant
    WSL_USER_GROUP=vagrant
fi

export DEBIAN_FRONTEND=noninteractive

if [ -f /home/$WSL_USER_NAME/.features/cassandra ]; then
    echo "cassandra already installed."
    exit 0
fi

# Install Cassandra and driver dependencies
echo "deb https://debian.cassandra.apache.org 41x main" | sudo tee -a /etc/apt/sources.list.d/cassandra.sources.list
curl https://downloads.apache.org/cassandra/KEYS | sudo apt-key add -

sudo DEBIAN_FRONTEND=noninteractive apt update
sudo DEBIAN_FRONTEND=noninteractive apt install cassandra openjdk-8-jdk git libgmp-dev -y

# Start Cassandra and boot at runtime
sudo service cassandra start
sudo update-rc.d cassandra defaults

# Install DataStax C++ (required for PHP Extension)
wget -q https://downloads.datastax.com/cpp-driver/ubuntu/18.04/dependencies/libuv/v1.28.0/libuv1-dev_1.28.0-1_amd64.deb
wget -q https://downloads.datastax.com/cpp-driver/ubuntu/18.04/dependencies/libuv/v1.28.0/libuv1_1.28.0-1_amd64.deb
wget -q https://downloads.datastax.com/cpp-driver/ubuntu/18.04/cassandra/v2.12.0/cassandra-cpp-driver-dev_2.12.0-1_amd64.deb
wget -q https://downloads.datastax.com/cpp-driver/ubuntu/18.04/cassandra/v2.12.0/cassandra-cpp-driver_2.12.0-1_amd64.deb
dpkg -i libuv1_1.28.0-1_amd64.deb
dpkg -i libuv1-dev_1.28.0-1_amd64.deb
dpkg -i cassandra-cpp-driver_2.12.0-1_amd64.deb
dpkg -i cassandra-cpp-driver-dev_2.12.0-1_amd64.deb
rm libuv1-dev_1.28.0-1_amd64.deb libuv1_1.28.0-1_amd64.deb cassandra-cpp-driver-dev_2.12.0-1_amd64.deb cassandra-cpp-driver_2.12.0-1_amd64.deb

# Install PHP Extension
cd /usr/src
git clone https://github.com/datastax/php-driver.git

sudo phpenmod cassandra

# Clean Up
sudo rm -R /usr/src/php-driver

# Just in case other Java versions exist, set JAVA_HOME, because Cassandra doesn't work with newer
# Java versions than Java 8
echo "JAVA_HOME=/usr/lib/jvm/java-8-openjdk-amd64" | sudo tee -a /etc/default/cassandra
sudo service cassandra stop
sudo service cassandra start

touch /home/$WSL_USER_NAME/.features/cassandra
chown -Rf $WSL_USER_NAME:$WSL_USER_GROUP /home/$WSL_USER_NAME/.features
