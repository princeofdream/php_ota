
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
//echo $_POST["m_ver"];
//echo $_POST["m_ot_time"];

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
/*mysql_connect(server,user,pwd,newlink(optional),clientflag(optional));*/

$db_mdb;


function logd($str)
{
    $DEBUG=1;
    if($DEBUG === 1){
        echo "$str<br />";
    }else{
    }
}

function connect_to_mysql_server($db_server,$db_user,$db_pwd)
{
    $result=0;
	$db_mdb = mysql_connect($db_server,$db_user,$db_pwd);
	if(!$db_mdb)
	{
		echo "connect db Fail!";
	}
	else
	{
		logd("connect sucess");
	}
	return $db_mdb;
}

function select_database($sel_db,$db_mdb)
{
	$result  = mysql_select_db($sel_db, $db_mdb);
}

function run_database_command($query)
{
    // $query = "SELECT cpuid FROM license_datasheet where cpuid='".$_GET['cpuid']."'";
    $result = mysql_query($query);
}

function disconnect_from_mysql_server($db_mdb)
{
	mysql_close($db_mdb);
}


function db_insert($mdb,$get_ver,$get_id,$get_sn,$get_remoteip_dec)
{
	$ret = mysql_select_db("fota", $mdb);

	$insert_str = sprintf("INSERT INTO `fota`(`timestamp`, `sn`, `version`, `fp`,`remoteip`) VALUES (now(),'%s','%s','%s','%s')",$get_sn,$get_ver,$get_id,$get_remoteip_dec);
	$result = mysql_query($insert_str);
	logd("------------>$insert_str<--<$ret:$result>--------");

	return 1;
}


function show_data($mdb)
{
	$display = 1;

	mysql_select_db("fota", $mdb);
	/* show data */
	$sql = "SELECT * FROM `fota`";
	$result = mysql_query($sql);

	if($display === 1)
	{
		echo "<table border='1'>";
	}
	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		if($display === 1)
		{
			echo "<tr><td>". $row['timestamp'] . " </td><td>" . $row['sn'] . " </td><td>" . $row['fp'] . " </td><td>" . $row['remoteip'] . " </td><td>" . $row['version'] . '</td><td>' .$row['sn']  . "<td></tr>";
		}
	}

	// mysql_close($mdb);
	return $ret;
}

function read_fota_vister_info_db($server,$user,$pwd,$display,$get_ip_dec,$get_dt,$get_tm,$get_ver)
{
	// $mdb = mysql_connect($server,$user,$pwd);
	$check_ver = $get_ver;



	mysql_select_db("fota", $mdb);

	$sql = sprintf("SELECT * FROM `fota`");
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

	// mysql_close($mdb);

	/* show data */
	show_data($server,$user,$pwd);
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

function update_obd_app()
{
}

function get_platform_info($get_platform)
{
	$hgsoft_platform= array
		(
			// project_name, with_db_support
			// "ibx", 1 --> means project ibx, with db support
			array("obd",0),
			array("obdapp",0),
			array("obd_bt",0),
			array("ibx",1),
			// array("Volvo",22,18),
			// array("BMW",15,13),
			// array("Saab",5,2),
			// array("Land Rover",17,15)
		);

	$get_hgsoft_platform[0] = "";
	$get_hgsoft_platform[1] = 0;

	$hgsoft_platform_arrlen=count($hgsoft_platform);
	if( strlen ($get_platform) != 0 )
	{
		for ($i0=$hgsoft_platform_arrlen-1; $i0 >= 0; $i0--)
		{
			// $ret = substr_compare($get_platform, $hgsoft_platform[$i0] , 0 ,strlen($get_platform));
			$ret = strcmp($get_platform, $hgsoft_platform[$i0][0]);
			if($ret == 0)
			{
				$get_hgsoft_platform = $hgsoft_platform[$i0];
				break;
			}
		}
	}
	if( strlen ($get_hgsoft_platform[0]) == 0 )
	{
		// logd("----------------------------Project name Empty, set ibx by default------------------------------------------");
		$get_hgsoft_platform[0] = "ibx";
		$get_hgsoft_platform[1] = 1;
	}

	return $get_hgsoft_platform;
}

function update_project_ibx ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver )
{
	$check_sn_str = sprintf("SELECT * FROM `fota` WHERE `sn` = %s ",$get_sn);
	logd($check_sn_str);
	$check_sn_stat = mysql_query($check_sn_str);
	$sn_stat = mysql_fetch_array($check_sn_stat);
	logd(" Get current sn stat : ");
	logd($sn_stat['sn']);

	$get_remoteip_dec = $get_remoteip[3] + $get_remoteip[2]*256 + $get_remoteip[1]*256*256 + $get_remoteip[0] *256*256*256;

	if( strlen($sn_stat['sn']) == 0 )
	{
		logd("----------------------------Insert info into DB------------------------------------------");
		db_insert($mdb,$get_ver,$get_id,$get_sn,$get_remoteip_dec);
		logd("----------------------------Read Info from DB------------------------------------------");
		$ret = show_data($mdb);
		logd("----------------------------End of DB action------------------------------------------");
		// $ret = read_fota_vister_info_db($db_server,$db_user,$db_pwd,1,$get_remoteip,$get_id);
		// $forward_url = sprintf("http://10.173.235.228/fota/index.php?id=%s",$get_ver);
		// header("Location: $forward_url");
	}
	else
	{
		logd("----------------------------Update DB info------------------------------------------");
		$sql = sprintf(" UPDATE `fota` SET `timestamp`=now(),`version`=\"%s\",`fp`=\"%s\",`remoteip`=\"%s\" WHERE `sn`=\"%s\" ",$get_ver,$get_id, $get_remoteip_dec,$get_sn);
		mysql_query($sql, $mdb);
		logd("----------------------------Read Info from DB------------------------------------------");
		$ret = show_data($mdb);
		logd("----------------------------End of DB action------------------------------------------");
	}
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
			return $ret;
		}
		else
		{
			logd("Error!");
			print("null");
			return $ret;
		}

	}
	else
	{
		echo "version is not $version_prefix !!\n";
		logd();
	}
}

