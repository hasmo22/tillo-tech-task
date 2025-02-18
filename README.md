## Tillo Tech Test

Below you'll find setup instructions and testing info. Please find the questions answered under answers.md.

This is a Laravel app which provides an API to query order data for Tillo orders. It utilises mongodb via docker, a command to import data from the ```orders.json``` file that was provided. The app utilises commands, controller functionality, models, repositories and factories. The code also provides tests to ensure that functionality runs as intended.

## Project setup

1. Clone the app
```
git clone git@github.com:hasmo22/tillo-tech-task.git
cd tillo-tech-task
```

2. Install dependencies
```
composer install
```

3. Set the environment variables
```
cp .env.example .env
```
For this test, please copy and replace the following in the .env file so that the app can connect to the mongodb database:
```
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=tillodb
DB_USERNAME=root
DB_PASSWORD=password
```

4. Set the application key
```
php artisan key:generate
```

5. Start mongodb container (wait 2secs here for it to boot) and setup data through the import orders command.
```
docker-compose up -d
php artisan import:orders
```

5. Serve the application.
```
php artisan serve
```

At this point, the application should be running.
- Please navigate to http://localhost:8000/ for the frontend to access the dashboard and the order statistics.
- APIs will be available at: http://localhost:8000/api/orders

## Run tests

Tests were created for all Controllers and Repositories.

```
php artisan test
```