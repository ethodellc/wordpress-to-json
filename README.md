# Wordpress to JSON

Extracts Wordpress posts, terms, authors, categories, and metadata into JSON files.

### Requirements

`php-cli` with PDO extension with appropriate driver for the database you're using.

### Preperation

Create a file at the location `/opt/php-db-local.ini` with the following contents:

```
[mysql_local]
dsn = "mysql:dbname=WPDBNAME;unix_socket=/var/run/mysqld/mysqld.sock"
user = "wpuser"
pass = "password"
```

> NOTE: Feel free to change the location of the ini file.

### Running

php wp-to-json.php

There will be no console output, but you should see some JSON files in the current directory after running.

#### Other notes

This was hastily built with minimal error checking, all database operations are read-only.