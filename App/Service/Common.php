<?php
namespace App\Service;
class Common{
    /**
     * array��������ת��
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
     * object��������ת��
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
     * @��ȡ�û�ͷ��
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