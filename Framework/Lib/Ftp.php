<?php
namespace Framework\Lib;
class Ftp{
	var $ftp_server = '221.229.172.30';
	var $ftp_port = '21';
	var $ftp_user_name = 'ltaaacom';
	var $ftp_user_pass = 'ltaaacom***###';
	var $conn_id;
	public function ftp(){
		$this->conn_id = ftp_connect($this->ftp_server,$this->ftp_port) or die("Couldn't connect to ".$this->ftp_server);
		if(ftp_login($this->conn_id,$this->ftp_user_name,$this->ftp_user_pass)){
			ftp_pasv($this->conn_id,true); // 被动模式
			return '连接成功!';
		}else{
			return '连接失败！';	
		}
	}
	// 上传文件
	public function upload($local_file,$destination_file){
		$dirs = explode("/",$destination_file);
		array_pop($dirs);
		$dir  = implode("/" , $dirs);
		if( @!$this->ftp_is_dir( $dir ) ){
			$this->ftp_mk_dir( $dir );
		}
		if(ftp_put($this->conn_id,$destination_file,$local_file,FTP_BINARY)){
			return true;	
		}else{
			return false;	
		}
		
	}
	// 删除文件
	public function ftp_del($file){
		return @ftp_delete($this->conn_id,$file);	
	}
	// 创建目录
	function ftp_mk_dir($path){
		$path = trim( $path , '/');
		$dir  = explode("/", $path);
		$path = "";
		$ret  = true;
        $original_directory = ftp_pwd($this->conn_id);// 根目录地址
		for($i=0; $i<count($dir); $i++){
		    $path .= "/".$dir[$i];
            $path = trim($path , '/');
			if(@!ftp_chdir($this->conn_id,$path)){
				ftp_chdir($this->conn_id , $original_directory); //返回根目录开始创建
				if(!ftp_mkdir($this->conn_id , $path)){
					$ret = false;
					break;
				}
			}else{
				ftp_chdir($this->conn_id , $original_directory);	
			}
		}
		return $ret;
	}
	// 判断路径是否存在
	function ftp_is_dir($path){  
		$original_directory = ftp_pwd($this->conn_id); // 当前路径，先保存
		if(@ftp_chdir($this->conn_id , $path)){
			ftp_chdir($this->conn_id,$original_directory);  
			return true;
		}  
		else { 
			return false;  
		}
	}
}
?>