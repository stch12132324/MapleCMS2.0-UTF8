<?php
/**
 * @ 自动加载
 */
spl_autoload_register(function ($class) {
    $arrs    = explode("\\", $class);
    $class   = lcfirst(array_pop($arrs)); // 文件名首字母必须小写 class User => user.php
    $filedir = implode('/',$arrs);
    $dir = $filedir.'/'.$class.'.php';
    if( is_file($dir) ){
        include $dir;
    }
});
?>