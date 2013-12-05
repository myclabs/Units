# Units

![UML diagram](docs/DC.jpg)

## Getting started

```shell
$ composer install
$ app/console doctrine:database:create
$ app/console doctrine:schema:create
$ app/console unit:populate
```

## Running the tests

Running the unit tests:

```shell
$ phpunit
```

Running the functional tests:

```shell
$ phpunit -c phpunit-functional.xml.dist
```

The functional test suite automatically generates a SQLite database in the `tests/FunctionalTest` directory.
As such, it requires the `pdo_sqlite` extension and the user running the
tests must have write permissions in that directory.

## Running the server

```shell
$ app/console server:run
```

The server is now running at [http://localhost:8000/](http://localhost:8000/).
