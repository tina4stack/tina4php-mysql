#MYSQL 
##Installing

```
composer install tina4stack/tina4php-mysql
```

##Testing with docker

```
docker run -d --platform linux/x86_64 -p 127.0.0.1:33306:3306 -e MYSQL_ROOT_PASSWORD=pass1234 -e MYSQL_USER=sysdba -e MYSQL_PASSWORD=pass1234 -e MYSQL_DATABASE=testing mysql:latest
```