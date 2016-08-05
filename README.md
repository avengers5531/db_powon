# db_powon
POWON project repository.

## Development Setup

###Composer
Clone this project with git. Composer is required to donwload the dependencies.
Once in the project directory, run
```
$ composer install
```
Alternatively, you can download composer.phar, place it on the project folder and run it:
```
$ php composer.phar install
```
######Note: It is important to run these commands from the root of the project folder.
This should create a `vendor` folder inside the project directory.

###Database
To prepare the database, launch the scripts in the scripts folder for your development MySQL database.
Run `create_tables.sql` first, then optionally `populate_tables.sql` if you want some data in it.
To do that, you can simply cd into the scripts/ directory, log in to the mysql database with the command line and run:
```
mysql> source create_tables.sql
```
and
```
mysql> source populate_tables.sql
```
If the database schema has changed, it is probably necessary to drop the tables and recreate them. Note: This will delete all the data stored inside the tables.

### Tests

To run the tests with phpunit, simply use the test_autoload.php bootstrap file.
From the project folder run:
```
$ phpunit --bootstrap tests/test_autoload.php tests/
```
Alternatively, download phpunit.phar, place it in the project folder and run:
```
$ php phpunit.phar --bootstrap tests/test_autoload.php tests/
```

###Configuration
Create a configuration file in `config/` called `settings.php`. Follow the
structure provided by `settings.php.example` and add your personal configuration.
You will need to change the database configuration to point to your database.

###Apache
The easiest way is to set the 'document root' to be the project root folder. We added `.htaccess` files
to make sure it works.
