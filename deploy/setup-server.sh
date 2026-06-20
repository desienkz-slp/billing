#!/bin/bash
#=============================================
# LadaPala-Bill: Server Setup & Deploy Script
# Target: Ubuntu 20.04/22.04/24.04
# Run as root on server 172.18.20.136
#=============================================
set -e

echo "=========================================="
echo " LadaPala-Bill - Server Setup"
echo "=========================================="

# 1. Accept SSH key from workstation
echo "[1/7] Setting up SSH key..."
mkdir -p /root/.ssh
chmod 700 /root/.ssh
PUBKEY="ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDqo1BgSTVbeCrF3NpZsPmYaM0LUXDy1VjYE95KbbmI/FHdGi4rxUtq7mQg/pmQUHs11NgeXMxSyjBsJvkelR6Udd5SH4svZahRBkwEESTiQgfEejc4n21/yRXVen564vn/lOfuYR/jVGqRUITZWhnwTSWnN8b9nD6dVVnypF91BZ/YL/sWoMYExoxNUmn/ZrTE6x5Oxd3Z1F7eFsnHs4U+q0NfeemfOlxwQehq80ZjlWYzL4u3YnMqft0yI+VMXUz0BSEEHgiDqCQ0tMh9al9q4VX4KU+3jRV86xqWNpXxhUGw1KL3QnmUEF456XH6H3O+o1J6AuZGEZhGg7jaK2mt17rUZyiAgLB5Db7+HJw5+Jvwv36mwgave0pvmUgF1BY8A+SqEpUICqnsCOGsoAlvyy5vH8Lt12q5GiamzlJRf1iA8dLBcJqRJazqcN2+3dTiPiE8l5jHmGIYdYLt05iM3g7QIDkqWTVZfFd8GQhwdA4tNnntH9M/CiZPI7lgcqHKI7ifO96iYwqyA0dVbybkNWyhIaDRdbXhvQwlQsZ1gT0ZgkpeMmDwZlQVN2bqHajGC699zzUblqZzeJ2pU4jEWGAJhwf7UA1gmqlRARzssZv+H9wglFgccL3MtoBX87oJV25uVbUdN7x1aXo0xtvVUKpIKDeEdo0wVzDm7bEpUQ== desie@SRNK-DGTL"
grep -qF "$PUBKEY" /root/.ssh/authorized_keys 2>/dev/null || echo "$PUBKEY" >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys
echo "  ✅ SSH key installed"

# 2. Install Docker
echo "[2/7] Installing Docker..."
if ! command -v docker &>/dev/null; then
    apt-get update -qq
    apt-get install -y -qq ca-certificates curl gnupg lsb-release
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
    chmod a+r /etc/apt/keyrings/docker.asc
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
    apt-get update -qq
    apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
    systemctl enable docker && systemctl start docker
    echo "  ✅ Docker installed"
else
    echo "  ✅ Docker already installed: $(docker --version)"
fi

# 3. Install Git, PHP, Composer (for artisan commands outside Docker)
echo "[3/7] Installing PHP 8.2, Git, Composer..."
if ! command -v php &>/dev/null; then
    apt-get install -y -qq software-properties-common
    add-apt-repository -y ppa:ondrej/php
    apt-get update -qq
    apt-get install -y -qq php8.2-cli php8.2-pgsql php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-redis unzip git
    echo "  ✅ PHP 8.2 installed"
else
    echo "  ✅ PHP already installed: $(php --version | head -1)"
fi

if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    echo "  ✅ Composer installed"
else
    echo "  ✅ Composer already installed: $(composer --version 2>/dev/null | head -1)"
fi

# 4. Create project directory
echo "[4/7] Setting up project directory..."
PROJECT_DIR="/var/www/ladapala-bill"
mkdir -p $PROJECT_DIR
echo "  ✅ Project dir: $PROJECT_DIR"

# 5. System info
echo ""
echo "=========================================="
echo " Server Ready! Info:"
echo "=========================================="
echo "  Hostname : $(hostname)"
echo "  OS       : $(cat /etc/os-release | grep PRETTY_NAME | cut -d= -f2 | tr -d '"')"
echo "  Docker   : $(docker --version 2>/dev/null)"
echo "  PHP      : $(php --version 2>/dev/null | head -1)"
echo "  Composer : $(composer --version 2>/dev/null | head -1)"
echo "  RAM      : $(free -h | awk '/Mem:/ {print $2}')"
echo "  Disk     : $(df -h / | awk 'NR==2 {print $4 " free of " $2}')"
echo ""
echo "=========================================="
echo " Next: Transfer project files via SCP"
echo "=========================================="
echo ""
echo "  From Windows PowerShell, run:"
echo "  scp -r c:\\xampp\\htdocs\\laravel-bill\\* root@172.18.20.136:$PROJECT_DIR/"
echo ""
echo "  Then on this server, run:"
echo "  cd $PROJECT_DIR && docker compose up -d"
echo "  docker compose exec app php artisan migrate --seed"
echo ""