function update_project_obd ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver, $get_platform )
{
}


function update_server_main( $get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver )
// function update_server_main( $base_info)
{
	logd("---- Usage ------->> http://10.173.201.222/fota/test.php?platform=obd&ver=HGSoft-v19.9.1&sn=440011600000075&id=1482167729 <<----------");
	logd();
	logd();
	logd("Debug --------->");

	/* tranform ip to dec */
	$get_remoteip_dec = $get_remoteip[3] + $get_remoteip[2]*256 + $get_remoteip[1]*256*256 + $get_remoteip[0] *256*256*256;
	$current_tm = date('H:i:s');

	logd("host  : $get_serv:$get_port");
	logd("remote ip :$get_remoteip($get_remoteip_dec)");
	logd("version  : $get_ver");
	logd("id  : $get_id");
	logd("platform  : $get_platform");
	logd("time : $current_tm");

	$get_hgsoft_platform = get_platform_info($get_platform);
	logd("platform(fixed) : $get_hgsoft_platform[0]");
	logd("db support : $get_hgsoft_platform[1]");

	$db_server = get_db_server();
	$db_user = get_db_user();
	$db_pwd = get_db_pwd();
	logd("DB server : $db_server");

	if( $get_hgsoft_platform[1] == 1)
	{
		logd("----------------------------Connect to DB------------------------------------------");
		$mdb = connect_to_mysql_server($db_server,$db_user,$db_pwd);
		select_database('fota',$mdb);
	}

	if (strcmp("$get_hgsoft_platform[0]","ibx") == 0)
	{
		update_project_ibx ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver );
	}
	else
	{
		update_project_obd ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver, $get_platform );
	}

	if( $get_hgsoft_platform[1] == 1)
	{
		logd();
		logd("----------------------------Disconnect to DB------------------------------------------");
		disconnect_from_mysql_server($mdb);
	}

}




/* Main */

$get_serv = $_SERVER['HTTP_HOST'];
$get_port = $_SERVER["SERVER_PORT"];
$get_remoteip = $_SERVER["REMOTE_ADDR"];

$get_id = $_GET['id'];
$get_sn = $_GET['sn'];
$get_ver = $_GET['ver'];
$get_platform = $_GET['platform'];

date_default_timezone_set('Asia/Shanghai');
// $current_dt = date('Y-m-d');
// $current_tm = date('H:i:s');


$base_info = array($get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver );


update_server_main( $get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver);
// update_server_main( $base_info );


?>
</body>
</html>