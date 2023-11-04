#!/bin/sh

# Nếu bạn muốn thêm bất cứ thứ gì hay làm gì ngay sau khi Workspace đã sẵn sàng thì viết vào đây.
#
# Nếu bạn có cấu hình dành riêng cho người dùng mà bạn muốn áp dụng, bạn cũng có thể tạo user-customizations.sh,
# nó sẽ được chạy sau tập lệnh này.

# Nếu bạn không muốn phiên bản node 18.x. Vậy thì bạn hãy bỏ comment ngay sau đây:

# Remove current Node.js version:
#sudo apt-get -y purge nodejs
#sudo rm -rf /usr/lib/node_modules/npm/lib
#sudo rm -rf //etc/apt/sources.list.d/nodesource.list

# Install Node.js Version desired (i.e. v13)
# More info: https://github.com/nodesource/distributions/blob/master/README.md#debinstall
#curl -sL https://deb.nodesource.com/setup_13.x | sudo -E bash -
#sudo apt-get install -y nodejs

# Hoặc xem thêm tại https://github.com/nodesource/distributions
