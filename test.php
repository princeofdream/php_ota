<?PHP
require_once('basepage.php');
get_html_main_header();
?>

<?php
// By JamesL 20170508 version 1.0.0
session_start();

require_once("debug_util.php");
require_once("db.php");
require("platform.php");


$db_mdb;

//echo $_POST["m_ver"];
//echo $_POST["m_ot_time"];



function show_data($mdb)
{
	$display = 1;

	/* show data */
	$sql = "SELECT * FROM `perseus_tire_base`";
	// $sql = "SELECT * FROM `fota_machine`";
	$result = mysqli_query($mdb, $sql);

	if($display === 1)
	{
		logs( "<table border='1'>");
	}
	$ret = "NULL";
	while($row = mysqli_fetch_array($result))
	{
		if($display === 1)
		{
			logs("<tr><td>". $row['idx'] . " </td><td>" . $row['sn'] . " </td><td>" . $row['vendor'] . " </td><td>" . $row['area'] . " </td><td>" . $row['timestamp'] . " </td><td>" . $row['size'].'</td></tr>');
		}
	}

	// mysqli_close($mdb);
	return $ret;
}


function get_file_md5_sum($file_path)
{
	$md5_file_path = sprintf("%s.md5",$file_path);
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
	return $file_md5;
}

function log_visit_info()
{
}

function get_vendorsion_detail($ver_str)
{
		// **************** START Transfer version to int ********************* //
		// logd("strip from: $ver_str");
		$pos_01 = strpos($ver_str,'.');
		$get_vendor_str_01 = substr($ver_str,0,$pos_01);
		$ver_str_tmp_01 = substr($ver_str,$pos_01+1);

		$pos_02 = strpos($ver_str_tmp_01,'.');
		$get_vendor_str_02 = substr($ver_str_tmp_01,0,$pos_02);
		$ver_str_tmp_02 = substr($ver_str_tmp_01,$pos_02+1);

		$get_vendor_str_03 = $ver_str_tmp_02;
		logd("--> pos_01: $pos_01 --> $get_vendor_str_01 . $get_vendor_str_02 . $get_vendor_str_03");
		// **************** END OF Transfer version to int ********************* //
		return array($get_vendor_str_01 , $get_vendor_str_02 , $get_vendor_str_03);
}

function get_vendorsion_detail_by_ver($ver_str)
{
	// **************** START Transfer version to int ********************* //
	// for version 1.23.456.789
	logd("strip from: $ver_str");

	$ver_info = array(0,0,0,0);
	$get_vendor_str = $ver_str;
	$ver_num_count = 0;
	for ($i0=0; $i0 <= 3; $i0++)
	{
		$ver_num_count ++;
		$current_pos = strpos($get_vendor_str,'.');
		$ver_info[$i0] = substr($get_vendor_str,0,$current_pos);

		if($current_pos < 1)
		{
			$ver_info[$i0] = $get_vendor_str;
			break;
		}
		$get_vendor_str = substr($get_vendor_str,$current_pos+1);
	}
	// logd("get ver -------> $ver_info[0] -- $ver_info[1] -- $ver_info[2] -- $ver_info[3]. count: $ver_num_count");
	$ret_ver = $ver_info[0];
	for($i0 = 0; $i0 < $ver_num_count; $i0++)
	{
	}
	if($ver_num_count == 1)
		return array( $ver_info[0]);
	else if($ver_num_count == 2)
		return array( $ver_info[0], $ver_info[1]);
	else if($ver_num_count == 3)
		return array( $ver_info[0], $ver_info[1],$ver_info[2]);
	else if($ver_num_count == 4)
		return array( $ver_info[0], $ver_info[1],$ver_info[2],$ver_info[3]);
	else
		return $ver_info;
	// **************** END OF Transfer version to int ********************* //
}


