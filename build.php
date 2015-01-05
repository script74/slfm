<?php
/**
 * User: loveyu
 * Date: 2015/1/5
 * Time: 23:08
 */
$out_file = "slfm.php";
$min_file = "slfm.min.php";
$content = file_get_contents("index.php");
preg_match_all("/include_once\\(\"([a-z0-9A-Z.-\\/]+?)\"\\)/", $content, $matches);
$output_content = "<?php
//
//首行为配置文件，请勿修改
";
$list = array('copyright.txt');//添加版权信息
$list = array_merge($list, $matches[1]);
foreach($list as $v){
	$content = file_get_contents($v);
	if(substr($content, 0, 5) == "<?php"){
		$content = substr($content, 5);
	}
	$output_content .= "\r\n\r\n/*-- 文件: {$v} ---*/\r\n";
	$output_content .= trim($content) . "\r\n";
	//echo $v . "\n";
}
file_put_contents($out_file, $output_content);
echo "File: $out_file, size:".filesize($out_file)."\n";
system("php -w $out_file > $min_file");
$content = file_get_contents($min_file);
$content = "<?php\r\n//\r\n//首行配置勿删\r\n".substr($content,5);
file_put_contents($min_file, $content);
echo "File: $min_file, size:".filesize($min_file)."\n";
