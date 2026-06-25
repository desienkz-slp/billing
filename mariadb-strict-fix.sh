cat > /etc/mysql/mariadb.conf.d/99-radiusdesk.cnf << 'EOF'
[mysqld]
sql_mode = "ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
EOF
systemctl restart mariadb