function strip_version_str($ver_str,$end_str,$start_str)
{
	// change ***HGOBD-APP-JM-0006-05<<-20161012.BIN>> to 0006-05
	$ver_info = $ver_str;
	if(strlen($end_str) != 0)
	{
		$get_pos = strrpos($ver_info,$end_str);
		if($get_pos > 0)
		{
			$ver_info = substr($ver_info, 0, $get_pos);
		}
	}

	if(strlen($start_str) != 0 )
	{
		$get_pos = strrpos($ver_info,$start_str);
		if($get_pos > 0)
		{
			$ver_info = substr($ver_info, $get_pos+strlen($start_str));
		}
	}

	return $ver_info;
}

function transfer_obd_ver_str_to_int($ver_info)	// 1234xxxx-56 || 1234xxxx ==> 12 34 56
{
	$ver_int_array[0] = 0;
	$ver_int_array[1] = 0;
	$ver_int_array[2] = 0;
	$ver_int_array[3] = 0;

	// $ver_int_aray_len=count($ver_int_array);
	if(strlen($ver_info) < 4)
		return $ver_int_array;

	$ver_array[0]=substr($ver_info,0,2);
	$ver_array[1]=substr($ver_info,2,4);
	$get_sub_ver_tmp = strchr($ver_info,"-");
	$ver_array[2] = substr($get_sub_ver_tmp,1);

	for ($i0=0; $i0 <= 3; $i0++)
	{
		if(preg_match("/[^\d-., ]/",$ver_array[$i0]))
		{
			logd("Not numbers!!!");
			$ver_int_array[0] = -1;
			return $ver_int_array;
		}
	}


	$ver_int_array[0] = intval($ver_array[0]);	//[0] [1]
	$ver_int_array[1] = intval($ver_array[1]);	// [2] [3]
	$ver_int_array[2] = intval($ver_array[2]);
	logd("---> get usr version detail $ver_info ---> $ver_int_array[0] ---> $ver_int_array[1] --> $ver_int_array[2] --> $ver_int_array[3]");
	return $ver_int_array;
}


function update_perseus_base ( $mdb, $get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_area, $get_timestamp, $get_size )
{
	$check_sn_str = sprintf("SELECT * FROM `perseus_tire_base` WHERE `SN`=\"%s\" ",$get_sn);
	logd("sql: $check_sn_str");
	$check_sn_stat = mysqli_query($mdb, $check_sn_str);
	// logd( $check_sn_stat);
	// if($check_sn_stat) {
		$row = mysqli_fetch_array($check_sn_stat);
		logd("get value: " . $row['sn']);
	// }

	if( strlen($row['sn']) == 0 )
	{
		logd("----------------------------Insert info into DB------------------------------------------");
		$ret = perseus_db_insert($mdb,$get_idx,$get_sn,$get_vendor, $get_area, $get_timestamp, $get_size);
		logd("----------------------------Read Info from DB, pre stat: $ret------------------------------------------");
		$ret = show_data($mdb);
		logd("----------------------------End of DB action------------------------------------------");
	}
	else
	{
		logd("----------------------------Update DB info------------------------------------------");
		$sql = sprintf(" UPDATE `perseus_tire_base` SET `idx`=\"%d\",`sn`=\"%s\", `vendor`=\"%s\", `area`=\"%s\", `timestamp` = now(), size=\"%s\" WHERE `sn`=\"%s\" ",$get_idx, $get_sn,$get_vendor,$get_area,$get_size, $get_sn);
		mysqli_query($mdb, $sql);
		logd("----------------------------Read Info from DB------------------------------------------");
		$ret = show_data($mdb);
		logd("----------------------------End of DB action------------------------------------------");
	}

}

