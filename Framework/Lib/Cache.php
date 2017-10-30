<?php
/*
 * Cache 缓存核心库类
 */
namespace Framework\Lib;
class Cache{
    var $type = 'file';
    /*
     *
     * @获取缓存
     * @key 键值
     * @ttl 生存时间 file模式下有效
     *
    */
    public function getCache($key = '' , $ttl = ''){
        switch( $this->type ){
            case 'file':
                return $this->getFileCache($key , $ttl);
            break;
            case 'memcache':

            break;
            case 'redis':

            break;
            case 'mongodb':

            break;
        }
    }

    /*
     * 写入缓存
    */
    public function setCache($key = '' , $val = '' , $ttl = ''){
        switch( $this->type ){
            case 'file':
                return $this->setFileCache($key , $val);
                break;
            case 'memcache':

                break;
            case 'redis':

                break;
            case 'mongodb':

                break;
        }
    }

    /*
     * @file 模式获取cache
     *
    */
    public function getFileCache($key = '' , $ttl = ''){
        $file = $this->getFileCacheDir($key);
        if( !is_file($file) ){
            return false;
        }
        if( filemtime($file) + $ttl > time() ){
            return file_get_contents($file);
        }else{
            return false;
        }
    }
    /*
     * @file 模式写入缓存
     *
    */
    public function setFileCache($key = '' , $val = ''){
        if( $this->createFileCacheDir($key) ){
            $file = $this->getFileCacheDir($key);
            file_put_contents($file , $val);
            return true;
        }else{
            return false;
        }
    }

    /*
     * @file 模式根据key获取Cache文件地址
     *
    */
    public function getFileCacheDir( $key = ''){
        $file = md5($key);
        return BJ_ROOT.'Cache/Caches/'.substr($file , 0 , 3).'/'.$file;
    }
    /*
     * @file 模式根据key判断文件路径，如果不存在则创建
     *
    */
    public function createFileCacheDir($key = ''){
        $dir = md5($key);
        $dir = 'Cache/Caches/'.substr($dir , 0 , 3);
        if( !is_dir(BJ_ROOT.$dir) ){
            createdir($dir);
        }
        return true;
    }
    /*
     * @file
     */
}
?>