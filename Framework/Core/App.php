<?php
namespace Framework\Core;
class App{
	//@ 主方法
	static public function run(){
		if( PATH_INFO == ''){
			// nginx
			$_args = trim( $_SERVER['DOCUMENT_URI'] , "/");
			$_args = explode("/" , $_args);
			$_args = $_args[1];
		}else{
			$_args = trim($_SERVER['PATH_INFO'],"/");
		}
        //@ 常规路由
        $_args = explode("/",$_args);
        // 分组
        list($_group, $_groupVal)			=  	self::_initGroup($_args);
        // 获取module和action
        list($_module, $_action)  			=  	self::_initController($_args);
        // 路由验证
        list($_module_name, $_module_file)	=	self::_initRouterVerify($_module, $_groupVal);
        // 获取GET参数
        self::_initGet($_args);
        // 自定义路由 - 能等待改进
        self::_initRouter($_module, $_action);
        self::_makeRun($_group , $_module , $_action , $_module_name , $_module_file);
	}

    private static function _makeRun( $_group , $_module , $_action , $_module_name , $_module_file ){
        // 语言包
		self::_initLang();
        $controler = '\App\Controller\\'.$_module_name;
		$act = new $controler;
		if( method_exists($act,$_action) ){
            self::_initRun($act, $_group, $_module, $_action);
		}else{
			// 异常action 处理
			abort(404);
		}
    }
	
//--------------------------------- 可配置 --------------------------

	//@ 自定义路由
	private static function _initRouter(&$_module, &$_action){

	}
	
	//@ Action 运行  / 初始化Controller后 到 运行Action之间运行事件
	private static function _initRun($act, $_group, $_module, $_action){
		//@ Action 基类注入
		$_module      = lcfirst($_module);
		$act->_action = $_action;
		$act->_module = $_module;
		$act->_group  = $_group;
        $act->_method = $_SERVER['REQUEST_METHOD'];
        //@ filter注入
        $act->_filter   =  new \Framework\Lib\Filter();
        $act->_filter->filterBase();
        //@ before & after
        $act->beforeAction();
		$act->$_action();
        $act->afterAction();
	}

//--------------------------------- 非配置 --------------------------

	//@ 获取分组
	private static function _initGroup(&$_args){
		$ConfigGroupArray = array( // 后续加入配置文件
			'Center',
			'Middleware',
		);
		if(in_array($_args[0],$ConfigGroupArray)){
			$_group = array_shift($_args);
			$_groupVal = $_group."/";
		}
		return array($_group,$_groupVal);
	}
	
	//@ 获取Controller
	private static function _initController(&$_args){
		$_module = array_shift($_args);
		$_action = array_shift($_args);
		$_module = $_module == '' ? 'Index' : $_module;
		$_action = $_action == '' ? 'index' : $_action;
        $method  = $_SERVER['REQUEST_METHOD'];
        $_action = $_action.ucfirst(strtolower($method));
		return array($_module, $_action);
	}
	
	//@ 验证Module 是否存在
	private static function _initRouterVerify($_module, $_groupVal){
		$_module_name = ucfirst($_module)."Controller";
		$_module_file = BJ_ROOT.'App/Controller/'.$_groupVal.$_module_name.".php";
		if(is_file($_module_file)){
			return array($_module_name, $_module_file);
		}else{
			abort(404);
		}
	}
	
	//@ 获取get
	private static function _initGet(&$_args){
		//unset($_GET); //清除非路由get参数
		$Len = count($_args);
		for($n = 0; $n < $Len; $n = $n+2){
			$_GET[$_args[$n]] = $_args[$n+1];
		}	
	}
	
	//@ 初始化语言包
	private static function _initLang(){
		$lang_file = BJ_ROOT."Lang/common.lang.php";
		if(is_file($lang_file)){
            include BJ_ROOT."Lang/common.lang.php";
		}	
	}	
	
}
?>