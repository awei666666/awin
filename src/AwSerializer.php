<?php
/**
 * 缓存串行器  解决缓存失效，大量请求压到db层。
 */
namespace Awin\Xianliu;

use Redis;

class AwSerializer
{
    /**
     * @var Redis $awredis
     */
    private static $awredis;
    private static $awslef;
   
    /**
     * 单例
     *
     * @param Redis $reids
     * @return AwSerializer
     */
    public static function getSingleton($reids)
    {
        if (empty(self::$awredis)) {
            self::$awredis = $reids;
        }
        if (empty(self::$awslef)) {
            self::$awslef = new self();
        }
        return self::$awslef;
    }

    /**
     * 获取redis缓存
     *      解决缓存击穿问题。
     * @param string $name  缓存key的名称
     * @param function $callback  获取数据的回调方法,有数据则缓存。没有则不缓存
     * @param string $key  存入集合的名称。一个缓存类型一个名称，或者不设置也是可以的
     * @param integer $frequency  重试次数
     * @return string/bool
     */
    public function get($name, $callback, $setCallback=null, $key='sadd', $frequency=3)
    {
        $data = self::$awredis->get($name);
        if (!empty($data)) {
            return $data;
        }

        $res = self::$awredis->sAdd($key, $name);
        if ($res) {
            $data = $callback();
            if(is_array($data)){
                $data = json_encode($data);
            }
            if(empty($setCallback)){
                self::$awredis::set($name, $data);
            }else{
                $setRes = $setCallback($name, $data, self::$awredis);
            }
            self::$awredis->sRem($key, $name);
        } else {
            $data = $this->getData($name, $frequency);
        }
        return $data;
    }

    /**
     * 按次取数据
     *
     * @param string $name
     * @param int $frequency
     * @return string/int
     */
    public function getData($name, $frequency)
    {
        $frequency_tmp = 0;
        get:
        sleep(1);
        $data = self::$awredis->get($name);
        if (empty($data)) {
            $frequency_tmp++;
            if ($frequency_tmp < $frequency) {
                goto get;
            }
        }
        return $data;
    }






    private function __clone()
    {
    }

    private function __construct()
    {
    }
}
