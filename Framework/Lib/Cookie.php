<?php
namespace Framework\Lib;
class Cookie{
    public function clearAll(){
        $cookieArray = $_COOKIE;
        foreach($cookieArray as $key=>$cookieName){
            setcookie($key , "" , time() - 3600 ,'/' , 'ltaaa.com');
        }
    }
    public function clearByArray( $cookieArray = array() ){
        foreach($cookieArray as $key){
            setcookie( $key , "" , time() - 3600 ,'/' , 'ltaaa.com');
        }
    }
}
?>