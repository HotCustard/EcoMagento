
# Docker Magento 2.4.4 Open Source (CE)

Docker containers for Magento 2.4.x development including :

- PHP 7.4
- Apache 2.4
- MYSQL 8
- Varnish 6 FPC  
- RabbitMQ  
- PhpMyAdmin
- ELASTIC search 7.x
- REDIS Session, System, FPC
  
## Installation

1. git clone https://github.com/HotCustard/EcoMagento.git 
2. `docker-compose build`
3. `docker-compose up -d`   
4. Install Magento
`docker-compose exec -u magento php-apache install-magento`
5. Disable 2FA for testing
`docker-compose exec -u magento php-apache bin/magento module:disable Magento_TwoFactorAuth`

## Test

 - Admin
http://magento2.dev.com/admin  
 - Frontend
http://magento2.dev.com   
 - CLI


    `docker-compose exec -u magento php-apache bash`

to fix layout issues with demo data : `docker-compose exec -u magento php-apache cp /var/www/dev/magento2/vendor/magento/module-cms-sample-data/fixtures/styles.css /var/www/dev/magento2/pub/media/`
### More

https://blog.gaiterjones.com/docker-magento-2-development-deployment-php7-apache2-4-redis-varnish-scaleable/ for further deployment instructions.

