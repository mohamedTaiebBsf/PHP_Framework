# Create a simple blog by building a php framework from scratch

### Description
Grafikart course - [Mise en pratique de la POO en PHP](https://www.youtube.com/playlist?list=PLjwdMgw5TTLXP6JWACTxDqun0jJ5_sYvK) 🚀

### Instructions

1. Clone the repository or download it.
2. Open your terminal to the project folder.
3. Create a Mysql database called **monsupersite**. You can change it in config/config.php at **database.name** key.
4. Run:
    ```sh
    $ composer install
    $ ./vendor/bin/phinx migrate
    $ ./vendor/bin/phinx seed:run
    $ php -S localhost:8000 -t public
    ```
### Issues

If a "No PSR-17 factory detected to create a response" exception is appeared, That's because the installation of
**Whoops** package failed to import psr-7 file **HttpFactory.php**.

**SOLUTION:**

copy issues/HttpFactory.php to vendor/guzzlehttp/psr7/src/HttpFactory.php.

```sh
$ mv issues/HttpFactory.php vendor/guzzlehttp/psr7/src/HttpFactory.php
```


