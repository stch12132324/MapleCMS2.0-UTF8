<?php
namespace Framework\Lib;
class MyException extends Exception{
    public function __construct($error = '' , $type = 'common'){
        parent::__construct();
        switch($type){
            case 'common':
                $this->commonError($error);
            break;
            case 'weChat':
                $this->weChatError($error);
            break;
        }
    }

    /*
     * common 异常
     */
    public function commonError($error = ''){
        echo $error;
    }

    /*
    * weChat 部分异常
     */
    public function weChatError($error = ''){
        echo '微信接口异常，异常代码：'.$error;
    }
}
?>