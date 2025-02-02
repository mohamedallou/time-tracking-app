# Set Up
## Requirements
Docker
php >= 8.2
php fpm

## Docker

In the backend folder run
```shell
docker compose up -d
```
## Database

In the backend folder, open the terminal and run:

```shell
 php bin/console doctrine:migrations:migrate 
```

## Web Server
We are using Symfony local web server for dev purposes. In the backend folder run:
```shell
symfony server:start
```

# Design Considerations

We set a constraint to limit the difference between start and end date in a log entry to less than 24 hours.
This of course makes sense since it's not possible to work continuously for 24 hours, and it will simplify our log managements
and the processing of the statistics