# simple-secret-server
[Introductions for this project](https://github.com/ngabesz-wse/secret-server-task)

This project using [Slim framework](https://www.slimframework.com), PHP 7.4+ required.

## Deploy
Set the config file (config.php) int the root directory based on the config.php.sample, and run composer update and install.

## Stucture
### App\Secrets
Manage (create and read) secrets.
### App\Utils
Utility functions. Currently it provides functions to:
- dbConnect: Connect to the database
- setResponse: Format the type of the response based of the Accept header (JSON or XML).
