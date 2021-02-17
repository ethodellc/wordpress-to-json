# Wordpress to JSON

Extracts Wordpress posts, terms, authors, tags, categories, and metadata into JSON files, useful for porting blogs into other Content Management Systems.


### Requirements

`php-cli` with PDO extension with appropriate driver for the database you're using.


### Preperation

Create a file in the working directory named `migration-db.ini` with the following contents:

```
[dbconnection]
dsn = "mysql:dbname={DATABASENAME};unix_socket={/var/run/mysqld/mysqld.sock}"
user = "wpuser"
pass = "password"
```


### Running

php wp-to-json.php

Once extracted, the script will write the following JSON files in the working directory:

- `all-authors.json`
- `all-posts.json`
- `all-tags.json`
- `all-categories.json`


#### Other notes

This was hastily built with minimal error checking, all database operations are read-only.