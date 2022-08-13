# projeqtor-docker

Projeqtor 8.2.4 docker images
Docker Stack (PHP/apache/Projeqtor container + Mysql container + PHPMyAdmin container)

## Prepare environnement

- clone repo into desired location.


## Build docker images

  > \> docker-compose build --force-rm --no-cache --pull --parallel

## Start the fulle stack

  > \> docker-compose up -d

## Connect to Projeqtor

- url: [http://localhost]()
- user: *admin*
- password: *admin*

## First access configuration

Replace database host value *127.0.0.1* by *mysql*

## Stop the full stack

  > \> docker-compose down

## Connect to PHPMyAdmin

- url: [http://localhost:8181]()
- user: *root*
- password: *mysql*

## Connect to MySQL

Use your preferred MySQL client connected to *localhost* port *3306*
(admin user: *root* / password: *mysql*)

## Credits

- https://github.com/fabrom/projeqtor-docker Fabrice Romand <fabrice.romand@gmail.com>
