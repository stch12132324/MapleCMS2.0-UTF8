<?php
namespace Framework\Provider;
class DB{
    private static $dbObj;  // 数据库操作默认对象
    private static $dbObj2; // 多数据库时候备用
    /**
     * @主数据库
     * @return \Framework\Lib\Database
     */
    public static function getDb(){
        if( !is_object(self::$dbObj) ){
            self::$dbObj = new \Framework\Lib\Database( 'default' );
        }
        return self::$dbObj;
    }

    /**
     * @分数据库
     * @return \Framework\Lib\Database
     */
    public static function getDb2(){
        if( !is_object(self::$dbObj2) ){
            self::$dbObj2 = new \Framework\Lib\Database( 'uc' );
        }
        return self::$dbObj2;
    }
}
?>