
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<title>HGSoft X server</title>
</head>

<style type="text/css">
body {
	border:none;
	margin:0px auto;
	padding:0px auto;
    background-color: rgb(97, 103, 180);
	text-align: left;
	width:70%;
	float:left;
	margin-left:15%;
	margin-right;15%;
}
.cont_left{
position:relative;
left:50px;
font-size:18px;
font-weight:bold;
}
.links {color: #009900}
div#container{/*width:720px;*/max-height:1920}
div#header {background-color:#99bbbb;}
div#menu {background-color:#ffff99;height:500;width:20%;float:left;}
div#content {background-color:#EEEEEE;height:500;width:80%;float:left;}
div#footer {background-color:#99bbbb;clear:both;text-align:center;}
h1 {margin-bottom:0;}
h2 {margin-bottom:0;font-size:18px;}
ul {margin:0;}
li {list-style:none;}
</style>

<body>


</br>
<?php   
//Accpet the http client request and generate response content.
//As a demo, this function just send "PHP HTTP Server" to client.



//echo $_POST["m_ver"];
//echo $_POST["m_ot_time"];


$db_server = "10.173.201.228";
$db_user = "fota";
$db_pwd = "fota1@#";
/*mysql_connect(server,user,pwd,newlink(optional),clientflag(optional));*/



function logd($str)
{
    $DEBUG=1;
    if($DEBUG === 1){
        echo "$str<br />";
    }else{
    }
}

function is_cpuid_exist($server,$user,$pwd,$display)
{
    $result=0;
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		echo "connect db Fail!";
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		logd("connect sucess");
	}

	mysql_select_db("fota", $mdb);

    $query = "SELECT cpuid FROM license_datasheet where cpuid='".$_GET['cpuid']."'";
    $t_result = mysql_query($query);
    while($row_result = mysql_fetch_array($t_result))
    {
        $result=1;
        logd($row_result['cpuid'] . " --> " . $row_result['wifi_mac']
                . " --> " . $row_result['board'] . " --> " . $row_result['platform']);
    }
	return $result;
}

function db_insert($server,$user,$pwd,$str)
{
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		logd("connect db Fail!");
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		logd("connect sucess");
	}


	mysql_select_db("fota", $mdb);

	$pre_str = "INSERT INTO version VALUES " ;//+ $str;
	$insert_str = sprintf("%s%s",$pre_str,$str);
	logd("------------>$insert_str<----------");
	mysql_query($insert_str);

	mysql_close($mdb);
	return 1;
}

/*
function read_fota_db($server,$user,$pwd,$display)
{
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		echo "connect db Fail!";
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		//echo "connect db OK!";
	}
	
	mysql_select_db("fota", $mdb);

	//mysql_query("INSERT INTO license_datasheet (cpuid, wifi_mac, board, platform) 
		//VALUES ('cpuid_005', 'wifi_223345', 'TVBOX', 'NS115')");

	$sql = "SELECT * FROM `license_datasheet`";
	$result = mysql_query($sql,$mdb);


	if($display === 1)
	{
		echo 'cpuid' . " --> " . 'wifi_mac' . " --> " . 'board' . " --> " . 'platform';
		logd("");
	}
	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		if($display === 1)
		{
			echo $row['cpuid'] . " --> " . $row['wifi_mac'] . " --> " . $row['board'] . " --> " . $row['platform'];
		}
		$ret = $row['cpuid'];
	}

	mysql_close($mdb);
	return $ret;
}
 */


function show_date($server,$user,$pwd)
{
	$display = 1;
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		echo "connect db Fail!";
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		logd("connect db OK!");
	}
	mysql_select_db("fota", $mdb);
	/* show data */
	$result = mysql_query("SELECT * FROM vister_info ORDER BY `vis_bk` ASC");

	if($display === 1)
	{
		echo "<table border='1'>";
		//echo '<tr><td>ver' . " \t\t--> " . '</td><td>date' .'</td></tr>';
	}
	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		if($display === 1)
		{
			echo "<tr><td>". $row['vis_ver'] . " ( " . $row['vis_bk'] . " )</td><td>" . $row['vis_dt'] . '</td><td>' .$row['vis_tm']  . "<td></tr>";
		}
	}

	$sql = sprintf("DELETE FROM `vister_info` WHERE `vis_ver` = \"%s\"","XXXXXX");
	$result = mysql_query($sql,$mdb);
	mysql_close($mdb);
	return $ret;
}

