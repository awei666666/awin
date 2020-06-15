<?php 


namespace Awin\Test;

use Awin\Xianliu\AwinRedis;
use Awin\Xianliu\AwLimiter;

class LimitTest  
{

    public function index()
    {
           
        $config['HOST'] = '192.168.33.10';
        $config['AUTH'] = 'redis';
        $config['port'] = '6379';
        $config['db'] = '1';
        $config['ttl'] = 900;
        $r = AwinRedis::getSingleton($config);
        $field = $r->hGet('abch', 9999);
        var_dump($field);
        exit;
        $obj = new AwLimiter($r);
    }
}