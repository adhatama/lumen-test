# Demo REST API using Lumen
This demo is intended to demonstrate REST API using Lumen.

## Requirements
- PHP 7++
- MySQL
- Postman

## Installation
- Clone this repo
- Run `composer install`
- Setup your `.env`
- Run `php artisan migrate --seed`.
- Import [this Postman](https://www.getpostman.com/collections/cbfbd936a83407d69967) to get the list of available endpoints.

## Getting Started
- We can start from Login using our dummy user from seeder. Call `POST Login` endpoint from Postman to get the token. 
  - email: user@example.com
  - password: secret
- Use the token in the `Authorization` header for every request calls.

## Testing

### Setup
- Create new env named `.env.testing` and set the `DB_DATABASE` to other than current database in `.env` (e.g. `lumen_test`).
- Change the `phpunit.xml` `DB_` to match your `.env.testing` configuration.
- Run `php artisan migrate --seed --env=testing` to fill the initial data for the testing DB. This command only need to run once.

### Run
- Run `vendor/bin/phpunit` to run the test.

## TODO
- [ ] Add query string to filter, sort, and select certain fields in get list endpoints.
- [ ] Make the setup of testing simpler, which is removing the multiple changes needed in `.env.testing` and `phpunit.xml` files. 
- [ ] Add more tests.