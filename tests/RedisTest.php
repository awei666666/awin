<?php 

namespace Awin\Test;

use Awin\Xianliu\AwinRedis;

class RedisTest  
{
    public function setGet()
    {
        
        $config['HOST'] = '192.168.33.10';
        $config['AUTH'] = 'redis';
        $config['port'] = '6379';
        $config['db'] = '1';
        $config['ttl'] = 900;
        $obj = AwinRedis::getSingleton($config);

        $obj->hIncrBy('abch', 9999, 1);

        $res  = $obj->sAdd('abc', 'abc');
        print_r($res);
        exit;
    
        

        // 使用过滤器，会统一给设置过期时间。而且会有过期随机时间。防止缓存雪崩发生
        $obj::set('a', 8);
        // 直接调用reids方法
        $obj->set('a', 8);
        return $obj->get('a');
    }


    public function setGet_old()
    {
        
        $config['HOST'] = '192.168.33.10';
        $config['AUTH'] = '';
        $config['port'] = '6378';
        $config['db'] = '0';
        $obj = AwinRedis::getSingleton($config);

        $obj->set('a', 8);
        // $obj->expire();
        return $obj->get('a');
    }
}