function read_fota_vister_info_db($server,$user,$pwd,$display,$get_ip_dec,$get_dt,$get_tm,$get_ver)
{
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		echo "connect db Fail!";
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		logd("connect db OK!");
	}


	$check_ver = $get_ver;



	mysql_select_db("fota", $mdb);

	// check if vis_ver and date, if no result do insert
	$sql = sprintf("SELECT * FROM `vister_info` WHERE `vis_ver` = \"%s\" AND `vis_dt` = \"%s\"",$check_ver,$get_dt);
	$result = mysql_query($sql,$mdb);
	$row = mysql_fetch_array($result);

	if($row['vis_ver'] == null)
	{
		$sql = sprintf("INSERT INTO vister_info (vis_ip, vis_ver, vis_dt, vis_tm, vis_bk) VALUES ('%s', '%s', '%s', '%s', '%s')",$get_ip_dec,$check_ver,$get_dt,$get_tm,$get_ver);
		mysql_query($sql, $mdb);
		logd("insert $get_ip_dec");
	}
	else if($row['vis_dt'] == $get_dt && $row['vis_ver'] == $check_ver)
	{
		$sql = sprintf("UPDATE vister_info SET vis_tm = \"%s\" WHERE vis_dt = \"%s\" AND vis_ver = \"%s\" ",$get_tm,$get_dt,$check_ver);
		mysql_query($sql, $mdb);
		logd("update $get_ip_dec");
	}
	//else
	//{
		//$check_info = false;
		//while($row = mysql_fetch_array($result))
		//{
			//// if ver is the same, total ++
			//if($row['vis_ver'] == $check_ver)
			//{
				//$sql = sprintf("UPDATE vister_info SET vis_tm = '%s' WHERE vis_ip = '%s' AND path = '%s' ",$row['vis_tm'],$get_ip_dec,$get_dt);
				//mysql_query($sql, $mdb);
				//$check_info = true;
			//}
		//}
		//if($check_info == false)
		//{
			////$sql = sprintf("INSERT INTO vister_info (vis_ip, total, path) VALUES ('%s', '%s', '%s')",$get_ip_dec,1,$get_dt);
			//$sql = sprintf("INSERT INTO vister_info (vis_ip, vis_ver, vis_dt, vis_tm, vis_bk) VALUES ('%s', '%s', '%s', '%s', '%s')",$get_ip_dec,$check_ver,$get_dt,$get_tm,"");
			//mysql_query($sql, $mdb);
		//}
		//logd("update $get_ip_dec");
	//}


	/* show data */
	show_date($server,$user,$pwd);
}


/*
function ip_to_country($server,$user,$pwd,$display,$get_ip_dec,$get_path)
{
	$mdb = mysql_connect($server,$user,$pwd);
	if(!$mdb)
	{
		echo "connect db Fail!";
		die('Could not connect: ' . mysql_error());
	}
	else
	{
		//logd("connect db OK!");
	}
	
	mysql_select_db("fota", $mdb);


	// auto plus 1
	$sql = sprintf("SELECT * FROM `ip-to-country` WHERE `IP_ADDR` <= %d AND `IP_ADDR_2` >= %d ",$get_ip_dec, $get_ip_dec);
	$result = mysql_query($sql,$mdb);
	$row = mysql_fetch_array($result);

	logd("-->". $row['COUNTRY']. "<--" );
	if($row['COUNTRY'] == null)
	{
		logd("get country Error! use CHN as default");
		return "CHN";
	}
	else
	{
		logd("get country :" . $row['COUNTRY']);
		$ret = $row['COUNTRY'];
	}


	mysql_close($mdb);
	return $ret;
}
 */


function dir_path($path)
{
	$path = str_replace('\\', '/', $path);
	if (substr($path, -1) != '/') $path = $path . '/';
	return $path;
}
function dir_list($path, $exts = '', $list = array())
{
	$path = dir_path($path);
	$files = glob($path . '*');
	foreach($files as $v) {
		if (!$exts || preg_match("/\.($exts)/i", $v))
		{
			$list[] = $v;
			// if (is_dir($v))
			// {
			//     $list = dir_list($v, $exts, $list);
			// }
		}
	}
	return $list;
}


function get_version_detail($ver_str)
{
		// **************** START Transfer version to int ********************* //
		// logd("strip from: $ver_str");
		$pos_01 = strpos($ver_str,'.');
		$get_ver_str_01 = substr($ver_str,0,$pos_01);
		$ver_str_tmp_01 = substr($ver_str,$pos_01+1);

		$pos_02 = strpos($ver_str_tmp_01,'.');
		$get_ver_str_02 = substr($ver_str_tmp_01,0,$pos_02);
		$ver_str_tmp_02 = substr($ver_str_tmp_01,$pos_02+1);

		$get_ver_str_03 = $ver_str_tmp_02;
		// logd("--> pos_01: $pos_01 --> $get_ver_str_01 . $get_ver_str_02 . $get_ver_str_03");
		// **************** END OF Transfer version to int ********************* //
		return array($get_ver_str_01 , $get_ver_str_02 , $get_ver_str_03);
}


/* Main */

$get_serv = $_SERVER['HTTP_HOST'];
$get_port = $_SERVER["SERVER_PORT"];
$get_remoteip = $_SERVER["REMOTE_ADDR"];
$get_id = $_GET['id'];

// $get_ver = $_POST['ver'];
$get_ver = $_GET['ver'];
//$get_ot_time = $_POST['m_ot_time'];

