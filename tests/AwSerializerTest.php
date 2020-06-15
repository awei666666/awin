<?php 


namespace Awin\Test;

use Awin\Xianliu\AwinRedis;
use Awin\Xianliu\AwSerializer;

class AwSerializerTest
{
    public function get()
    {
        $config['HOST'] = '192.168.33.10';
        $config['AUTH'] = 'redis';
        $config['port'] = '6379';
        $config['db'] = '1';
        $config['ttl'] = 900;
        $obj = AwinRedis::getSingleton($config);

        $sobj = AwSerializer::getSingleton($obj);
        //最简单的使用，会调用默认设置的ttl的时间+随机时间。防止缓存雪崩
        $data = $sobj->get('name', function(){
            $res = ['test'=>111];
            return $res;
        });

     
        //数据可以自定义缓存。设置有效期。
        // $data = $sobj->get('name', function(){
        //     $res = ['test'=>111];
        //     return ($res);
        // }, function($name, $value, $redis){
        //     $redis::setex($name, 100, ($value));
        //     return 555;
        // });

        
        // 其他不常用的地方
        // 第四个参数是 串行器名称。一个类型缓存一个名称。可以不修改
        // 第5个参数是重试次数。如果发生串行，重试几次后将不在获取。
        // $data = $sobj->get('name', function(){
        //     $res = ['test'=>111];
        //     return $res;
        // },null, '重新设置一个名称', 7);

        var_dump($data);
        exit;
        $res  = $obj->sAdd('abc', 'abc');
    }
}