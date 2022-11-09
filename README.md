<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.addintel.co.uk/images/logo--white.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Ticketing Laravel Challenge

The Ticketing System is based on Laravel 8. For deployment, system requires `Docker` and `Docker Compose` installed. Once the Docker and Docker compose are installed, run following commands to setup system locally. 

- `docker-compose up -d` - Run at project root to build docker images required for system using docker-compose.yml. 
- `docker exec -u 0 app sh app-setup.sh` - Run at project root to build the project. THis command will download php dependencies, start laravel, setup database migrations and seeders. This command will run the tests and generate HTML test reports for the project. 
- `docker exec -u 0 app service cron start` - Run at project root to start CRON job within container. CRON frequency is defined with DOCKERFILE so with this command we only need to start CRON service. 

Project contains following elements implemented: 
- A schedule containing `create` and `process` Artisan commands running every 1 minute and 5 minutes respectively. 
- `artisan app:tickets -A create` command to create tickets via CRON
- `artisan app:tickets -A process` command to process ticket via CRON
- `api/tickets` endpoint for paginated list of all tickets
- `api/tickets/processed` endpoint for paginated list of all processed tickets
- `api/tickets/unprocessed` endpoint for paginated list of all unprocessed tickets
- `api/tickets/from/[email]` endpoint for paginated list of all tickets by user email
- `api/tickets/stats` endpoint for giving general stats about tickets in database including
  - Total number of tickets
  - Total number of processed tickets
  - Total number of unprocessed tickets
  - User email with highest number of tickets
  - User email with lowest number of tickets
  - Time of last processed ticket

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
