<?php
namespace Framework\Core;
class Controller extends Base{
    public  $_action;	// app.php 初始化时候就会注入
    public  $_module;	// 同上
    public  $_group;		// 同上
    public  $_method;
	private $_aGVal = array();
	public function __construct(){

	}
	public function beforeAction(){}
	public function afterAction(){}
    /*
    * 渲染模板tpl  name dir group
    */
	public function display($_tplName = '' , $_tpfile = '' , $_tpgroup = ''){
		if(is_array($this->_aGVal)) extract($this->_aGVal);
		if(is_array($this->CONFIG_LIST)) extract($this->CONFIG_LIST);
		$action = $this->_action;
		$module = $this->_module;
        $group  = $_tpgroup == '' ? $this->_group : $_tpgroup;
		if($_tplName==''){
			include template($action , $module , $group);
		}else{
            if($_tpfile != ''){
		        include template($_tplName , $_tpfile , $group);
            }else{
                include template($_tplName , $module , $group);
            }
		}
	}

    /*
    * @注入模板参数 key-value 或者 array
    * # key-value 模式
    * assign('name' , 'lubi');
    *
    * # array模式
    * $user = array(
    *   'username' => $username,
    *   'age'      => $age
    * );
    * assign('user' , $user);
    */
	public function assign($key = '' , $val = ''){
        if( is_array($key) ){
            foreach($key as $keys => $vals){
                $this->_aGVal[$keys] = $vals;
            }
            unset($key);
        }else{
		    $this->_aGVal[$key] = $val;
		    unset($key,$val);
        }
	}
}
?>