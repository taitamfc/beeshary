This module provides a solution for integrating Redis as a caching mechanism into your PrestaShop store.

Using the memory for caching is faster than standard caching solutions, like filesystem or sql.
Similar to Memcached, Redis is using key-value data stores, although Redis is more accurately described as a data store structure. Redis is also NoSQL and it also keeps all data in RAM. Some people say about Redis that it's a "Memcached on steroids" because it has more features than Memcached and ,in my opinion, it's more powerful and flexible.

## Requirements:

- Redis Server: 3.0+
- PHP Redis extension (https://github.com/phpredis/phpredis)
- Recommended php version: 7.0+

## Installation:

See the INSTALL.txt in the module directory.

## Upgrade from RedisCache 1.0.x to 1.1.x

Before upgrading please completely uninstall the module (version 1.0.x) and remove all module files located at:

- `/modules/rediscache`
- `/overrides/classes/cache/CacheRedis.php`

## Troubleshooting:

### 1) Something went wrong, my site is down with error 500.

#### Using the emergency disable script:

Access `http://<your-store-url>/modules/rediscache/emergency_disable.php`

Notes:

- You have to be logged in to access the script.
- You can bypass redis and log in by adding `bypass_redis=1` to your URL as a query parameter.

#### Manual disable:

**Prestashop 1.6:**

Disable the caching by editing the configuration file at `config/settings.inc.php`
You need to modify `define('_PS_CACHE_ENABLED_', '1');` to `define('_PS_CACHE_ENABLED_', '0');`.

**Prestashop 1.7:**

Disable the caching by editing the configuration file at `app/config/parameters.php`
You need to modify `'ps_cache_enable' => 1,` to `'ps_cache_enable' => 0,`;

Contact us for further assistance: https://addons.prestashop.com/en/contact-us?id_product=26866

### 2) How can I know if caching works?

- Connect to your Redis server with redis-cli MONITOR. When there are visitors on your site you should see a lot of GET requests.
- To check if page cache works, the module sets a response header: `X-RedisCache: <page-hash>`

## Known Issues:

- Using this module with other full-page-cache modules is not recommended.

## Tested Prestashop Versions:

- Prestashop 1.6.0.4
- Prestashop 1.7.5.2
- Prestashop 1.7.6.1
