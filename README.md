# Appy Pay Wordpress Plugin

This plugin follows the [Appy Pay Official Docs](https://appypay.stoplight.io/)

# Setting up

You must have a working wordpress in your machine, then create a synlink from `appypay-payments` to `your-wp-install-dir\wp-content\plugins`
## Generate your tests database

> ./bin/install-wp-tests.sh <database> <username> <password> <host>

## Download dev dependencies
> php composer.phar install

## Run tests
> php vendor/bin/phpuint