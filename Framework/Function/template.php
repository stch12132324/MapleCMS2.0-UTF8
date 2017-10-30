<?php
/*
@ 核心函数 模板驱动
*/
function template_parse($str, $istag = 0){
	$str = preg_replace("/([\n\r]+)\t+/s","\\1",$str);
	$str = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}",$str);
	$str = preg_replace("/\{template\s+(.+)\}/","<?php include template(\\1); ?>",$str);
	$str = preg_replace("/\{include\s+(.+)\}/","<?php include \\1; ?>",$str);
	$str = preg_replace("/\{php\s+(.+)\}/","<?php \\1?>",$str);
	$str = preg_replace("/\{if\s+(.+?)\}/","<?php if(\\1) { ?>",$str);
	$str = preg_replace("/\{else\}/","<?php } else { ?>",$str);
	$str = preg_replace("/\{elseif\s+(.+?)\}/","<?php } elseif (\\1) { ?>",$str);
	$str = preg_replace("/\{\/if\}/","<?php } ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2) { ?>",$str);
	$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/","<?php if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>",$str);
	$str = preg_replace("/\{\/loop\}/","<?php } ?>",$str);
	$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/","<?php echo \\1;?>",$str);
	$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/","<?php echo \\1;?>",$str);
	$str = preg_replace_callback("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/s", function($match){ return addquote('<?php echo '.$match[0].';?>');} , $str);
	$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>",$str);
	if(!$istag) $str = "<?php defined('IN_BM') or exit('Access Denied'); ?>".$str;
	return $str;
}
function template($filename, $dir='', $group=''){
	$tplfile_c = md5($dir.$group.$filename);
	if($dir!="") $dir=$dir.'/';
	if($group==''){
		$filename = TPL_ROOT.TPL_NAME.$dir.$filename.".tpl";
	}else{
		$filename = TPL_ROOT.$group.'/'.$dir.$filename.".tpl";
	}
	$tplfile = CPD_ROOT.$tplfile_c.".php";
	if(!file_exists($filename)) {
		echo "模板文件不存在".$filename;
		exit();
	}
	if(@filemtime($filename)>@filemtime($tplfile)){
		template_compile($filename,$tplfile);
	}
	return $tplfile;
}
function template_compile($file,$file_c){
	$tplfile=$file;
	$content = file_get_contents($tplfile);
	if($content==false){
		 echo "模板文件不存在";
		 exit();
	}
	$compiled_file=$file_c;
	$content = template_parse($content);
	$strlen = @file_put_contents($compiled_file, $content);
	@chmod($compiled_file, 0777);
	return $strlen;
}
function addquote($var){
    $var = preg_replace("/\{(.*)\}/" , "$1" ,$var);
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}
?>