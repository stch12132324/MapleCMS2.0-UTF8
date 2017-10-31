<?php
// +----------------------------------------------------------------------
// +@ Framework 入口文件
// +@ By Stch12132324
// +@ Time:2014-08-08
// +@ Update:2017-09-12
// +----------------------------------------------------------------------
define('BJ_ROOT', str_replace("\\", '/', substr(dirname(__FILE__), 0, -9)));
define('MICROTIME_START',microtime());
unset($HTTP_ENV_VARS, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_POST_FILES, $HTTP_COOKIE_VARS);
require "autoload.php";
require BJ_ROOT.'Config/config.inc.php';
include BJ_ROOT.'Framework/Function/template.php';
include BJ_ROOT.'Framework/Function/functions.php';

header('Content-type: text/html; charset='.CHARSET);
if(function_exists('date_default_timezone_set')) date_default_timezone_set(TIMEZONE);

\Framework\Core\App::run();
?>