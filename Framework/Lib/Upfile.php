<?php
/*
	用户分享上传：限定文件大小30kb，文件类型jpg ,png ,如果大于30kb，则进行缩略图
*/
namespace Framework\Lib;
class Upfile{
    var $dir;             //附件存放物理目录
    var $filepath;         //附件目录访问路径
    var $filetype;         //文件类型
    var $fileext;          //文件扩展名
    var $time;             //自定义文件上传时间
    var $allow_types;     //允许上传附件类型
    var $field;             //上传控件名称
    var $maxsize;         //最大允许文件大小，单位为KB
    var $thumb_width=0;    //缩略图宽度
    var $thumb_height=0;   //缩略图高度
    var $watermark_file; //水印图片地址
    var $watermark_pos;  //水印位置
    var $watermark_trans;//水印透明度
	var $pinzi;
	var $imgType;
	var $src_x = 0;
	var $src_y = 0;
	var $dst_x = 0;
	var $dst_y = 0;
	
    //构造函数
    //$types : 允许上传的文件类型 , $maxsize : 允许大小 ,  $field : 上传控件名称 , $time : 自定义上传时间
    function __construct($types = 'jpg|png|gif', $maxsize = 30, $field = 'attach', $time = '') {
        $this->allow_types = explode('|',$types);
        $this->maxsize = $maxsize * 1024;
        $this->field = $field;
        $this->time = $time ? $time : time();
		$this->pinzi = 95;
    }
	//设置上传的为大图
	function setSizeBig($maxsize,$img_type){
		$this->maxsize = $maxsize;
		$this->imgType = $img_type;
	}
    //设置并创建文件具体存放的目录
    //$basedir  : 基目录，必须为物理路径
    //$filedir  : 自定义子目录，可用参数{y}、{m}、{d}
    function set_dir($basedir='',$filedir = '') {
    	//echo $basedir;
        $this->filepath = UPLOAD_URL.$basedir;
        $dir = UPLOAD_ROOT.$basedir;
        !is_dir($dir) && @mkdir($dir,0777);
        if (!empty($filedir)) {
            $filedir = str_replace(array('{y}','{m}','{d}'),array(date('Y',$this->time),date('m',$this->time),date('d',$this->time)),strtolower($filedir));
            $dirs = explode('/',$filedir);
            foreach ($dirs as $d) {
                !empty($d) && $dir .= $d.'/';
                $this->filepath.=$d.'/';
                !is_dir($dir) && @mkdir($dir,0777);
            }
        }
        $this->dir = $dir;
    }
    //图片缩略图设置，如果不生成缩略图则不用设置
    //$width : 缩略图宽度 , $height : 缩略图高度
    function set_thumb ($width = 0, $height = 0) {
        $this->thumb_width  = $width;
        $this->thumb_height = $height;
    }
    //图片水印设置，如果不生成添加水印则不用设置
    //$file : 水印图片 , $pos : 水印位置 , $trans : 水印透明度
    function set_watermark ($file, $pos = 6, $trans = 80) {
        $this->watermark_file  = $file;
        $this->watermark_pos   = $pos;
        $this->watermark_trans = $trans;
    }
	
