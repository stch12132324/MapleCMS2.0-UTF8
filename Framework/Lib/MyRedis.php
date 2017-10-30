<?php 
/*
 * Redis 公共类
 */
namespace Framework\Lib;
class MyRedis{
	var $redis;
	var $redisHost = "127.0.0.1";
	var $redisPort = "6379";
	var $redisTimeOut = 2;
	function __construct(){
		return $this->initRedis();
	}
	/*
	 * 初始化Redis创建对象
	 */
	function initRedis(){
		try{
			$this->redis = new \Redis();
			$this->redis->connect($this->redisHost,$this->redisPort,$this->redisTimeOut);
			return true;
		}catch(Exception $e){
			// 处理邮件报警
			return false;
		}
	}
	/*
	 * 选择数据库 0放默认缓存，15放固定user-send-list
	 */
	function redisSelectDb($dbNumber){
		return $this->redis->select($dbNumber);
	}
//---------------------------- STRING --------------------------------------
	/*
	 * STRING方法获取key的值
	 */
	function redisGet($key){
		return $this->redis->get($key);
	}
	
	/*
	 * STRING方法存储key的值
	*/
	function redisSet($key,$value){
		return $this->redis->set($key,$value);
	}
	/*
	*  STRING方法mget
	*/
	function redisMget($keys){
		if(is_array($keys)){
			return $this->redis->mGet($keys);
		}else{
			return false;	
		}
	}
//---------------------------- HASH --------------------------------------
	/*
	 * HASH方法获取key的所有值
	 */
	function redisHGet($key){
		return $this->redis->hGetAll($key);
	}
	
	/*
	 * HASH方法存储key的值
	 * $key
	 * $data array类型
	 */
	function redisHSet($key,$data){
		return $this->redis->hMset($key,$data);
	}
	/*
	 * HASH方法自增
	 * $key
	 * $k
	 * $step 
	 */
	function redisHIncr($key,$k,$step=1){
		return $this->redis->hIncrBy($key,$k ,$step);
	}
	
	function redisHMset($key,$array){
		return $this->redis->hMset($key,$array);
	}
	function redisHGetAll($key){
		return $this->redis->hGetAll($key);
	}
//---------------------------- LIST --------------------------------------
	/*
	 * LIST方法 rPush
	 * $key
	 * $k
	 * $step 
	 * 返回值 List 长度
	 */
	function redisRPush($key,$val){
		return $this->redis->rPush($key,$val);
	}
	/*
	 * LIST方法 lRange
	 * $key
	 * $start
	 * $end
	 */
	function redisLRange($key,$start,$end){
		return $this->redis->lRange($key,$start,$end);
	}
	
	function redisLSize($key){
		return $this->redis->lSize($key);	
	}
	
	function redisLRem($key,$val){
		return $this->redis->lRem($key,$val,0);	
	}
//---------------------------- OTHER --------------------------------------
	/*
	*	del 删除内容
	*	$key
	*/
	function redisDel($key){
		return $this->redis->delete($key);
	}
	
	/*
	 * setTimeout 设置过期时间
	 * $key
	 * $life
	 */
	function redisTimeout($key,$life){
		return $this->redis->setTimeout($key,$life);	
	}
	
	/*
	 * keys 根据条件查找keys
	 * $param
	 *
	 */
	 function redisKeys($param=''){
	 	return $this->redis->keys($param); 
	 }
	
}
?>