function update_project_obd ( $mdb, $get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_hgsoft_platform )
{
	$get_stat = get_update_file($get_hgsoft_platform, $get_vendor,$get_serv);
	if($get_stat == -1)
	{
		print("{\"code\":\"500\",\"msg\":\"Your version is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
	}
}


function update_server_main( $get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_area, $get_timestamp, $get_size, $get_show )
// function update_server_main( $base_info)
{
	logd("-- Usage -->> http://10.173.201.222/perseus/test.php?ver=HGSoft-v1.0.0&sn=440011600000090&id=1482167729 <<--");
	logd("-- Usage -->> http://10.173.201.222/perseus/test.php?platform=ibx&ver=HGSoft-v1.0.0&sn=440011600000090&id=1482167729 <<--");
	logd("-- Usage -->> http://10.173.201.222/perseus/test.php?platform=obd&ver=0401 <<--");
	logd("");
	logd("Debug --------->");

	/* tranform ip to dec */
	// $get_remoteip_dec = $get_remoteip[3] + $get_remoteip[2]*256 + $get_remoteip[1]*256*256 + $get_remoteip[0] *256*256*256;
	$current_tm = date('H:i:s');

	logd("serv : $get_serv : -port- : $get_port: -rtip- : $get_remoteip: -idx- : $get_idx: -sn- : $get_sn: --vd : $get_vendor: -ar- : $get_area: -ts- : $get_timestamp ");
	logd("host  : $get_serv:$get_port");
	// logd("remote ip :$get_remoteip($get_remoteip_dec)");
	logd("idx  : $get_idx");
	logd("sn  : $get_sn");
	logd("vendor  : $get_vendor");
	logd("area  : $get_area");
	logd("timestamp  : $get_timestamp");
	logd("time : $current_tm");

	logd("----------------------------Connect to DB------------------------------------------");
	$db_server = get_db_server();
	$db_user = get_db_user();
	$db_pwd = get_db_pwd();
	logd("DB server : $db_server");
	$mdb = connect_to_mysqli_server($db_server,$db_user,$db_pwd);
	select_database('perseus',$mdb);

	if ( strcmp( $get_show, "1") == 0 ) {
		$check_sn_str = sprintf("SELECT * FROM `perseus_tire_base` WHERE `SN`=\"%s\" ",$get_sn);
		logd("sql: $check_sn_str");
		$check_sn_stat = mysqli_query($mdb, $check_sn_str);
		$row = mysqli_fetch_array($check_sn_stat);
		if (strlen($row['sn']) == 0)
			logs("null");
		else
			logs("{\"index\":\"". $row['idx'] . "\",\"sn\":\"" . $row['sn'] . "\",\"vendor\":\"" . $row['vendor'] . "\",\"area\":\"" . $row['area'] . "\",\"timestamp\":\"" . $row['timestamp'] . "\",\"size\":\"". $row['size'] . "\"}");
	} else {
		update_perseus_base ( $mdb, $get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_area, $get_timestamp, $get_size );
	}

	// logd("----------------------------Disconnect to DB------------------------------------------");
	// disconnect_from_mysqli_server($mdb);
}

function get_value($value)
{
	if (isset($_GET[$value])) {
		$get_value = $_GET[$value];
		return $get_value;
	} elseif (isset($_POST[$value])) {
		$get_value = $_POST[$value];
		return $get_value;
	} else {
		return "";
	}
}


/* Main */

$get_serv = $_SERVER['HTTP_HOST'];
$get_port = $_SERVER["SERVER_PORT"];
$get_remoteip = $_SERVER["REMOTE_ADDR"];

// $get_idx = $_GET['idx'];
$get_idx = get_value("idx");
$get_sn = get_value("sn");
$get_vendor = get_value("vendor");
$get_area = get_value("area");
$get_timestamp = get_value("timestamp");
$get_size = get_value("size");

$get_show = get_value("show");

date_default_timezone_set('Asia/Shanghai');
// $current_dt = date('Y-m-d');
// $current_tm = date('H:i:s');


$base_info = array($get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_area, $get_timestamp );

	logd("idx  : $get_idx");
	logd("sn  : $get_sn");
	logd("vendor  : $get_vendor");
	logd("area  : $get_area");
	logd("timestamp  : $get_timestamp");
	logd("size  : $get_size");

$main_ret=update_server_main( $get_serv, $get_port, $get_remoteip, $get_idx, $get_sn, $get_vendor, $get_area, $get_timestamp, $get_size, $get_show);
if($main_ret == -1)
{
	print("{\"code\":\"500\",\"msg\":\"Your version is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
}
// update_server_main( $base_info );

?>

<?PHP
require_once('basepage.php');
get_html_main_end();
?>

