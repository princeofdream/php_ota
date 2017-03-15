<?php
session_start();

//检测是否登录，若没登录则转向登录界面
if(!isset($_SESSION['userid'])){
    header("Location:login.html");
    exit();
}

if(!isset($_POST['submit']) && !isset($_POST['submit_del']))
{
	exit('非法访问!');
}



function logd($str)
{
    $DEBUG=0;
    if($DEBUG === 1){
		$current_tm = date('H:i:s');
        echo "[ DEBUG $current_tm ]  $str<br />";
    }else{
    }
}

function db_del($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max)
{
	logd("DB_DEL --> get sec num: $get_sec_num");
	$check_sec_num = mysql_query("select sec_num from fota_machine where sec_num='$get_sec_num' limit 1");
	$sql_str = "";
	$result = "";
	if($result = mysql_fetch_array($check_sec_num))		//get sec_num
	{
		$sql_str = sprintf("DELETE FROM `fota_machine` WHERE `sec_num` = %d and `s_stat`='%s'and `s_min`='%s' and `s_max`='%s'",$get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max);
	}
	else
	{
		return;
	}
	$ret = mysql_query($sql_str);
	logd("------>$result<------>$sql_str<--<$ret>--------");
	if($ret == 1 )
	{
		$check_sec_num = mysql_query("select sec_num from fota_machine where sec_num='$get_sec_num' limit 1");
		if($result = mysql_fetch_array($check_sec_num))		//get sec_num
		{
			echo "失败！请检查所有数据，保证数据准确";
		}
		else
		{
			echo "删除成功！";
		}
	}

	return 1;
}

function db_update($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max)
{
	logd("DB_UPDATE --> get sec num: $get_sec_num");
	$check_sec_num = mysql_query("select sec_num from fota_machine where sec_num='$get_sec_num' limit 1");
	$sql_str = "";
	$result = "";
	if($result = mysql_fetch_array($check_sec_num))		//get sec_num
	{
		$sql_str = sprintf("UPDATE `fota_machine` SET `s_stat`='%s',`s_min`='%s',`s_max`='%s' WHERE `sec_num` = %d ",$get_sec_stat,$get_sec_min,$get_sec_max,$get_sec_num);
	}
	else
	{
		$sql_str = sprintf("INSERT INTO `fota_machine`(`sec_num`, `s_stat`, `s_min`, `s_max`) VALUES ('%d','%s','%s','%s')",$get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max);
	}
	$ret = mysql_query($sql_str);
	logd("------>$result<------>$sql_str<--<$ret>--------");
	if($ret == 1 )
		echo "Success update!";

	return 1;
}


function show_sn_data()
{
	/* show data */
	$sql = "SELECT * FROM `fota_machine`";
	// $sql = "SELECT * FROM `fota_machine`";
	$result = mysql_query($sql);

	print( "<table border='1'>");

	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		print( "<tr><td>". $row['sec_num'] . " </td><td>" . $row['s_stat'] . " </td><td>" . $row['s_min'] . " </td><td>" . $row['s_max'] . "<td></tr>");
	}

	return $ret;
}












function main_activity($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max)
{
	$operate = isset($_POST['submit_del']);
	if( $operate == "删除记录" )
	{
		db_del($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max);
	}
	else
	{
		db_update($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max);
	}
	show_sn_data();

	echo '<a href="ibx_db.php">返回</a><br />';

}



//包含数据库连接文件
include('conn.php');
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$user_query = mysql_query("select * from user where uid=$userid limit 1");
$row = mysql_fetch_array($user_query);
echo '用户信息：<br />';
echo '用户ID：',$userid,'<br />';
echo '用户名：',$username,'<br />';
echo '邮箱：',$row['email'],'<br />';
echo '注册日期：',date("Y-m-d", $row['regdate']),'<br />';
echo '<a href="login.php?action=logout">注销</a> 登录<br />';

// $username = htmlspecialchars($_POST['username']);
$get_sec_num = htmlspecialchars($_POST['sec_num']);
$get_sec_stat = htmlspecialchars($_POST['sec_stat']);
$get_sec_min = htmlspecialchars($_POST['sec_min']);
$get_sec_max = htmlspecialchars($_POST['sec_max']);

logd("---> $get_sec_num ----> $get_sec_stat ---> $get_sec_min ---> $get_sec_max");

main_activity($get_sec_num,$get_sec_stat,$get_sec_min,$get_sec_max);

?>

