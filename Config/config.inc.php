<?php
// 网站根目录
define('BASE_PATH', '/');
// Redis配置
define("REDIS_IP","127.0.0.1");
// 数据库配置 json 格式
define('DB_CONFIG' , '{
    "default": {
        "dbhost": "127.0.0.1",
        "dbuser": "",
        "dbpw": "",
        "dbname": "",
        "charset": "gbk",
        "dbpre": "bm_"
    },
    "db2": {
        "dbhost": "127.0.0.1",
        "dbuser": "",
        "dbpw": "",
        "dbname": "",
        "charset": "gbk",
        "dbpre": "pre_"
    }
}');
define('DB_PCONNECT'    , '0'); // 0 或1，是否使用持久连接
define('DB_ERROR_TYPE'  , 1);   // 数据库当前环境1=开发，2=线上
define('DB_RESULT_TYPE' , 1);   // 0 = array, 1 = object
//路径设置
define('CACHE_PATH'     , BJ_ROOT.'date/cache/'); //缓存默认存储路径
define('ADS_PATH'       , BJ_ROOT.'Cache/Ads/');
define('PLUGIN_PATH'    , BJ_ROOT.'Lib/Plugin/');
define('SINGLE_PATH'    , BJ_ROOT.'Cache/Single/');

//模板相关配置
define('TPL_ROOT'       , BJ_ROOT.'Tpl/'); //模板保存物理路径
define('TPL_NAME'       , 'Default/'); 	//当前模板方案目录
define('TPL_CSS'        , 'Default'); 		//当前样式目录
define('CPD_ROOT'       , BJ_ROOT.'Cache/Compiled/');
define('CACHE_DIR'      , BJ_ROOT."Cache/Caches");
define('COMPILE_DIR'    , BJ_ROOT."Cache/Compiled");
define('IN_BM'          , true);
define('CSS_MERGE'      , true);//是否开始css,js合并
define('CSS_MERGE_ZIP'  , true);//是否开启css,js压缩
//COOKIE
define('COOKIE_PATH'   , '/');
define('C_DOMAIN_AREA' ,'/');
define('COOKIE_KEY'    , 'lt_ck_');

//LOG
define('LOG_OPEN'            , '1');//基础日志开关
define('LOG_OPEN_ALL'        , '1');//全部类型日志开关
define('LOGIN_LOCKED_TIME'   , 900);
define('LOGIN_LOCKED_NUMBER' , 4);

//附件相关配置
define('UPLOAD_ROOT'        , BJ_ROOT.'uploadfile/'); //附件保存物理路径
define('UPLOAD_URL'         , 'uploadfile/'); //附件目录访问路径
define('BIG_IMG_SIZE'       , '250');
define('BIG_IMG_HEIGHT'     , '600');
define('CHARSET'            , 'gbk');
define('TIMEZONE'           , 'Etc/GMT-8');
define('AUTH_KEY'           , 'YUsf120sDR'); //Cookie密钥
define('PASSWORD_KEY'       , 'ltFDfsd');
define('URL_KEY'            , 'Yts   dbtlas');//url的key
define('CRYPT_KEY'          , '4984FDRvcdvsregdASDfvcrtrctrtFe1');
define('CRYPT_KEY2'         , 'DSd67a8dahDJkdhadaslddkaslsdksas');
define('TOKEN_KEY_NAME'     , 'lt_user_keys');
define('ALLOWED_HTMLTAGS'   , '<a><p><br><hr><h1><h2><h3><h4><h5><h6><font><u><i><b><strong><div><span><ol><ul><li><img><table><tr><td>'); //前台发布信息允许的HTML标签，可防止XSS跨站攻击
?>