date_default_timezone_set('Asia/Shanghai');
$current_dt = date('Y-m-d');
$current_tm = date('H:i:s');


logd("get host -->$get_serv:$get_port<-- remote ip -->$get_remoteip<--get ver --> $get_ver --current date -->$current_dt");


/* get args from url */
$id_info = explode("-",$get_id);

/* tranform ip to dec */
$get_remoteip_dec = $get_remoteip[3] + $get_remoteip[2]*256 + $get_remoteip[1]*256*256 + $get_remoteip[0] *256*256*256;

logd("get remote ip dec -->  $get_remoteip_dec");

/* check and log user info */
$get_data = $get_full_path;
logd("---get ver -->$get_ver--get id-->$get_id<--");

if( strlen($get_ver) != 0 )
{
	logd("insert get ver from form");
	db_insert($db_server,$db_user,$db_pwd,$get_ver);
	// $forward_url = sprintf("http://$get_serv/fota/index.php?id=%s",$get_ver);
	// header("Location: $forward_url");
}
/*
else if(strlen($get_id) != 0)
{
	logd("insert ver from id");
	$ret = read_fota_vister_info_db($db_server,$db_user,$db_pwd,1,$get_remoteip,$current_dt,$current_tm,$get_id);
}
else
	echo "Do not have ID"

	//$ret = read_fota_vister_info_db($db_server,$db_user,$db_pwd,1,$get_remoteip,"2016-05-03","20:10:00","李劲");
*/

// $ver_pos = stripos($get_ver,"-")

$version_prefix = "HGSoft-v";
$version_offset = 0;

$ret = substr_compare($get_ver,$version_prefix , $version_offset ,strlen($version_prefix));
if ( $ret == 0 )
{
	$usr_ver_info = strrchr($get_ver,'v');
	$usr_ver = substr( $usr_ver_info, 1, strlen($usr_ver_info));
	// logd("get user version: $usr_ver");

	$get_usr_ver = get_version_detail($usr_ver);
	logd("get usr version: $get_usr_ver[0] . $get_usr_ver[1] . $get_usr_ver[2] ");

	// **************** Get local list and sort ********************* //
	$ver_list = dir_list('./version/');
	$arrlength=count($ver_list);
	sort($ver_list);
	// **************** Get local list and sort ********************* //


	logd("get dir list :");
	$ret = 0;
	for ($i0=$arrlength-1; $i0 >= 0; $i0--)
	{
		$get_local_ver_info = strrchr($ver_list[$i0], 'v');
		$local_ver_info = substr( $get_local_ver_info,1);
		logd(" -- get local version --> $ver_list[$i0] <----> $local_ver_info <--");


		$local_ver = get_version_detail($local_ver_info);
		/*
		if( $local_ver[0] > $get_usr_ver[0] )
		{
			$get_usr_ver = $local_ver;
		}
		else if( $local_ver[0] == $get_usr_ver[0] )
		{
			if( $local_ver[1] > $get_usr_ver[1] )
			{
				$get_usr_ver = $local_ver;
			}
			else if( $local_ver[1] == $get_usr_ver[1] )
			{
				if( $local_ver[2] > $get_usr_ver[2] )
				{
					$get_usr_ver = $local_ver;
				}
			}
		}
		*/
		$file_path = sprintf("version/v%d.%d.%d/from_v%d.%d.%d/update.zip",
			$local_ver[0], $local_ver[1], $local_ver[2], 
			$get_usr_ver[0], $get_usr_ver[1], $get_usr_ver[2]	);
		$md5_file_path = sprintf("version/v%d.%d.%d/from_v%d.%d.%d/md5.txt",
			$local_ver[0], $local_ver[1], $local_ver[2], 
			$get_usr_ver[0], $get_usr_ver[1], $get_usr_ver[2]	);
		logd("look for file: $file_path .");
		if (file_exists($file_path))
		{
			if( file_exists( $md5_file_path ) )
			{
				$fp = fopen( $md5_file_path , 'r');
				$get_md5_from_file = fread($fp, 1024);
				logd("get md5 from file: $get_md5_from_file");
				fclose($fp);
				$file_md5 = $get_md5_from_file;
			}
			else
			{
				$file_md5 = md5_file($file_path);
				$fp = fopen( $md5_file_path ,'w');
				fwrite($fp,"$file_md5");
				fclose($fp);
			}

			$full_path = sprintf("http://$get_serv/fota/%s",$file_path);
			logd("$full_path exists");

			$get_file_length = filesize($file_path);

			$ret = 1;
			break;
		}
		else
		{
			logd("can not get file: $full_path");
		}
	}

	if( $ret == 1 )
	{
		print("{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\"}");
		logd();
		logd("get update file!");
	}
	else
	{
		logd("Error!");
		print("null");
	}

}
else
{
	echo "version is not $version_prefix !!\n";
	logd();
}








?>
</body>
</html>
