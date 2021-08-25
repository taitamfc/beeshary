<?php

/**
 * Class Inix2Config
 */
class Inix2Config
{

    private static $config = array();

    private static $configFile = '';

    /**
     *
     */
    public static function load()
    {

        self::$configFile = _PS_MODULE_DIR_ . 'inixframe/config/config.data';

        $config = unserialize(Tools::file_get_contents(self::$configFile));

        if (!is_array($config)) {
            self::$config = array();
        } else {
            self::$config = $config;
        }
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return bool|null|string
     */
    public static function get($key, $default = null)
    {
        if (!self::$config) {
            self::load();
        }

        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }


        if (($val = Configuration::get($key)) !== false) {
            self::put($key, $val);
            Configuration::deleteByName($key);

            return $val;
        }
        if ($default) {
            return $default;
        }

        return false;

    }

    /**
     * @return array
     */
    public static function getAll()
    {
        if (!self::$config) {
            self::load();
        }

        return self::$config;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function put($key, $value)
    {
        self::$config[$key] = $value;
        self::write();
    }

    /**
     * @param $key
     */
    public static function delete($key)
    {
        if (isset(self::$config[$key])) {
            unset(self::$config[$key]);
        }

        @Configuration::deleteByName($key);

        self::write();
    }

    /**
     *
     */
    public static function write()
    {
        self::$configFile = _PS_MODULE_DIR_ . 'inixframe/config/config.data';
        file_put_contents(self::$configFile, serialize(self::getAll()));
    }
}
