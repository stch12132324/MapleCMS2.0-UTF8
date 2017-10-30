<?php
namespace App\Service;
class Common{
    /**
     * array数组类型转码
     * @param $val
     * @return array|string
     */
    public static function iconvArray($val){
        if(is_array($val)){
            foreach($val as $key => $v){
                $val[$key] = self::iconvArray($v);
            }
            return $val;
        }else{
            return iconv('gbk' , 'utf-8', $val);
        }
    }

    /**
     * object类型数组转码
     * @param $val
     * @return string
     */
    public static function iconvObject($val){
        if( is_object($val) ){
            foreach($val as $key => $v){
                $val->$key = self::iconvObject($v);
            }
            return $val;
        }else{
            return iconv('gbk' , 'utf-8', $val);
        }
    }
    /**
     * @获取用户头像
     *
     * @param $uid
     * @param string $type
     * @return string
     */
    public static function getAvatarUrl($uid , $type = 'small'){
        $uid   = sprintf("%09d", $uid);
        return 'http://uc.ltaaa.com/uc_server/data/avatar/'.substr($uid, 0, 3).'/'.substr($uid, 3, 2).'/'.substr($uid, 5, 2).'/'.substr($uid, -2).'_avatar_'.$type.'.jpg';
    }
}