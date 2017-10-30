<?php
namespace Framework\Provider;
class Redis{
    private static $redis;
    /*
    * @ Redis Base 部分
    */
    public static function getRedis(){
        if(!is_object(self::$redis)){
            self::$redis = new \Framework\Lib\MyRedis();
        }
        return self::$redis;
    }
}
?>