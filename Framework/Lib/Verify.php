<?php
/*
@ 验证码类
*/
namespace Framework\Lib;
class Verify{
	public function run(){
		session_start();
		//如果浏览器显示“图像XXX因其本身有错无法显示”，可尽量去掉文中空格  
		//先成生背景，再把生成的验证码放上去  
		$img_height = 70;//先定义图片的长、宽
		$img_width  = 32;
		$authnum    = '';
		//生产验证码字符  
		/*$ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";  
		$list = explode(",",$ychar);  */
		$list = range('A','Z');
		$list = array_merge(range(0,9),$list);
		for($i=0;$i<4;$i++){  
			$randnum=rand(0,35);
			$authnum.=$list[$randnum];
		}
		//验证码保存session  
		$_SESSION["check_number"] = $authnum;
		$aimg = imagecreate($img_height,$img_width);//生成图片  
		imagecolorallocate($aimg, 255,255,255);//图片底色  
		$black = imagecolorallocate($aimg,168,168,168);//定义需要的黑色  
		  
		for ($i=1;$i<=100;$i++){
			imagestring($aimg,1,mt_rand(1,$img_height),mt_rand(1,$img_width),"@",imagecolorallocate($aimg,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255)));  
		}
		//为了区别于背景，这里的颜色不超过200，上面的不小于200  
		for($i=0;$i<strlen($authnum);$i++){
			imagestring($aimg, mt_rand(5,8),$i*$img_height/4+mt_rand(2,7),mt_rand(1,$img_width/2-2), $authnum[$i],imagecolorallocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)));
		}
		imagerectangle($aimg,0,0,$img_height-1,$img_width-1,$black);
		header("Content-type: image/PNG");
		imagepng($aimg);
		imagedestroy($aimg);
	}
}
?> 