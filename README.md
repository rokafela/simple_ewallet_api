# Flip Test API

## Setup Database
1. Create "simple_ewallet" database in PostgreSQL.
2. Create tables by running the "./simple_ewallet.sql" file.
3. Config the database connection according to your database host in "./application/config/database.php" file.

## Installation
1. Put the "flip_test_api" folder in your webserver public folder.
2. Run command "composer install" to make sure all the necessary modules are installed.

## Run Test
1. Open terminal / command prompt in "flip_test_api" folder.
2. Config the codeception.yml to the according to your webserver "url: http://localhost/flip_test_api".
3. Run command "php ./vendor/bin/codecept run" to run test cases.

## URL Example
1. http://localhost/flip_test_api/create_user
2. http://localhost/flip_test_api/balance_read
3. http://localhost/flip_test_api/balance_topup
4. http://localhost/flip_test_api/transfer
5. http://localhost/flip_test_api/top_transactions_per_user
6. http://localhost/flip_test_api/top_users