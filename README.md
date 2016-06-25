# db_powon
POWON project repository.

## Development Setup
Clone this project with git. Composer is required to donwload the dependencies.
Once in the project directory, run
```
$ composer install
```

To prepare the database, launch the scripts in the scripts folder for your development MySQL database.
Run `create_tables.sql` first, then optionally `populate_tables.sql` if you want some data in it.


## Tests

To run the tests with phpunit (phpunit must be installed), simply use the test_autoload.php bootstrap file.
From the project folder run:
```
$ phpunit --bootstrap tests/test_autoload.php tests/
```