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
The database credentials are in the ***.env** file
In the backend folder, open the terminal and run:

```shell
 php bin/console doctrine:migrations:migrate 
```

To populate the database with data run the sql file located under ***backend/resources/timelog.sql***

## Web Server
We are using Symfony local web server for dev purposes. In the backend folder run:
```shell
symfony server:start
```

# Design Considerations

We set a constraint to limit the difference between start and end date in a log entry to less than 24 hours.
This of course makes sense since it's not possible to work continuously for 24 hours, and it will simplify our log managements
and the processing of the statistics

## Frontend
We created a single page app used Vuetify UI library on top of vuejs.

In the frontend folder, open the terminal and run:
````shell
npm ci
npm run dev
````
This will install the dependencies and start dev server.

The app is composed of 2 main view: the default home view with the tracking buttons, and the admin area where
the user can create, delete and edit time logs. This admin area is found under ***http://localhost:5173/admin***

