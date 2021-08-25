<?php
/**
 * Redis Cache powered by Vopster
 *
 *    @author    Vopster
 *    @copyright 2017 Vopster
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *    @link      https://addons.prestashop.com/en/contact-us?id_product=26866
 */

final class RedisHelper
{
    /**
     * Checks if the dependencies are satisfied.
     *
     * @param object $module
     * @return bool
     */
    public static function dependencyCheck($module)
    {
        $dependenciesSatisfied = true;

        if (!self::isModuleLoaded() || !self::isRedisInstalled()) {
            $dependenciesSatisfied = false;
            $module->warning[] = $module->l(
                'PHP-Redis module is not loaded or installed.'
            );
        }

        return $dependenciesSatisfied;
    }

    /**
     * Checks if the Redis class exists.
     *
     * @return boolean
     */
    public static function isRedisInstalled()
    {
        return class_exists('Redis');
    }

    /**
     * Checks if the Redis extension exists and was loaded.
     *
     * @return boolean
     */
    public static function isModuleLoaded()
    {
        return extension_loaded('redis');
    }

    /**
     * Returns the configuration keys for this module
     *
     * @return array Configuration keys
     */
    public static function getConfigKeys()
    {
        $defaults = self::getDefaultConfig();
        return array_keys($defaults);
    }

    /**
     * Helper method to check if the bypass parameter exists in the URL.
     *
     * @return bool
     */
    public static function bypass()
    {
        return Tools::getValue('bypass_redis') == 1;
    }

