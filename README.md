# Units

![UML diagram](docs/DC.jpg)

## Getting started

```shell
$ composer install
$ app/console doctrine:database:create
$ app/console doctrine:schema:update --force
$ app/console unit:populate
```

## Running the tests

```shell
$ phpunit
```

## Running the server

```shell
$ app/console server:run
```

The server is now running at http://localhost:8000/.
