<?php
/*
 * 文件处理库类
 */
namespace Framework\Lib;
class File{

    /*
    * 文件夹拷贝
    *
    */
    public function copyDirFiles($dir = '' ,$sourceDir = '', $destDir = ''){
        $urldir  = BJ_ROOT.$sourceDir.'/';
        $copydir = BJ_ROOT.$destDir.'/';
        if(!is_dir($copydir.'/')){
            mkdir($copydir.'/');
        }
        if($dir == ''){
            $dir = opendir($urldir);
        }
        while($filename = readdir($dir)){
            if($filename != "." && $filename != "..") {
                if(is_dir($urldir.$filename)){
                    is_dir($copydir.$filename) ? '' : mkdir($copydir.$filename);
                    $dirs = opendir($urldir.$filename);
                    $this->copyDirFiles($dirs, $sourceDir.'/'.$filename, $destDir.'/'.$filename);
                }else{
                    copy($urldir.$filename , $copydir.$filename);
                }
            }
        }

    }

    /*
    * 删除目录下所有文件
    */
    public function dropDirFiles($now_dir){
        if(is_dir($now_dir)){
            $dir = opendir($now_dir);
            $urldir = $now_dir.'/';
            while($filename = readdir($dir)){
                if($filename != "." && $filename != "..") {
                    if(is_dir($urldir.$filename)){
                        $this->dropDirFiles($now_dir.'/'.$filename);
                    }else{
                        unlink($urldir.$filename);
                    }
                }
            }
            closedir($dir);
            if(rmdir($now_dir)){
                return true;
            }else{
                return false;
            }
        }else{
            return;
        }
    }

}
?>