	function set_thumb_height($height = 0){
		$this->thumb_height = $height;
	}
    /*----------------------------------------------------------------
    执行文件上传，处理完返回一个包含上传成功或失败的文件信息数组，
    其中：name 为文件名，上传成功时是上传到服务器上的文件名，上传失败则是本地的文件名
          dir  为服务器上存放该附件的物理路径，上传失败不存在该值
          size 为附件大小，上传失败不存在该值
          flag 为状态标识，1表示成功，-1表示文件类型不允许，-2表示文件大小超出
    -----------------------------------------------------------------*/
    function execute() {
        $files = array(); //成功上传的文件信息
        $field = $this->field;
        $keys = array_keys($_FILES[$field]['name']);
        foreach ($keys as $key) {		
            if (!$_FILES[$field]['name'][$key]) continue;
			$img_info = getimagesize($_FILES[$field]['tmp_name'][$key]);  // 3个返回值 width height type  其中type可以判断真类型及mita类型

            $fileext = $this->fileext($_FILES[$field]['name'][$key]); //获取文件扩展名
			if( !( $fileext == 'jpg' || $fileext == 'png' || $fileext == 'gif' ) ){
				exit;	
			}
            $filename = $this->time.mt_rand(100,999).'.'.$fileext; //生成文件名
            $filedir = $this->dir;    //附件实际存放目录
            $filesize = $_FILES[$field]['size'][$key]; //文件大小
            //文件类型不允许
            if ($img_info[2]==''){
                $files[$key]['name'] = $_FILES[$field]['name'][$key];
                $files[$key]['flag'] = -1;
                continue;
            }
            //文件大小超出
			if ($filesize > $this->maxsize){
				// 背景等大图情况
				if($this->imgType=='big'){
					// 限定高度情况
					if(intval($this->thumb_height)!=0){
						
					}else{
						// 未限定高度先不处理
						$files[$key]['name'] = $_FILES[$field]['name'][$key];
						$files[$key]['flag'] = -2;
						continue;
					}
				}else{
				// 缩略图小图情况
					// 如果本身宽度大于500进行大小调整
					/*if($img_info[0]>400){
						$this->thumb_width = 400;// 如果超过大小则进行缩略
						$this->pinzi = 90;
					}else{
						$this->thumb_width = $img_info[0];
						$this->pinzi = 90;
					}*/
				}
			}
            //--
            $files[$key]['filename'] = $filename;
            $files[$key]['filetype'] = $_FILES[$field]['type'][$key];
            $files[$key]['filesize'] = $filesize;
            $files[$key]['filepath'] = $this->filepath;
            $files[$key]['fileext'] = $fileext;
            //保存上传文件并删除临时文件
            if (is_uploaded_file($_FILES[$field]['tmp_name'][$key])) {
                move_uploaded_file($_FILES[$field]['tmp_name'][$key],$filedir.$filename);
                @unlink($_FILES[$field]['tmp_name'][$key]);
                $files[$key]['flag'] = 1;
                //对图片进行加水印和生成缩略图
                if (in_array($fileext,array('jpg','png','gif'))) {
					// 所过超出了，并且设置了宽度或高度，则进行缩略（缩略图）
                    if ($this->thumb_width!=0||$this->thumb_height!=0) {
						// 用户背景部分，设定了高度
						if($this->thumb_height!=0){
							$this->dst_x = 0;
							$this->dst_y = 0;
							$this->src_x = 0;
							$this->src_y = round(($img_info[1]-$this->thumb_height)/2);
						}
						// 直接替换原图，调整大小
//return $this->create_thumb($filedir.$filename,$filedir.$filename); // dubug
						if ($this->create_thumb($filedir.$filename,$filedir.$filename)) {
                            $files[$key]['thumb'] = $filename;
                        }
                        $files[$key]['isthumb'] = 1;
						
						if($fileext=='png'){
							$filename = explode(".",$filename);
							$filename = $filename[0].".jpg";
						}
                    }
                    //$this->create_watermark($filedir.$filename); // 先不加水印
                }
            }
            $files[$key]['filepath'].=$filename;
        }
        return $files;
    }
    //创建缩略图,以相同的扩展名生成缩略图
    //$src_file : 来源图像路径 , $thumb_file : 缩略图路径
    function create_thumb ($src_file,$thumb_file) {
        $t_width  = $this->thumb_width;
        $t_height = $this->thumb_height;
        if (!file_exists($src_file)) return false;
        $src_info = getImageSize($src_file);
        //如果来源图像小于或等于缩略图则拷贝源图像作为缩略图
        if ($src_info[0] <= $t_width && $src_info[1] <= $t_height && $this->pinzi==95) {
            if (!copy($src_file,$thumb_file)) {
                return false;
            }
            return true;
        }
        //按比例计算缩略图大小
        /*if ($src_info[0] - $t_width > $src_info[1] - $t_height) {
            $t_height = ($t_width / $src_info[0]) * $src_info[1];
        } else {
            $t_width = ($t_height / $src_info[1]) * $src_info[0];
        }*/
		if($this->imgType=='big'){
			$t_width = $src_info[0];
			$t_height = $this->thumb_height;
			$src_info[1] = $this->thumb_height;
		}else{
			$t_height = ($t_width / $src_info[0]) * $src_info[1]; //高度由宽度决定
		}
        //取得文件扩展名
        $fileext = $this->fileext($src_file);
        switch ($fileext) {
            case 'jpg' :
                $src_img = ImageCreateFromJPEG($src_file); break;
            case 'png' :
                $src_img = ImageCreateFromPNG($src_file); break;
            case 'gif' :
                $src_img = ImageCreateFromGIF($src_file); break;
        }
        //创建一个真彩色的缩略图像
        $thumb_img = @ImageCreateTrueColor($t_width,$t_height);
        //ImageCopyResampled函数拷贝的图像平滑度较好，优先考虑
        if (function_exists('imagecopyresampled')) {
            @ImageCopyResampled($thumb_img,$src_img,$this->dst_x,$this->dst_y,$this->src_x,$this->src_y,$t_width,$t_height,$src_info[0],$src_info[1]);
        } else {
            @ImageCopyResized($thumb_img,$src_img,$this->dst_x,$this->dst_y,$this->src_x,$this->src_y,$t_width,$t_height,$src_info[0],$src_info[1]);
        }
        //生成缩略图
        switch ($fileext) {
            case 'jpg' :
                ImageJPEG($thumb_img,$thumb_file,$this->pinzi); break;
            case 'gif' :
                ImageGIF($thumb_img,$thumb_file); break;
            case 'png' :
				unlink($thumb_file);
				$thumb_file = str_replace("png","jpg",$thumb_file);
				ImageJPEG($thumb_img,$thumb_file,$this->pinzi);
			break;
                //ImagePNG($thumb_img,$thumb_file); break;
        }
        //销毁临时图像
        @ImageDestroy($src_img);
        @ImageDestroy($thumb_img);
        return true;
    }
    //为图片添加水印
    //$file : 要添加水印的文件
    function create_watermark ($file) {
        //文件不存在则返回
        if (!file_exists($this->watermark_file) || !file_exists($file)) return;
        if (!function_exists('getImageSize')) return;

        //检查GD支持的文件类型
        $gd_allow_types = array();
        if (function_exists('ImageCreateFromGIF')) $gd_allow_types['image/gif'] = 'ImageCreateFromGIF';
        if (function_exists('ImageCreateFromPNG')) $gd_allow_types['image/png'] = 'ImageCreateFromPNG';
        if (function_exists('ImageCreateFromJPEG')) $gd_allow_types['image/jpeg'] = 'ImageCreateFromJPEG';
        //获取文件信息
        $fileinfo = getImageSize($file);
        $wminfo   = getImageSize($this->watermark_file);
        if ($fileinfo[0] < $wminfo[0] || $fileinfo[1] < $wminfo[1]) return;
        if (array_key_exists($fileinfo['mime'],$gd_allow_types)) {
            if (array_key_exists($wminfo['mime'],$gd_allow_types)) {

                //从文件创建图像
                $temp = $gd_allow_types[$fileinfo['mime']]($file);
                $temp_wm = $gd_allow_types[$wminfo['mime']]($this->watermark_file);
                //水印位置
                switch ($this->watermark_pos) {
                    case 1 :  //顶部居左
                        $dst_x = 0; $dst_y = 0; break;
                    case 2 :  //顶部居中
                        $dst_x = ($fileinfo[0] - $wminfo[0]) / 2; $dst_y = 0; break;
                    case 3 :  //顶部居右
                        $dst_x = $fileinfo[0]; $dst_y = 0; break;
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
                if (function_exists('imageCopyMerge')) {
                    ImageCopyMerge($temp,$temp_wm,$dst_x,$dst_y,0,0,$wminfo[0],$wminfo[1],$this->watermark_trans);
                } else {
                    ImageCopyMerge($temp,$temp_wm,$dst_x,$dst_y,0,0,$wminfo[0],$wminfo[1],100);
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
    //获取文件扩展名
    function fileext($filename) {
        return strtolower(substr(strrchr($filename,'.'),1,10));
    }
	
	/*  */
	function execute2() {
        $files = array(); //成功上传的文件信息
        $field = 'upload';
        //$keys = array_keys($_FILES[$field]['name']);
        //foreach ($keys as $key) {
            //if (!$_FILES[$field]['name'][$key]) continue;
			$img_info = getimagesize($_FILES[$field]['tmp_name']);  // 3个返回值 width height type  其中type可以判断真类型及mita类型

            $fileext = $this->fileext($_FILES[$field]['name']); //获取文件扩展名
            $filename = $this->time.mt_rand(10000000,99999999).'.'.$fileext; //生成文件名
            $filedir = $this->dir;    //附件实际存放目录
            $filesize = $_FILES[$field]['size']; //文件大小
            //文件类型不允许
            //文件类型不允许
            if ($img_info[2]==''){
                $files['name'] = $_FILES[$field]['name'];
                $files['flag'] = -1;
                return;
            }
            //文件大小超出
			if ($filesize > $this->maxsize) {
				$files['name'] = $_FILES[$field]['name'];
				$files['flag'] = -2;
				 return;
			}
            //--
            $files['filename'] = $filename;
           // $files['dir'] = $filedir;
            $files['filetype']=$_FILES[$field]['type'];
            $files['filesize'] = $filesize;
            $files['filepath'] = $this->filepath;
            $files['fileext'] = $fileext;
            //保存上传文件并删除临时文件
            if (is_uploaded_file($_FILES[$field]['tmp_name'])) {
                move_uploaded_file($_FILES[$field]['tmp_name'],$filedir.$filename);
                @unlink($_FILES[$field]['tmp_name']);
                $files['flag'] = 1;
                //对图片进行加水印和生成缩略图
                if (in_array($fileext,array('jpg','png','gif'))) {
                    if ($this->thumb_width) {
                        if ($this->create_thumb($filedir.$filename,$filedir.'thumb_'.$filename)) {
                            $files['thumb'] = 'thumb_'.$filename;  //缩略图文件名
                        }
                        $files['isthumb'] = 1;
                    }
                    $this->create_watermark($filedir.$filename);
                }
            }
             $files['filepath'] .= $this->upload_files.'/'.$filename;
        //}
        return $files;
    }
}
?>