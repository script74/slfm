<?php
//a:9:{s:4:"lang";s:2:"zh";s:9:"auth_pass";s:32:"d41d8cd98f00b204e9800998ecf8427e";s:8:"quota_mb";i:0;s:17:"upload_ext_filter";a:0:{}s:19:"download_ext_filter";a:0:{}s:15:"error_reporting";s:1:"2";s:7:"fm_root";s:0:"";s:17:"cookie_cache_time";i:2592000;s:7:"version";s:4:"0.10";}
//首行为配置保存行，务必保留
include_once("data/class.config.php");//配置文件类
include_once("data/class.archive.php");//打包类
include_once("data/class.tar_file.php");//tar打包类
include_once("data/class.gzip_file.php");//gzip打包类
include_once("data/class.zip_file.php");//zip打包类
include_once("data/class.bzip_file.php");//bzip打包类
include_once("data/func.sys.php");//系统性函数
include_once("data/func.file.php");//文件操作函数
include_once("data/func.data.php");//数据操作函数
include_once("data/func.et.php");//国际化语言函数
include_once("data/func.html.php");//HTML接口
include_once("data/func.session.php");//SESSIOn接口
include_once("data/func.frame.php");//frame
include_once("data/func.form.php");//SESSION接口
include_once("data/global.php");//全局初始化操作
include_once("data/run.php");//执行操作