    /**
     * Tests if the connection to the Redis server can be established
     * with the provided connection string.
     *
     * @param string $connection_string
     * @return bool
     */
    public static function testConnection($connection_string)
    {
        $redis = new Redis();

        $connection = parse_url($connection_string);

        if (!empty($connection['query'])) {
            $params = explode('&', $connection['query']);
            unset($connection['query']);
            foreach ($params as $param) {
                $data = explode('=', $param);
                $connection[$data[0]] = $data[1];
            }
        }

        try {
            $port = isset($connection['port']) ? (int)$connection['port'] : 6379;
            $connected = $redis->connect($connection['host'], $port);

            if (!$connected) {
                return false;
            }

            if (!empty($connection['auth'])) {
                try {
                    $redis->auth($connection['auth']);
                } catch (Exception $e) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        try {
            return $redis->ping() == '+PONG';
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Returns the Redis connection object.
     *
     * @param object $module
     * @return object
     */
    public static function getConnection($module = false)
    {
        static $redis = false;

        if ($redis) {
            return $redis;
        }

        $config = self::getConfig();

        if (isset($config['PS_REDIS_STATUS']) && $config['PS_REDIS_STATUS'] === 0) {
            return false;
        }

        $redis = new Redis();

        try {
            $connection_method = 'connect';

            if (isset($config['PS_REDIS_PERSISTENT']) && $config['PS_REDIS_PERSISTENT'] === 1) {
                $connection_method = 'pconnect';
            }

            $tls = strpos($config['PS_REDIS_CONNECTION'], 'tls://') !== false ?
                'tls://' : '';

            // Process the connection string.
            $connection_data = explode(':', str_replace('tls://', '', $config['PS_REDIS_CONNECTION']));

            $connection = count($connection_data) > 1 ? [
                //'server' => $tls . $connection_data[0], //@todo: TLS connection fails.
                'server' => $connection_data[0],
                'port' => (int)$connection_data[1],
            ] : $tls . $connection_data[0];

            $connected = false;
            if (isset($connection['port'])) {
                $connected = $redis->{$connection_method}(
                    $connection['server'],
                    $connection['port']
                );
            } else {
                $connected = $redis->{$connection_method}($connection);
            }

            if (!$connected) {
                $redis = false;
            } else {
                if (!empty($config['PS_REDIS_PASSWORD'])) {
                    try {
                        $redis->auth($config['PS_REDIS_PASSWORD']);
                    } catch (Exception $e) {
                        if ($module) {
                            $module->warning[] = $module->l(
                                'Couldn\'t authenticate to Redis Server.'
                            );
                        }
                    }
                }

                $redis->setOption(Redis::OPT_SERIALIZER, $config['PS_REDIS_SERIALIZER']);
                $redis->setOption(Redis::OPT_PREFIX, $config['PS_REDIS_PREFIX']);
                // $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);

                $redis->select($config['PS_REDIS_DB']);

                if (Tools::strlen(trim($config['PS_REDIS_CUSTOM_CONFIG'])) > 0) {
                    $custom_configs = explode("\n", $config['PS_REDIS_CUSTOM_CONFIG']);

                    if (!empty($custom_configs)) {
                        foreach ($custom_configs as $custom_config) {
                            $redis_conf = explode(':', $custom_config);
                            if (count($redis_conf) == 2) {
                                $redis->config("SET", $redis_conf[0], $redis_conf[1]);
                            }
                        }
                    }
                }
            }
            return $redis;
        } catch (Exception $e) {
            if ($module) {
                $module->warning[] = $module->l(
                    'Redis server must be configured before using this module.'
                );
            }
            return false;
        }
        return false;
    }

    /**
     * Returns the default configuration for this module
     *
     * @param array $key Configuration key
     *
     * @return array Default configuration
     */
    public static function getDefaultConfig($key = false)
    {
        $defaults = array(
            'PS_REDIS_STATUS' => 0,
            'PS_REDIS_DISABLED_PATHS' => '',
            'PS_REDIS_PERSISTENT' => 0,
            'PS_REDIS_SERIALIZER' => self::isRedisInstalled() ? Redis::SERIALIZER_PHP : 0,
            'PS_REDIS_PREFIX' => 'ps_',
            'PS_REDIS_PASSWORD' => '',
            'PS_REDIS_CONNECTION' => '127.0.0.1:6379',
            'PS_REDIS_DB' => 0,

            'PS_REDIS_FPC' => 0,
            'PS_REDIS_CUSTOM_CONFIG' => '',
            'PS_REDIS_CACHE_OBJECT_INDEX' => 1,
            'PS_REDIS_CACHE_OBJECT_PRODUCTS' => 1,
            'PS_REDIS_CACHE_OBJECT_CATEGORIES' => 1,
            'PS_REDIS_CACHE_OBJECT_CMS' => 1,
            'PS_REDIS_CACHE_OBJECT_CONTACT' => 1,
            'PS_REDIS_CACHE_OBJECT_STORES' => 1,
            'PS_REDIS_CACHE_OBJECT_SITEMAP' => 0,
            'PS_REDIS_CACHE_OBJECT_DB_QUERIES' => 0,

            'PS_REDIS_SESSION_CACHE_STATUS' => 0,
            'PS_REDIS_SESSION_CONNECTION' => '127.0.0.1:6379',
            'PS_REDIS_SESSION_PASSWORD' => '',
            'PS_REDIS_SESSION_DB' => 0,
        );

        if (!$key) {
            return $defaults;
        }

        return isset($defaults[$key]) ? $defaults[$key] : false;
    }

    /**
     * Converts configurations to proper data types.
     *
     * @param array $configs
     * @return array
     */
    protected static function configurationValueMassage($configs)
    {
        $types = array(
            'PS_REDIS_STATUS' => 'int',
            'PS_REDIS_SERIALIZER' => 'int',
            'PS_REDIS_PERSISTENT' => 'int',
            'PS_REDIS_FPC' => 'int',
            'PS_REDIS_CACHE_OBJECT_INDEX' => 'int',
            'PS_REDIS_CACHE_OBJECT_PRODUCTS' => 'int',
            'PS_REDIS_CACHE_OBJECT_CATEGORIES' => 'int',
            'PS_REDIS_CACHE_OBJECT_CMS' => 'int',
            'PS_REDIS_CACHE_OBJECT_CONTACT' => 'int',
            'PS_REDIS_CACHE_OBJECT_STORES' => 'int',
            'PS_REDIS_CACHE_OBJECT_SITEMAP' => 'int',
            'PS_REDIS_CACHE_OBJECT_DB_QUERIES' => 'int',
            'PS_REDIS_SESSION_CACHE_STATUS' => 'int',
            'PS_REDIS_DB' => 'int',
        );

        $config = array();

        foreach ($configs as $conf) {
            $key = $conf['key'];
            $value = $conf['value'];
            if (isset($types[$key]) && $types[$key] == 'int') {
                $config[$key] = (int)$value;
            } elseif (isset($types[$key]) && $types[$key] == 'bool') {
                $config[$key] = (bool)$value;
            } else {
                $config[$key] = trim($value);
            }
        }
        return $config;
    }

    /**
     * Retrieves the configuration directly from the database.
     * This method is necesarry because the "Configuration"
     * methods are cached.
     *
     * @param string $key
     * @return mixed
     */
    public static function getConfigSql($key = false)
    {
        $config = array();

        $sql = 'SELECT `key`, `value`
            FROM `'._DB_PREFIX_.'redis_config`';

        $db = Db::getInstance();

        $conditions = '';
        $keys = $key ? [$key] : self::getConfigKeys();

        foreach ($keys as $config_key) {
            $conditions .= ' WHERE `key` = "'. pSQL($config_key) . '"';
        }

        $sql .= $conditions;

        try {
            $results = $db->executeS(
                $sql,
                1,
                0
            );
        } catch (\Exception $e) {
            $results = array();
        }


        if (empty($results)) {
            return self::getDefaultConfig($key);
        }

        $config = self::configurationValueMassage($results);

        if ($key) {
            return $config[$key];
        }

        return $config;
    }

    /**
     * Gets a specific module configuration or all configurations
     *
     * @param array $key Configuration key
     *
     * @return array Module configuration
     */
    public static function getConfig($key = false)
    {
        $config = self::getDefaultConfig();

        if (!$key) {
            $config_keys = self::getConfigKeys();
            $password_keys = array(
                'PS_REDIS_PASSWORD',
                'PS_REDIS_SESSION_PASSWORD'
            );
            foreach ($config_keys as $config_key) {
                if (in_array($config_key, $password_keys)) {
                    if (self::getConfigSql($config_key) != '' && Tools::getValue($config_key) == '') {
                        $config[$config_key] = self::getConfigSql($config_key);
                        continue;
                    }
                }
                $config[$config_key] = Tools::getValue($config_key, self::getConfigSql($config_key));
            }
        } else {
            $config[$key] = Tools::getValue($key, self::getConfigSql($key));
        }

        return $key ?
            (isset($config[$key]) ? $config[$key] : false)
            : $config;
    }

    /**
     * Saves a configuration value for the module.
     *
     * @param string $key   The configuration key
     * @param mixed  $value The configurtion value
     *
     * @return bool          Returns true on success, false on failure.
     */
    public static function saveConfig($key, $value)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'redis_config`';
        $sql .= ' (`key`, `value`) ';
        $sql .= ' VALUES (\''. pSQL($key).'\', \'' . pSQL($value) . '\') ';
        $sql .= ' ON DUPLICATE KEY UPDATE ';
        $sql .= ' value=\'' . pSQL($value) . '\'';

        $db = Db::getInstance();
        try {
            $result = $db->execute($sql, 1, 0);
        } catch (\Exception $e) {
            $result = false;
        }


        return $result;
    }

    /**
     * Saves multiple configurations at once
     *
     * @param array $configs The configuration array
     *
     * @return bool           Returns true on success, false on failure.
     */
    public static function saveMultipleConfigs($configs)
    {
        $saved = true;

        foreach ($configs as $key => $value) {
            $saved = $saved && self::saveConfig($key, $value);
        }

        return $saved;
    }

    /**
     * Deletes a configuration for the module
     *
     * @param string $key The configuration key
     *
     * @return bool        Returns true on success, false on failure.
     */
    public static function deleteConfig($key)
    {
        /**
         * @todo: Implement.
         * Not needed for now. The uninstall hook drops the configuration table.
         */
        return $key;
    }

    /**
     * Deletes multiple configurations at once
     *
     * @param array $keys Multiple configuration keys
     *
     * @return bool        Returns true on success, false on failure.
     */
    public static function deleteMultipleConfigs($keys)
    {
        $status = true;

        foreach ($keys as $key) {
            $status = $status && self::deleteConfig($key);
        }

        return $status;
    }

    /**
     * Overrides the caching settings of the store.
     *
     * @param int $cacheEnable
     * @return void
     */
    public static function overrideDefaultCaching($cacheEnable)
    {
        if (BackwardCompatibility::overrideDefaultCaching($cacheEnable)) {
            return ;
        }

        $parameters_file = _PS_CORE_DIR_ . '/app/config/parameters.php';
        $app_parameters_file = _PS_CACHE_DIR_ . '/appParameters.php';

        $data = file($parameters_file);

        foreach ($data as $key => $value) {
            $arr = explode(' => ', $value);
            switch ($arr[0]) {
                case "    'ps_caching'":
                    if ($cacheEnable == 1) {
                        $arr[1] = "'CacheRedis',\n";
                    } else {
                        $arr[1] = "'CacheMemcache',\n";
                    }
                    $data[$key] = implode(" => ", $arr);
                    break;
                case "    'ps_cache_enable'":
                    $arr[1] = ' _PS_MODE_DEV_ ? 0 : ' . $cacheEnable . ",\n";
                    $data[$key] = implode(" => ", $arr);
                    break;
                default:
                    break;
            }
        }

        try {
            file_put_contents($parameters_file, $data);
            file_put_contents($app_parameters_file, $data);
        } catch (\Exception $e) {
        }
    }

    /**
     * Get unique key for current request
     *
     * @return string
     */
    public static function getKey($context)
    {
        $keyTags = implode("-", [
            filter_input(INPUT_SERVER, "REQUEST_URI"),
            (int) $context->country->id,
            (int) $context->getDevice()
        ]);
        $key = 'OBJCACHE:' . md5($keyTags);
        return $key;
    }

    /**
     * Store response
     *
     * @param string $html
     * @return mixed
     */
    public static function store($key, $html)
    {
        $redis = self::getConnection();
        $reponse = sprintf("<!-- cached on %s -->\n%s", date("Y-m-d H:i:s"), $html);
        try {
            return $redis->set($key, [
                'html' => $reponse,
                'hash' => md5($html),
            ], 3600);
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Strips the prefix from the cache key.
     * Since this prefix is re-added by the Redis class we need to "massage" this key.
     *
     * @param string $key
     * @return string
     */
    public function stripPrefix($key)
    {
        $prefix = self::getConfig('PS_REDIS_PREFIX');
        return str_replace($prefix, '', $key);
    }

    /**
     * Clears the Page Cache.
     *
     * @return void
     */
    public static function flushFPC()
    {
        $redis = self::getConnection();

        $keys = $redis->keys('*OBJCACHE:*');
        if (empty($keys)) {
            return false;
        }

        $keys = array_map('self::stripPrefix', $keys);

        return $redis->del($keys);
    }

    /**
     * Gets the cached response
     *
     * @return mixed
     */
    public static function load($key)
    {
        $redis = self::getConnection();
        return $redis->get($key);
    }

    /**
     * Builds the session save path from the configuration.
     *
     * @return string
     */
    public static function buildSessionSavePath()
    {
        $configs = self::getConfig();

        $save_path = 'tcp://' . $configs['PS_REDIS_SESSION_CONNECTION'];
        $session_params = array();
        $session_params[] = 'weight=1';
        $session_params[] = 'timeout=2.5';
        $session_params[] = 'persistent=0';

        if (!empty($configs['PS_REDIS_SESSION_PASSWORD'])) {
            $session_params[] = 'auth=' . $configs['PS_REDIS_SESSION_PASSWORD'];
        }

        if (!empty($configs['PS_REDIS_SESSION_DB'])) {
            $session_params[] = 'database='. $configs['PS_REDIS_SESSION_DB'];
        }

        $save_path .= '?' . implode('&', $session_params);

        return $save_path;
    }
}
