<?php 
namespace Awin\Xianliu;

use Redis;

class AwLimiter
{

    /**
     * redis
     *
     * @var Redis
     */
    private $awredis;
    public $error = '没有错误';
    /**
     * Class constructor.
     */
    public function __construct($reids)
    {
        $this->awredis = $reids;
    }

    /**
     * 令牌限流
     *
     * @param string $key  用户标识
     * @param string $windowSecond  窗口时间
     * @param string $maxVisitTimes  最大请求时间
     * @return int/false
     */
    public function limit_token($key, $windowSecond = 10, $maxVisitTimes=5)
    {
        $redis = $this->awredis;
        //获取微妙时间戳
        list($msec, $sec) = explode(' ', microtime());
        $time = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        //开启事务
        $redis->multi();
        //删除之前没用的访问数据
        $redis->zRemRangeByScore($key, 0, ($time - ($windowSecond * 1000)));
        //增加这次的访问记录
        $redis->zAdd($key, $time, $time.rand(1111, 9999));
        //设置有效期
        $redis->expire($key, $windowSecond);
        //执行事物
        $redis->exec();
        // 判断是否拥有token。
        $count =  $this->awredis->zCard($key);
        if($count > $maxVisitTimes){
            $this->error = '访问超出限制';
            return false;
        }
        return $time;
    }


    /**
     * 滴水限流
     *
     * @param string $key
     * @param integer $windowSecond
     * @param string $hash_key
     * @return int/false
     */
    public function limit_drip($key, $windowSecond=10, $hash_key='limit_drip')
    {
        $redis = $this->awredis;
        //获取微妙时间戳
        list($msec, $sec) = explode(' ', microtime());
        $time = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        $old_time = $redis->hGet($hash_key, $key);
        if(($time-$old_time) < ($windowSecond * 1000)){
            $this->error = '时间还未到';
            return false;
        }
        $redis->hSet($hash_key, $key, $time);
        return $time;
    }

}