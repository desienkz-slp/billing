cat > /etc/nginx/sites-available/radiusdesk << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/rdcore/rd/build/production/Rd;
    index index.html index.php;

    location /login {
        alias /var/www/html/rdcore/login;
    }

    location /cake4/rd_cake {
        alias /var/www/html/rdcore/cake4/rd_cake/webroot;
        try_files $uri $uri/ @cake4;
        
        location ~ \.php$ {
            fastcgi_pass unix:/run/php/php8.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    location @cake4 {
        rewrite ^/cake4/rd_cake/(.*)$ /cake4/rd_cake/index.php?$args last;
    }

    location ~ /\. {
        deny all;
    }
}
EOF
rm -f /etc/nginx/sites-enabled/default
ln -sfn /etc/nginx/sites-available/radiusdesk /etc/nginx/sites-enabled/radiusdesk
systemctl restart php8.2-fpm nginx
