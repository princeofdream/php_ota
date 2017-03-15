
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>更新数据库</title>
<style type="text/css">
    html{font-size:12px;}
	    fieldset{width:520px; margin: 0 auto;}
		    legend{font-weight:bold; font-size:14px;}
			    label{float:left; width:270px; margin-left:10px;}
				    .left{margin-left:80px;}
					    .input{width:150px;}
						    span{color: #666666;}
</style>
<script language=JavaScript>

function InputCheck(LoginForm)
{
	if (LoginForm.sec_num.value == "")
	{
		alert("请输入要修改的数据库对应的序号!");
		LoginForm.sec_num.focus();
		return (false);
	}
	if (LoginForm.sec_stat.value == "")
	{
		alert("请输入升级控制状态!");
		LoginForm.sec_stat.focus();
		return (false);
	}
	if (LoginForm.sec_min.value == "")
	{
		alert("请输入IBox最小序列号!");
		LoginForm.sec_min.focus();
		return (false);
	}
	if (LoginForm.sec_max.value == "")
	{
		alert("请输入最大IBox序列号!");
		LoginForm.sec_max.focus();
		return (false);
	}
}

</script>
<?php
session_start();

//检测是否登录，若没登录则转向登录界面
if(!isset($_SESSION['userid'])){
    header("Location:login.html");
    exit();
}

function get_db_server()
{
	$db_server = "10.173.201.228:3306";
	return $db_server;
}

function get_db_user()
{
	$db_user = "fota";
	return $db_user;
}
function get_db_pwd()
{
	$db_pwd = "fota1@#";
	return $db_pwd;
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

function show_data()
{
	/* show data */
	$sql = "SELECT * FROM `fota`";
	// $sql = "SELECT * FROM `fota_machine`";
	$result = mysql_query($sql);

	print( "<table border='1'>");

	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		print( "<tr><td>". $row['timestamp'] . " </td><td>" . $row['sn'] . " </td><td>" . $row['fp'] . " </td><td>" . $row['remoteip'] . " </td><td>" . $row['version'] . '</td><td>' .$row['sn']  . "<td></tr>");
	}

	return $ret;
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

function run_database_command($query)
{
    // $query = "SELECT cpuid FROM license_datasheet where cpuid='".$_GET['cpuid']."'";
    $result = mysql_query($query);
}

function db_insert($get_ver,$get_id,$get_sn,$get_remoteip_dec)
{
	$insert_str = sprintf("INSERT INTO `fota`(`timestamp`, `sn`, `version`, `fp`,`remoteip`) VALUES (now(),'%s','%s','%s','%s')",$get_sn,$get_ver,$get_id,$get_remoteip_dec);
	$result = mysql_query($insert_str);
	logd("------------>$insert_str<--<$ret:$result>--------");

	return 1;
}














function main_activity()
{
	show_sn_data();
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


main_activity();

?>

<body>
<div>
	<fieldset>
		<legend>更新数据库</legend>
		<form name="LoginForm" method="post" action="db_center.php" onSubmit="return InputCheck(this)">
			<p>
			<label for="sec_num" class="label">数据库序号(1/2/3...):</label>
			<input id="sec_num" name="sec_num" type="text" class="input" />
			<p/>
			<p>
			<label for="sec_stat" class="label">升级控制状态（allow/deney/confirm):</label>
			<input id="sec_stat" name="sec_stat" type="sec_stat" class="input" />
			<p/>
			<p>
			<label for="sec_min" class="label">IBox最小序列号:</label>
			<input id="sec_min" name="sec_min" type="sec_min" class="input" />
			<p/>
			<p>
			<label for="sec_max" class="label">IBox最大序列号:</label>
			<input id="sec_max" name="sec_max" type="sec_max" class="input" />
			<p/>
			<p>
			<input type="submit" name="submit" value="更新记录" class="left" />
			</p>
			<p>
			<input type="submit" name="submit_del" value="删除记录" class="left" />
			</p>
		</form>
	</fieldset>
</div>
</body>
