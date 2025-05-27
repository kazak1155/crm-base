<?php

/**
 * MemoryCache class for caching data in memory using a set of different extensions when available.
 *
 * Supported Extensions: memcache, wincache (using WinCache class)
 *
 * @author Rudi Visser <rudiv.se>
 * @copyright Rudi Visser 2011-2012
 * @version 1.0
 */
class MemoryCache
{
    const ENGINE_MEMCACHE = 0;
    const ENGINE_WINCACHE = 1;

    public $var_prefix = '';
    protected $_engine;
    protected $_backend;

    public function __construct($namespace = 'base')
    {
        $this->var_prefix = $namespace . '_';

        // Favour WinCache..
        if (extension_loaded('wincache') && class_exists('WinCache')) {
            $this->_engine = self::ENGINE_WINCACHE;
            $this->_backend = new WinCache();
        } else if (extension_loaded('memcache')) {
            $this->_engine = self::ENGINE_MEMCACHE;
            $this->_backend = new Memcache();
        } else {
            throw new Exception('There are no persistent in-memory storage engines available.');
        }
    }

    public function __isset($name)
    {
        $name = $this->var_prefix . $name;
        switch ($this->_engine) {
            case self::ENGINE_WINCACHE:
                return $this->_backend->exists($name);
            case self::ENGINE_MEMCACHE:
                return $this->_backend->get($name) !== false && $this->_backend->get($name) !== null;
        }
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            $name = $this->var_prefix . $name;
            return $this->_backend->get($name);
        }
    }

    public function __set($name, $value)
    {
        $name = $this->var_prefix . $name;
        $this->_backend->set($name, $value);
    }

    public function __unset($name)
    {
        $name = $this->var_prefix . $name;
        $this->_backend->delete($name);
    }

    private static $_instances = array();
    public static function getNamespace($namespace = 'base')
    {
        if (empty($namespace)) {
            $namespace = 'base';
        }
        if (!isset(self::$_instances[$namespace]))
            self::$_instances[$namespace] = new self($namespace);

        return self::$_instances[$namespace];
    }
}

/**
 * The Wincache class is a compatible API to the Memcache extension, but for Wincache extension on Windows Server platform instead.
 *
 * This class should be a drop in replacement for any basic usage of the Memcache class, and does not aim to provide additional functionality. Instead, you can use
 * the MemoryCache class for a more natural persistent storage class.
 *
 * Limitations:
 * - As mentioned above, this only supports Memcache and NOT Memcached!!
 * - Will only support the emulation of a single server
 * - get*Stats are not compatible with memcache stats but still valid calls
 *
 * @author Rudi Visser <rudiv.se>
 * @copyright Rudi Visser 2012
 * @version 0.2
 */
class WinCache
{
    public function add(/*string*/ $key, /*mixed*/ $value, $flags = null, /*int*/ $expiration = 0)
    {
        // Similar to set() but fails if key already exists
        return wincache_ucache_add($key, $value, $expiration);
    }

    public function addServer($host, $port = 11211, $persistent = false, $weight = null, $timeout = 0, $retry_interval = 0, $status = null, $failure_callback = null, $timeoutms = 0)
    {
        // We pretend that this succeeds
        return true;
    }

    public function close()
    {
        return true;
    }

    public function connect($host, $port = 11211, $timeout = 0)
    {
        // Pretend that this succeeds
        return true;
    }

    public function decrement($key, $value = 1)
    {
        return wincache_ucache_dec($key, $value);
    }

    public function delete($key)
    {
        return wincache_ucache_delete($key);
    }

    public function flush()
    {
        wincache_ucache_clear();
    }

    public function get($key, $flags = null)
    {
        return wincache_ucache_get($key);
    }

    public function getExtendedStats($type = null, $slabid = null, $limit = 100)
    {
        return wincache_ucache_info();
    }

    public function getServerStatus()
    {
        return wincache_ucache_info();
    }

    public function getStats()
    {
        return wincache_ucache_meminfo();
    }

    public function getVersion()
    {
        return '0.2';
    }

    public function increment($key, $value = 1)
    {
        return wincache_ucache_inc($key, $value);
    }

    public function exists($key)
    {
        return wincache_ucache_exists($key);
    }

    public function pconnect($host, $port = 11211, $timeout = 0)
    {
        return true;
    }

    public function replace($key, $value, $flag = null, $expiration = 0)
    {
        if (!wincache_ucache_exists($key))
            return false;
        wincache_ucache_set($key, $value, $expiration);
    }

    public function set($key, $var, $flag = null, $expiration = 0)
    {
        return wincache_ucache_set($key, $var, $expiration);
    }

    public function setCompressThreshold($threshold, $min_savings)
    {
        return true;
    }

    public function setServerParams($host, $port = 11211, $timeout = null, $retry_interval = false, $status = null, $failure_callback = null)
    {
        return true;
    }
}
