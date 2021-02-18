# Wordpress to JSON

Extracts WordPress posts, terms, authors, tags, categories, and metadata into JSON files. Useful for migrating WordPress blogs into other Content Management Systems.


### Requirements

`php-cli` with PDO extension with appropriate database driver.


### Preparation

Create a file in the working directory named `migration-db.ini` with the following contents:

```
[dbconnection]
dsn = "mysql:dbname={DATABASENAME};unix_socket={/var/run/mysqld/mysqld.sock}"
user = "{wpuser}"
pass = "{password}"
```


### Running

`php wp-to-json.php`

Once extracted, the script will write the following JSON files in the working directory:

- `all-authors.json`
- `all-posts.json`
- `all-tags.json`
- `all-categories.json`


#### Other notes

- All database operations are read-only.
- Extracts Yoast Premium SEO titles and descriptions