<?php
/*
*Author:FancyPig Team
*https://www.iculture.cc
*/

$time_start = microtime(true);
define('ROOT', dirname(__FILE__).'/');
define('MATCH_LENGTH', 0.1*1024*1024);	//字符串长度 0.1M 自己设置，一般够了。
define('RESULT_LIMIT',8);


function my_scandir($path){//获取数据文件地址
	$filelist=array();
	if($handle=opendir($path)){
		while (($file=readdir($handle))!==false){
			if($file!="." && $file !=".."){
				if(is_dir($path."/".$file)){
					$filelist=array_merge($filelist,my_scandir($path."/".$file));
				}else{
					$filelist[]=$path."/".$file;
				}
			}
		}
	}
	closedir($handle);
	return $filelist;
}

function get_results($keyword){//查询
	$return=array();
	$count=0;
	$datas=my_scandir(ROOT."YGwiki"); //数据库文档目录
	if(!empty($datas))foreach($datas as $filepath){
		$filename = basename($filepath);
		$start = 0;
		$fp = fopen($filepath, 'r');
			while(!feof($fp)){
				fseek($fp, $start);
				$content = fread($fp, MATCH_LENGTH);
				$content.=(feof($fp))?"\n":'';
				$content_length = strrpos($content, "\n");
				$content = substr($content, 0, $content_length);
				$start += $content_length;
				$end_pos = 0;
				while (($end_pos = strpos($content, $keyword, $end_pos)) !== false){
					$start_pos = strrpos($content, "\n", -$content_length + $end_pos);
					$start_pos = ($start_pos === false)?0:$start_pos;
					$end_pos = strpos($content, "\n", $end_pos);
					$end_pos=($end_pos===false)?$content_length:$end_pos;
					$return[]=array(
									'f'=>$filename,
									't'=>trim(substr($content, $start_pos, $end_pos-$start_pos))
								);
					$count++;
					if ($count >= RESULT_LIMIT) break;
				}
				unset($content,$content_length,$start_pos,$end_pos);
				if ($count >= RESULT_LIMIT) break;
			}
		fclose($fp);
		if ($count >= RESULT_LIMIT) break;
	}
	return $return;
}


if(!empty($_POST)&&!empty($_POST['q'])){
	set_time_limit(0);				//不限定脚本执行时间
	$q=strip_tags(trim($_POST['q']));
	$results=get_results($q);
	$count=count($results);
}
 
?>
<!DOCTYPE HTML>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>邕高2021级学号查询在线平台- Powered by ygwiki.net </title>
<meta name="copyright" content="www.ygwiki.net" />
<meta name="keywords" content="邕高,学号,2021" />
<meta name="description" content="邕高2021级学号查询在线平台-" />
<link rel="stylesheet" type="text/css" href="html/default.css" />
	<style type="text/css">
	body,td,th {
	color: #FFF;
}
    a:link {
	color: #0C0;
	text-decoration: none;
}
    body {
	background-color: #000;
}
    a:visited {
	text-decoration: none;
	color: #999;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
	color: #F00;
}
    </style>
<script>
<!--
    function check(form){
if(form.q.value==""){
  alert("Not null！");
  form.q.focus();
  return false;
 }
}
-->
</script>
	</head>
	<body>
	<div id="container"><div id="header"><a href="https://2021.ygwiki.net" ><h1>邕高2021级学号查询在线平台</h1></a></div><br /><br />

<form name="from"action="index.php" method="post">
			<div id="content"><div id="create_form"><label>请输入您要查询的关键词：<input class="inurl" size="26" id="unurl" name="q" value="<?php echo !empty($q)?$q:''; ?>"/></label>
	<p class="ali"><label for="alias">关键词搜索:</label><span>姓名,学号（支持模糊搜索）</span></p><p class="but"><input onclick="check(form)" type="submit" value="Search" class="submit" /></p>
		</form></div>
		<?php
		if(isset($count)){
			echo '找到 ' . $count . ' 条数据，耗时 ' . (microtime(true) - $time_start) . " 秒（每次最多搜索8条数据）"; 
			if(!empty($results)){
				echo '<ul>';
				foreach($results as $v){
					echo '<li>'.$v['t'].'</li>';
				}
				echo '<br /><br /><font color=#ffff00><li>使用本工具即表示<br />您赞成或支持邕高实行双休</li></font>';
				echo '</ul>';
			}
			        echo '<hr align="center" width="550" color="#2F2F2F" size="1"><font color=#ff0000>我们担保信息的准确性';
				echo '<br />联系我们:<a href="mailto:admin@ygwiki.net" target="_blank">admin@ygwiki.net</a></font>';
				echo '</ul>';
		}
		?>
		<div id="nav">
<ul><li class="current"><a href="https://www.spaghettimonster.org">点此加入飞天意面神教</a></li><li><a href="https://savedotorg.org/" target="_blank">点此支持禁售.org的后缀域名</a></li></ul>
</div>
<div id="footer">
<p>邕高2021级学号查询在线平台 by <a href="https://ygwiki.net" target="_blank">邕有维基<a></p><div style="display:none">
</div>
</div>
</body>
</html>