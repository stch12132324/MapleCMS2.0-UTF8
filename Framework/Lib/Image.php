<?php
namespace Framework\Lib;
class Image {
    var $thumb_file;
    var $thumb_width;
    var $thumb_height;
    var $scr_file;
    var $scr_width;
    var $scr_height;
    var $type;
    var $im;
    function __construct($file = ''){
        if(is_file($file)){
            $this->scr_file = $file;
            $this->type = substr(strrchr($this->scr_file,".") , 1);
            if( $this->type == "jpg" ){
                $this->im = imagecreatefromjpeg($this->scr_file);
            }
            if( $this->type == "gif" ){
                $this->im = imagecreatefromgif($this->scr_file);
            }
            if( $this->type == "png" ){
                $this->im = imagecreatefrompng($this->scr_file);
            }
            $this->scr_width  = imagesx($this->im);
            $this->scr_height = imagesy($this->im);
        }
    }
	/*
     * @ 强制改变图片大小和品质
     */
    public function imageForceSize( $pinzhi = 70){
        $t_width  = $this->thumb_width;
        $src_file = $this->scr_file;
        if (!file_exists($src_file)) return false;
        $src_info = getImageSize($src_file);
        //如果来源图像小于或等于缩略图则拷贝源图像作为缩略图
        $new_width = $src_info[0] > $t_width ? $t_width : $src_info[0];
        //按比例计算缩略图大小
        $new_height = round( ( $new_width / $src_info[0] ) * $src_info[1]);
		
        //取得文件扩展名
        $fileext = $this->fileext($src_file);
        switch ($fileext) {
            case 'jpg' :
                $src_img = ImageCreateFromJPEG($src_file);
            break;
            case 'png' :
                $src_img = ImageCreateFromPNG($src_file);
            break;
            case 'gif' :
                $src_img = ImageCreateFromGIF($src_file);
            break;
        }
        //创建一个真彩色的缩略图像
        $thumb_img = ImageCreateTrueColor( $new_width , $new_height );
        if (function_exists('imagecopyresampled')) {
            @ImageCopyResampled($thumb_img,$src_img,0,0,0,0,$new_width,$new_height,$src_info[0],$src_info[1]);
        } else {
            @ImageCopyResized($thumb_img,$src_img,0,0,0,0,$new_width,$new_height,$src_info[0],$src_info[1]);
        }
        //生成缩略图,（可能要另外名字） PNG也强制JPG
        switch ($fileext) {
            case 'jpg' :
                imagejpeg($thumb_img , $src_file, $pinzhi);
            break;
            case 'gif' :
                imagegif($thumb_img , $src_file , $pinzhi);
            break;
            case 'png' :
			    $src_file = str_replace('.png' , '.jpg' , $src_file);	
				imagejpeg($thumb_img , $src_file , $pinzhi);
				//imagepng($thumb_img , $src_file , $pinzhi);
            break;
        }
        //销毁临时图像
        @ImageDestroy($src_img);
        @ImageDestroy($thumb_img);
        return $src_file;
    }
    /*
     * 图说专用，根据大小调整或者填充图片 使用css控制高度，差别比小于1.2的强制拉伸宽度
     */
    public function imageForceFill( $maxWidth = '' , $maxHeight = '' ){
        $src_file = $this->scr_file;
        if (!file_exists($src_file)) return false;
        $src_info = getImageSize($src_file);
        //@ 如果原图宽度小于maxWidth则，不改变宽度
        $new_width = $src_info[0] > $maxWidth ? $maxWidth : $src_info[0];
        //@ 根据宽度算缩放完的高度
        $new_height = round( ( $new_width / $src_info[0] ) * $src_info[1]);
        //@ 如果缩放完高度超出了maxHeight，则根据高度获得新宽度
        if( $new_height > $maxHeight ){
            $new_height = $maxHeight;
            $new_height = round( ( $new_height / $src_info[1] ) * $src_info[0]);
        }
        //-- 超过标准比例1.2倍强制填充
        $maxBili = round($maxHeight / $maxWidth );//超过标准比例1.2倍强制填充   0.65
        $nowBili = round($new_height / $new_width );
        //@ 只有比例小于1.2时候强制根据高度缩放图片，大于1.2时候不控制，让css填黑色
        if( ( $nowBili < $maxBili * 1.2 ) && $nowBili < 1 ){
            $new_width  = round( ($maxWidth / $maxHeight ) * $new_height);
            //取得文件扩展名
            $fileext = $this->fileext($src_file);
            //创建一个真彩色的缩略图像
            $thumb_img = ImageCreateTrueColor( $new_width , $new_height );
            if (function_exists('imagecopyresampled')) {
                @ImageCopyResampled( $thumb_img , $this->im , 0 , 0 , 0 , 0 , $new_width , $new_height , $src_info[0] , $src_info[1] );
            } else {
                @ImageCopyResized( $thumb_img , $this->im ,0 , 0 , 0 , 0 , $new_width , $new_height , $src_info[0] , $src_info[1] );
            }
            //生成缩略图,（可能要另外名字）
            switch ($fileext) {
                case 'jpg' :
                    imagejpeg($thumb_img , $src_file);
                break;
                case 'gif' :
                    imagegif($thumb_img , $src_file);
                break;
                case 'png' :
                    imagepng($thumb_img , $src_file);
                break;
            }
            //销毁临时图像
            //@ImageDestroy($src_img);
            @ImageDestroy($thumb_img);
        }
        return true;
    }
    /*
     * 首页长传图片自带文字版权水印脚本
     */
    public function waterMark(){
        //创建画布高度为图片高度+30px
        $new_height = 30;
        if($this->scr_width == '') return;
        $bgIm = imagecreate($this->scr_width, $new_height);
        imagecolorallocate($bgIm, 33, 78, 137);
        //底部加入文字
        $text_color = imagecolorallocate($bgIm, 255, 255, 255); //文字颜色
        $text = '龙腾网 http://www.ltaaa.com 倾听各国草根真实声音，纵论全球平民眼中世界'; //加入文字
        imagettftext($bgIm, 10, 0, 5, $new_height - 8, $text_color , BJ_ROOT.'/Static/font/mcyahei.ttf' ,iconv("GBk","UTF-8", $text)); // 字体, 斜度, x, y
        //将文字的图片拷贝到旧图片底部
        imagecopymerge($this->im, $bgIm, 0, $this->scr_height - 30, 0, 0, $this->scr_width, 30, 100);
        //保存文件
        imagejpeg($this->im, $this->scr_file, 100);
        imagedestroy($bgIm);
        imagedestroy($this->im);
    }
    // @ logo 水印版权
    public function logoWaterMark( $file , $pos , $trans = 80){
        $waterMarkFile = BJ_ROOT.'Static/images/water-mark.png';
        //文件不存在则返回
        if ( !file_exists( $waterMarkFile ) || !file_exists($file)) return;
        if ( !function_exists('getImageSize') ) return;
        //检查GD支持的文件类型
        $gd_allow_types = array();
        if (function_exists('ImageCreateFromGIF')) $gd_allow_types['image/gif'] = 'ImageCreateFromGIF';
        if (function_exists('ImageCreateFromPNG')) $gd_allow_types['image/png'] = 'ImageCreateFromPNG';
        if (function_exists('ImageCreateFromJPEG')) $gd_allow_types['image/jpeg'] = 'ImageCreateFromJPEG';
        //获取文件信息
        $fileinfo = getImageSize($file);
        $wminfo   = getImageSize($waterMarkFile);
        if ($fileinfo[0] < $wminfo[0] || $fileinfo[1] < $wminfo[1]) return;
        if (array_key_exists($fileinfo['mime'],$gd_allow_types)) {
            if (array_key_exists($wminfo['mime'],$gd_allow_types)) {
                //从文件创建图像
                $temp    = $gd_allow_types[$fileinfo['mime']]($file);
                $temp_wm = $gd_allow_types[$wminfo['mime']]($waterMarkFile);
                //水印位置
                switch ($pos) {
                    case 1 :  //顶部居左
                        $dst_x = 0; $dst_y = 0; break;
                    case 2 :  //顶部居中
                        $dst_x = ($fileinfo[0] - $wminfo[0]) / 2; $dst_y = 0; break;
                    case 3 :  //顶部居右
                        $dst_x = $fileinfo[0]-$wminfo[0]-30; $dst_y = 30; break;
                    case 4 :  //底部居左
                        $dst_x = 0; $dst_y = $fileinfo[1]; break;
                    case 5 :  //底部居中
                        $dst_x = ($fileinfo[0] - $wminfo[0]) / 2; $dst_y = $fileinfo[1]; break;
                    case 6 :  //底部居右
                        $dst_x = $fileinfo[0]-$wminfo[0]; $dst_y = $fileinfo[1]-$wminfo[1]; break;
                    default : //随机
                        $dst_x = mt_rand(0,$fileinfo[0]-$wminfo[0]); $dst_y = mt_rand(0,$fileinfo[1]-$wminfo[1]);
                }
                if (function_exists('ImageAlphaBlending')) ImageAlphaBlending($temp_wm,True); //设定图像的混色模式
                if (function_exists('ImageSaveAlpha')) ImageSaveAlpha($temp_wm,True); //保存完整的 alpha 通道信息
                //为图像添加水印
                if( $wminfo['mime'] == 'image/png' ){ // png 本身自带透明
                    imagecopy($temp,$temp_wm,$dst_x,$dst_y,0,0,$wminfo[0],$wminfo[1]);
                }else{
                    if (function_exists('imageCopyMerge')) {
                        ImageCopyMerge($temp,$temp_wm,$dst_x,$dst_y,0,0,$wminfo[0],$wminfo[1],$trans);
                    } else {
                        ImageCopyMerge($temp,$temp_wm,$dst_x,$dst_y,0,0,$wminfo[0],$wminfo[1],100);
                    }
                }
                //保存图片
                switch ($fileinfo['mime']) {
                    case 'image/jpeg' :
                        @imageJPEG($temp,$file);
                        break;
                    case 'image/png' :
                        @imagePNG($temp,$file);
                        break;
                    case 'image/gif' :
                        @imageGIF($temp,$file);
                        break;
                }
                //销毁零时图像
                @imageDestroy($temp);
                @imageDestroy($temp_wm);
            }
        }
    }
    /*
     * 文字转化图片带版权 postmake目录下，定期进行清理
     */
    public function txtToImg($string = '' , $author = '' , $contentid = '' , $pic_num = '' , $type = ''){
        //header("Content-type: image/jpeg");
        $txt_width  = 698;
        //$txt_height = 300;
        $img_dir  = '/uploadfile/PostMake/'.substr($contentid , -2).'/'.$contentid;
        if(!is_dir($img_dir)) createdir($img_dir);
        $img_file = $img_dir.'/'.$type.'-'.$pic_num.'.jpg';
        mb_internal_encoding("UTF-8"); // 设置编码
        $fontFace = BJ_ROOT.'/Static/font/mcyahei.ttf';
        $string = strip_tags($string);
        $string = trim($string , "[/copy]");
        $string = $this->addAuthorCopy($string , $author);
        $string = html_entity_decode($string);
        //$string = str_replace("&nbsp;", "", $string);
        $string = $this->autowrap(10, 0, $fontFace, $string, 698); // 自动换行处理
        $txt_height = $this->getTxtImgHeight($string);
        $im = imagecreate($txt_width , $txt_height); // 画布
        imagecolorallocate($im, 255, 255, 255);       // 背景颜色
        $text_color = imagecolorallocate($im, 69, 69, 69); //文字颜色
        imagettftext($im, 10, 0, 0, 16, $text_color , $fontFace ,$string);// 字体, 斜度, x, y
        //imagepng($im);
        imagejpeg($im, BJ_ROOT.$img_file , 85);
        imagedestroy($im);
        return $img_file;
    }
    // 根据图片换图\n换行
    public function autowrap($fontsize, $angle, $fontface, $string, $width) {
        $string = iconv("GBK" , "UTF-8//IGNORE//TRANSLIT" , $string);
        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $content = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i=0;$i<mb_strlen($string);$i++) {
            $letter[] = mb_substr($string, $i, 1);
        }
        $countString = '';
        foreach ($letter as $l) {
            $countString .= " ".$l;
            /*$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $content .= "\n";
            }*/
            if($l=="\n") $countString = ''; //如果字符是换行，则重新计数
            if(strlen($countString) > 208){
                $countString = '';
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }
    public function getTxtImgHeight($string){
        $line = explode("\n",$string);
        $line = count($line);
        //return $line * 20 + 35; //版权30高度
        return $line * 20;
    }
    // 随机插入版权
    public function addAuthorCopy($string='' , $author=''){
        $parts = explode("\n",$string);
        $line = count($parts);
        $addPoint = rand(1,$line);
        $parts_1  = array_slice($parts, 0 ,$addPoint);
        $parts_2  = array_slice($parts, $addPoint, $line);
        return implode("\n",$parts_1)."\n\n龙腾网 http://www.ltaaa.com 倾听各国草根真实声音，纵论全球平民眼中世界 译文作者：".$author."\n".implode("\n",$parts_2);
    }
	//获取文件扩展名
    function fileext($filename) {
        return strtolower(substr(strrchr($filename,'.'),1,10));
    }

    function thumb_image($wid , $hei , $path) {
		$this->thumb_width  = $wid;
		$this->thumb_height = $hei;

		if(($this->scr_width-$this->thumb_width)>($this->scr_height-$this->thumb_height)){
			$this->thumb_height=($this->thumb_width/$this->scr_width)*$this->scr_height;
		}else{
			$this->thumb_width=($this->thumb_height/$this->scr_height)*$this->scr_width;
		}
		//echo $this->thumb_width,$this->thumb_height;
		if($this->type != 'gif' && function_exists('imagecreatetruecolor')){
			$thumbimg = imagecreatetruecolor($this->thumb_width, $this->thumb_height);
		}else{
			$thumbimg = imagecreate($this->thumb_width,$this->thumb_height);
		}
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($thumbimg, $this->im, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->scr_width, $this->scr_height);
		}else{
			imagecopyresized($thumbimg,$this->im, 0, 0, 0, 0, $$this->thumb_width, $this->thumb_height,  $this->scr_width, $this->scr_height);
		}
		if($this->type=='gif' || $this->type=='png'){
			$background_color  =  imagecolorallocate($thumbimg,  0, 255, 0);  //  指派一个绿色
			imagecolortransparent($thumbimg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
		}
        switch ($this->type) {
            case 'jpg' :
                ImageJPEG($thumbimg , $path); break;
            case 'gif' :
                ImageGIF($thumbimg , $path); break;
            case 'png' :
                ImagePNG($thumbimg , $path); break;
        }
        imagedestroy($this->im);
        imagedestroy($thumbimg);
        return $this->thumb_file;
    }
}
?>