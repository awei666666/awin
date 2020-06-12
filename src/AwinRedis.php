<?php
namespace Awin\Xianliu;

class AwinRedis
{
    public $host = '192.168.33.10';
    public $port = '6378';
    public $auth = '';
    public $db = 8;
    public $ttl = 900;
    private static $redis  ;
    private static $awredis  ;


    /**
     * 单例
     *
     * @return \Redis
     */
    public static function getSingleton($config = [])
    {
        if (empty(self::$awredis)) {
            self::$awredis = (new self());
        }
        if (!empty($config)) {
            $obj = self::$awredis->setConfig($config);
            self::$redis = $obj->getRedis();
        }
        if (empty(self::$redis)) {
            self::$redis = self::$awredis->getRedis();
        }
        return self::$awredis;
    }

    public function awsetex($params)
    {
        $ttl = rand(10, 99);
        $params[1] = $params[1]+$ttl;
        return call_user_func_array([self::$redis, 'setex'], $params);
    }

    public function awexpire($params)
    {
        $ttl = rand(10, 99);
        $params[1] = $params[1]+$ttl;
        return call_user_func_array([self::$redis, 'expire'], $params);
    }

    public function awset($params)
    {
        $ttl = rand(10, 99);
        $par[0] = $params[0];
        $par[1] = self::$awredis->ttl + $ttl;
        $par[2] = $params[1];
        return call_user_func_array([self::$redis, 'setex'], $par);
    }

    /**
     * 静态调用reids的方法.用来增加缓存的随机时间。防止缓存雪崩的发生；
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public static function __callstatic($name, $params)
    {
        if (in_array($name, ['setex','expire','set'])) {
            $name = 'aw'.$name;
            return  self::$awredis->{$name}($params);
        }
        return call_user_func_array([self::$redis, $name], $params);
    }

     /**
     * 调用reids方法
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public function __call($name, $params)
    {
        return call_user_func_array([self::$redis, $name], $params);
    }

    /**
     * 设置配置信息
     *
     * @param array $config
     * @return \Redis
     */
    public function setConfig($config)
    {
        if (!empty($config)) {
            foreach ($config as $k => $v) {
                $k = strtolower($k);
                if ('password' == $k) {
                    $k = 'auth';
                }
                if ('select' == $k) {
                    $k = 'db';
                }
                self::$awredis->{$k} = $v;
            }
        }
        return self::$awredis;
    }

    /**
     * 设置配置信息
     *
     * @return \Redis
     */
    public function getRedis()
    {
        $redis = new \Redis();
        $redis->connect($this->host, $this->port);
        if (!empty($this->auth)) {
            $redis->auth($this->auth);
        }
        $redis->select($this->db);
        return $redis;
    }



    private function __clone()
    {
    }

    private function __construct()
    {
    }

        
   
}
