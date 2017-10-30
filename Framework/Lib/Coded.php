<?php
namespace Framework\Lib;
class Coded{
    //@ unicode 解码
    public function unicode_decode($string, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
        preg_match_all('/'.$prefix.'(\d+)'.$postfix.'/isU' , $string , $pexArr);
        foreach($pexArr[1] as $pex){
            $temp     = intval( $pex );
            $unistr   = ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
            $new_char = iconv('UCS-2', $encoding , $unistr);
            $string = str_replace($prefix.$pex.$postfix , $new_char , $string);
        }
        return $string;
    }
}
?>