# Installation and configuration

### Installation

Run `composer install` to install Futureecom platform with all its dependencies.
Laravel auto-discovery will discover all required service providers.

If there is a need to install Platform that is not yet tagged by any version, 
use composer dev installation by specifying a branch. Like this: composer require `futureecom/futureecom:dev-branch`

Steps that you need to follow to do your first Platform setup:
1) run artisan migrations to update MongoDB collections with unique keys: `php artisan migrate`
2) setup your organisation and stores: `php artisan futureecom:setup` 
3) install default configurations for previously created store: `php artisan futureecom:install:configuration`

## Quick setup

If you would like to start using platform with some basic, fake data, you can follow this steps:
1) run `php artisan migrate` to migrate DB changes (mostly required if there are some existing tables)
2) run `php artisan futureecom:setup` command, name your Organisation, but answer `no` to all other questions this command will ask
3) run `php artisan futureecom:demo` command, that will tap into your existing Organistaion (or will ask to choose one). 
Running this command once will create single store inside organisation and will seed it. Running it twice will create two stores and so on.
4) run `php artisan futureecom:install:configuration --all` to make sure that settings are installed 
   1) consider running `php artisan config:cache` prior to the configuration command to make sure that cached config is up to date
5) you can also run `php artisan futureecom:seed` to seed additional data to your store (like more products)
6) if there is a need to quickly create many orders run `php artisan futureecom:fake:orders --num=X` command (where num is how many orders you would like to create). 
But do remember to have listeners running in background (at least for `order.placed` and `order.created` events)

After running our seeder you can start using storefront and admin console with two different clients.
For storefront use client ID: `90000000-0000-0000-0000-000000000000`, 
for admin console use: `00000000-0000-0000-0000-000000000000` with `admin` secret.

The default admin created by this will be `admin@futureecom / admin`

### .env file

1) Change DB_CONNECTION variable to 'mongodb'
2) Change MONGO_HOST to your Mongodb location (localhost or docker image)
3) Change REDIS_HOST to your Redis location (localhost or docker image)
4) Setup mailing account (otherwise Notifications module will not work properly)
2) Change QUEUE_CONNECTION. By default its set up to sync, but for production it should be either
database or redis. If you will pick database, then you need to make sure that `queue.php` database driver
will be set to mongodb (same for failed jobs table).

### Authorization

Put two files into a `storage/keys` directory: `public.key` and `private.key`.
Both files should contain SSH keys - public and private as files says.

If you dont want to create new SSH keys or put your own, for a development (**not suggested for production!**)
add `AUTH_ENCRYPTION_ALGORITHM` with value `HS256` to your .env file. 
Then you can fill your public and private keys with a single string (same for both) instead of RSS keys

### Using Postman

Make sure that your postman will `Accept` `application/json` so that Laravel response will be properly formatted (including validation errors).
Always remember about tenancy headers, as most of our endpoints are behind it. 
