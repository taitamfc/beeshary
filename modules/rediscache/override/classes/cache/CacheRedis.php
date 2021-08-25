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

include_once _PS_ROOT_DIR_ . '/modules/rediscache/classes/RedisHelper.php';

class CacheRedis extends Cache
{
    /**
     * The redis object
     *
     * @var object
     */
    protected $redis = false;

    /**
    * @var array List of blacklisted tables for SQL cache, these tables won't be indexed
    */
    protected $redisBlacklist = array();

    /**
     * Initializes the connection to the Redis server
     */
    public function __construct()
    {
        $this->blacklist = array_merge($this->blacklist, $this->redisBlacklist);
        $this->redis = false;
        try {
            if (RedisHelper::getConfig('PS_REDIS_CACHE_OBJECT_DB_QUERIES')
                && RedisHelper::getConfig('PS_REDIS_STATUS') !== 0
                && !$this->isBlacklistedController()
                && !RedisHelper::bypass()) {
                $this->redis = RedisHelper::getConnection();
            }
        } catch (\Exception $e) {
        }


        // @todo: Session cache management is will be removed in the next version.
        // try {
        //     if (RedisHelper::getConfig('PS_REDIS_PHP_SESSION') !== 0) {
        //         $session_save_path = RedisHelper::buildSessionSavePath();
        //         if (RedisHelper::testConnection($session_save_path)) {
        //             ini_set('session.save_handler', 'redis');
        //             ini_set('session.save_path', $session_save_path);
        //         }
        //     }
        // } catch (\Exception $e) {
        // }
    }

    /**
     * Checks if a controller is blacklisted.
     *
     * @param string $controller
     * @return boolean
     */
    protected function isBlacklistedController($controller = false)
    {
        $controllerBlacklist = array(
            'order' => true,
        );

        if (!$controller) {
            $controller = Tools::getValue('controller');
        }

        return isset($controllerBlacklist[$controller]);
    }

    /**
     * Checks if the connection is persistent.
     * @todo: Maybe move this to RedisHelper?
     *
     * @return boolean
     */
    protected function isPersistentConnection()
    {
        return RedisHelper::getConfig('PS_REDIS_PERSISTENT') === 1;
    }

    /**
     * Checks if there is an established connection to the redis server.
     * @todo: Maybe move this to RedisHelper?
     *
     * @return boolean
     */
    protected function isConnected()
    {
        return $this->redis->isConnected();
    }

    /**
     * Sets a cachable data with the specified key
     *
     * @param string  $key   Cache key
     * @param mixed   $value Cache value
     * @param integer $ttl   Time to live
     *
     * @return bool True on success False on failure.
     *
     * @see Cache::_set()
     */
    protected function _set($key, $value, $ttl = null)
    {
        if (!$this->redis) {
            return false;
        }

        $ttl = isset($ttl) ? $ttl : 3600;

        $success = $this->redis->set($key, $value, ['nx', 'ex' => $ttl]);

        if ($success === false) {
            //$this->setAdjustTableCacheSize(true);
        }

        return $success;
    }

    /**
     * The public set method
     *
     * @param string  $key   Cache key
     * @param mixed   $value Cache value
     * @param integer $ttl   Time to live
     *
     * @return bool True on success False on failure.
     *
     * @see Cache::set()
     */
    public function set($key, $value, $ttl = null)
    {
        $this->_set($key, $value, $ttl);
    }

    /**
     * Retrieves a cached data by key
     *
     * @param string $key Cache key
     *
     * @return mixed Cache value
     *
     * @see Cache::_get()
     */
    protected function _get($key)
    {
        if (!$this->redis) {
            return false;
        }

        $data = $this->redis->get($key);

        return $data;
    }

    /**
     * The public get method
     *
     * @param string $key Cache key
     *
     * @return mixed Cache value
     *
     * @see Cache::get()
     */
    public function get($subkey)
    {
        if (!$this->redis) {
            return false;
        }
        return $this->_get($subkey);
    }

    /**
     * Checks if a cache key exists
     *
     * @param string $key Cache key
     *
     * @return bool Returns true if the key exists, else returns false.
     *
     * @see Cache::_exists()
     */
    protected function _exists($key)
    {
        if (!$this->redis) {
            return false;
        }

        return $this->redis->exists($key);
    }

    /**
     * The public exists method
     *
     * @param string $key Cache key
     *
     * @return bool Returns true if the key exists, else returns false.
     *
     * @see Cache::exists()
     */
    public function exists($key)
    {
        return $this->_exists($key);
    }

    /**
     * Deletes a cached data by key
     *
     * @param string $key Cache key
     *
     * @return bool Returns true on success, else returns false.
     *
     * @see Cache::_delete()
     */
    protected function _delete($key)
    {
        if (!$this->redis) {
            return array();
        }

        return $this->redis->del($key);
    }

    /**
     * The public _deleteMulti() method.
     *
     * @param array $keyArray
     */
    public function deleteMulti(array $keyArray)
    {
        $this->_deleteMulti($keyArray);
    }

    /**
     * Delete several keys at once from the cache.
     *
     * @param array $keyArray
     */
    protected function _deleteMulti(array $keyArray)
    {
        if (!$this->redis) {
            return array();
        }

        $this->redis->delete($keyArray);
    }

    /**
     * The public delete method
     *
     * @param string $key Cache key
     *
     * @return bool Returns true on success, else returns false.
     *
     * @see Cache::_delete()
     */
    public function delete($key)
    {
        RedisHelper::flushFPC();

        if (!$this->redis) {
            return array();
        }

        return $this->_delete($key);
    }

    /**
     * Writes the cache keys to the database
     *
     * @return bool Returns true on success, else returns false.
     *
     * @see Cache::_writeKeys()
     */
    protected function _writeKeys()
    {
        if (!$this->redis) {
            return false;
        }
        return true;
    }

    /**
     * Removes all cached data
     *
     * @return bool Always true
     *
     * @see Cache::flush()
     */
    public function flush()
    {
        if (!$this->redis) {
            return false;
        }
        return $this->redis->flushAll();
    }
}
