<?php
/*****************************
*数据库连接
*****************************/
$conn = @mysql_connect("10.173.201.228:3306","fota","fota1@#");
if (!$conn){
    die("连接数据库失败：" . mysql_error());
}
mysql_select_db("fota", $conn);
//字符转换，读库
mysql_query("set character set 'gbk'");
//写库
mysql_query("set names 'gbk'");
?>