<?PHP
require_once('basepage.php');
// get_html_main_header();
?>

<?php
// By JamesL 20170508 version 1.0.0
session_start();

require_once("debug_util_null.php");
require_once("db.php");
require("platform.php");


$db_mdb;

//echo $_POST["m_ver"];
//echo $_POST["m_ot_time"];



function show_data($mdb)
{
	$display = 1;

	/* show data */
	$sql = "SELECT * FROM `fota`";
	// $sql = "SELECT * FROM `fota_machine`";
	$result = mysql_query($sql);

	if($display === 1)
	{
		logs( "<table border='1'>");
	}
	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		if($display === 1)
		{
			logs("<tr><td>". $row['timestamp'] . " </td><td>" . $row['sn'] . " </td><td>" . $row['fp'] . " </td><td>" . $row['remoteip'] . " </td><td>" . $row['version'] . '</td><td>' .$row['sn']  . "<td></tr>");
		}
	}

	// mysql_close($mdb);
	return $ret;
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
		logd("--> pos_01: $pos_01 --> $get_ver_str_01 . $get_ver_str_02 . $get_ver_str_03");
		// **************** END OF Transfer version to int ********************* //
		return array($get_ver_str_01 , $get_ver_str_02 , $get_ver_str_03);
}

function get_version_detail_by_ver($ver_str)
{
	// **************** START Transfer version to int ********************* //
	// for version 1.23.456.789
	logd("strip from: $ver_str");

	$ver_info = array(0,0,0,0);
	$get_ver_str = $ver_str;
	$ver_num_count = 0;
	for ($i0=0; $i0 <= 3; $i0++)
	{
		$ver_num_count ++;
		$current_pos = strpos($get_ver_str,'.');
		$ver_info[$i0] = substr($get_ver_str,0,$current_pos);

		if($current_pos < 1)
		{
			$ver_info[$i0] = $get_ver_str;
			break;
		}
		$get_ver_str = substr($get_ver_str,$current_pos+1);
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

function get_update_file($get_hgsoft_platform, $get_ver, $get_serv)
{
	// version info -> "obd",0,".BIN","","",""
	$version_prefix = $get_hgsoft_platform[4];
	$ret = strstr($get_ver,$version_prefix);
	logd("get_ver--> $get_ver");
	// for OBD and OBD app
	$usr_ver_array = array(0,0,0,0);
	$server_ver_array = array(0,0,0,0);


	logd("-------$ret");

	if ( $ret != null || strlen($version_prefix) == 0 )	// can not get ver from version_prefix
	{
		// **************** Start Get user version ********************* //
		$usr_ver = "";
		if( strlen($version_prefix) != 0 )
		{
			$usr_ver_info = strrchr($get_ver, $get_hgsoft_platform[3]);
			$usr_ver = substr( $usr_ver_info, 1, strlen($usr_ver_info));
		}
		else
		{
			if( strcmp($get_hgsoft_platform[0], "obd") ==0 || strcmp($get_hgsoft_platform[0], "obd_app") ==0)
			{
				$usr_ver = $get_ver;
				if(strlen($usr_ver) < 4)
				{
					logd("version error");
					return -1;
				}
				$usr_ver_array = transfer_obd_ver_str_to_int($usr_ver);
				if($usr_ver_array[0] == -1 )
					return -1;
			}
		}
		logd("get usr version: $usr_ver_array[0] . $usr_ver_array[1] . $usr_ver_array[2] .$usr_ver_array[3]");
		// **************** End of Get user version ********************* //

		// **************** Start Get local list and sort ********************* //
		$ver_dir = sprintf("./version/%s/",$get_hgsoft_platform[0]);
		$ver_list = dir_list($ver_dir);
		$arrlength=count($ver_list);
		sort($ver_list);
		// **************** End of Get local list and sort ********************* //


		logd("get dir list :");
		$ret = 0;
		for ($i0=$arrlength-1; $i0 >= 0; $i0--)
		{
			$update_file_path = substr( $ver_list[$i0],2);
			$local_ver_info = "";
			$get_file_ext = strrchr($update_file_path, ".");
			logd("get file ext: $get_file_ext");
			if( strcmp($get_file_ext, ".md5") == 0)
			{
				logd("Not a correct update file!");
				continue;
			}

			// **************** Start Get server version ********************* //
			if( strlen($version_prefix) != 0 )
			{
				// TODO
				logd("for_ibx!!!!!!!!!!!!!!!!!!!!");
			}
			else
			{
				if( strcmp($get_hgsoft_platform[0], "obd") ==0 || strcmp($get_hgsoft_platform[0], "obd_app") ==0)
				{
					$server_ver = strip_version_str($update_file_path,"-","JM-");		//change ***HGOBD-APP-JM-0006-05<<-20161012.BIN>> to 0006-05
					$server_ver_array = transfer_obd_ver_str_to_int($server_ver);

				}
			}

			logd("server version detail $server_ver_array[0].$server_ver_array[1].$server_ver_array[2].$server_ver_array[3]");
			logd("user version detail $usr_ver_array[0].$usr_ver_array[1].$usr_ver_array[2].$usr_ver_array[3]");
			// **************** End of Get server version ********************* //

			if(strcmp($get_hgsoft_platform[0],"obd") == 0 ||
				strcmp($get_hgsoft_platform[0],"obd_app") == 0 )
			{
				if($usr_ver_array[0] != $server_ver_array[0])
				{
					logd("platform version does not the same,skip...");
					continue;
				}
				logd("Compareing --> $update_file_path<----> $get_hgsoft_platform[2] <--");
				if( strcasecmp(strrchr( strtolower($update_file_path) ,  strtolower($get_hgsoft_platform[2])), $get_hgsoft_platform[2]) == 0)
				{
					logd("Get server update file's ext name OK!");
					$file_md5 = get_file_md5_sum($update_file_path);

					$full_path = sprintf("http://$get_serv/fota/%s",$update_file_path);
					$get_file_length = filesize($update_file_path);
					for($i1 = 0; $i1 < count($server_ver_array); $i1++)
					{
						logd("--------server ver[$i1]: $server_ver_array[$i1], user ver[$i1]: $usr_ver_array[$i1].");
						if( $server_ver_array[$i1] > $usr_ver_array[$i1] )
						{
							logd("server ver[$i1]: $server_ver_array[$i1]  > user ver[$i1]: $usr_ver_array[$i1]");
							print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$server_ver\"}}");
							return;
						}
						else if( $server_ver_array[$i1] < $usr_ver_array[$i1] )
						{
							break;
						}
					}
					print("{\"code\":\"300\",\"msg\":\"Your app is up to date!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
					return;
				}
				else
				{
					logd("Not specify file type!");
					continue;
				}
			}
			else
			{
				$update_file_path = sprintf("version/ibx/v%d.%d.%d/from_v%d.%d.%d/update.zip",
					$local_ver[0], $local_ver[1], $local_ver[2], 
					$get_usr_ver[0], $get_usr_ver[1], $get_usr_ver[2]	);
				logd("=========>> get file path : $update_file_path");
				$md5_file_path = sprintf("version/ibx/v%d.%d.%d/from_v%d.%d.%d/md5.txt",
					$local_ver[0], $local_ver[1], $local_ver[2], 
					$get_usr_ver[0], $get_usr_ver[1], $get_usr_ver[2]	);
				logd("look for file: $update_file_path .");
				if (file_exists($update_file_path))
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
						$file_md5 = md5_file($update_file_path);
						$fp = fopen( $md5_file_path ,'w');
						fwrite($fp,"$file_md5");
						fclose($fp);
					}

					$full_path = sprintf("http://$get_serv/fota/%s",$update_file_path);
					logd("$full_path exists");

					$get_file_length = filesize($update_file_path);

					$ret = 1;
					break;
				}
				else
				{
					logd("can not get file: $full_path");
				}
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
			logd("Error! File not found!");
			print("{\"code\":\"300\",\"msg\":\"Your app is up to date!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
			// print("null");
			return $ret;
		}

	}
	else
	{
		logd("version is not correct !!! version prefix will be $version_prefix !!");
		logd();
		return -1;
	}
}


// SN:	44 - 00 - 1 - 16 - 0 - 000007 - 5
// provice - xx - device_type - year - batch - number - checksum

function check_sn_to_update($get_sn,$mdb)
{
	$sql = "SELECT * FROM `fota_machine`";
	$result = mysql_query($sql);

	if(strlen($get_sn) != 15)
	{
		logd("Error: serial number is incorrect!");
		return -2;
	}

	logd( "<table border='2'>");
	$ret = "NULL";
	while($row = mysql_fetch_array($result))
	{
		logd( "<tr><td>". $row['sec_num'] . " </td><td>" . $row['s_stat'] . " </td><td>" . $row['s_min'] . " </td><td>" . $row['s_max'] . "<td></tr>");
		if(strcmp($row['s_stat'], "allow") == 0)
		{
			$sn_prefix = substr($get_sn,0,5);
			$sn_year = substr($get_sn,5,2);
			$sn_batch = substr($get_sn,7,1);
			$sn_data = intval(substr($get_sn,8,6));
			$sn_checksum = substr($get_sn,14,1);
			logd("---get sn---$sn_prefix-----$sn_year---$sn_batch----$sn_data----$sn_checksum--");
			$sn_min_prefix = substr($row['s_min'],0,5);
			$sn_min_year = substr($row['s_min'],5,2);
			$sn_min_batch = substr($row['s_min'],7,1);
			$sn_min_data = intval(substr($row['s_min'],8,6));
			$sn_min_checksum = substr($row['s_min'],14,1);
			logd("---get min sn---$sn_min_prefix-----$sn_min_year---$sn_min_batch----$sn_min_data----$sn_min_checksum--");
			$sn_max_prefix = substr($row['s_max'],0,5);
			$sn_max_year = substr($row['s_max'],5,2);
			$sn_max_batch = substr($row['s_max'],7,1);
			$sn_max_data = intval(substr($row['s_max'],8,6));
			$sn_max_checksum = substr($row['s_max'],14,1);
			logd("---get min sn---$sn_max_prefix-----$sn_max_year---$sn_max_batch----$sn_max_data----$sn_max_checksum--");

			if(strcmp($sn_prefix, $sn_min_prefix) == 0 &&
				strcmp($sn_year, $sn_min_year) == 0 &&
				strcmp($sn_batch, $sn_min_batch) == 0 )
			{
				if( $sn_data >= $sn_min_data && $sn_data <= $sn_max_data )
				{
					logd("bingo!  Check update files!!!");
					return 0;
				}
			}

		}
	}

	return -1;
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
		logd("----------------------------Insert info into DB sn=0------------------------------------------");
		db_insert($mdb,$get_ver,$get_id,$get_sn,$get_remoteip_dec);
		logd("----------------------------Read Info from DB------------------------------------------");
		$ret = show_data($mdb);
		logd("----------------------------End of DB action------------------------------------------");
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
		$ver_list = dir_list('./version/ibx/');
		$arrlength=count($ver_list);
		sort($ver_list);
		// **************** Get local list and sort ********************* //

		// **************** Check if sn is in update list ********************* //
		// select_database('fota',$mdb);
		$update_stat = check_sn_to_update($get_sn,$mdb);
		if($update_stat < 0)
		{
			logd("Your machine is not in update list!!!");
			switch($update_stat)
			{
				case -1:
					print("{\"code\":\"600\",\"msg\":\"Unknow!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
					break;
				case -2:
					print("{\"code\":\"600\",\"msg\":\"Serial number is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
					break;
				default:
					print("null");
					break;
			}
			return $update_stat;
		}
		// select_database('fota',$mdb);
		// **************** End Check if sn is in update list ********************* //

		logd("get dir list :");
		$ret = 0;
		for ($i0=$arrlength-1; $i0 >= 0; $i0--)
		{
			$get_file_ext = strrchr($ver_list[$i0], ".");
			logd("get file ext: $get_file_ext");
			if( strcmp($get_file_ext, ".md5") == 0)
			{
				continue;
			}
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
			$file_path = sprintf("version/ibx/v%d.%d.%d/from_v%d.%d.%d/update.zip",
				$local_ver[0], $local_ver[1], $local_ver[2], 
				$get_usr_ver[0], $get_usr_ver[1], $get_usr_ver[2]	);
			$md5_file_path = sprintf("version/ibx/v%d.%d.%d/from_v%d.%d.%d/md5.txt",
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
			logd("");
			return $ret;
		}

	}
	else
	{
		logd("version is not $version_prefix !!");
		logd();
	}
}

function update_project_obd ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver, $get_hgsoft_platform )
{
	$get_stat = get_update_file($get_hgsoft_platform, $get_ver,$get_serv);
	if($get_stat == -1)
	{
		print("{\"code\":\"500\",\"msg\":\"Your version is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
	}
}


function update_server_main( $get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver )
// function update_server_main( $base_info)
{
	logd("-- Usage -->> http://10.173.201.222/fota/test.php?ver=HGSoft-v1.0.0&sn=440011600000090&id=1482167729 <<--");
	logd("-- Usage -->> http://10.173.201.222/fota/test.php?platform=ibx&ver=HGSoft-v1.0.0&sn=440011600000090&id=1482167729 <<--");
	logd("-- Usage -->> http://10.173.201.222/fota/test.php?platform=obd&ver=0401 <<--");
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

	$get_hgsoft_platform = get_platform_info($get_platform,$get_ver);
	logd("platform(fixed) : $get_hgsoft_platform[0]");
	logd("db support : $get_hgsoft_platform[1]");
	logd("file type : $get_hgsoft_platform[2]");
	logd("version key word : $get_hgsoft_platform[3]");
	logd("version prefix : $get_hgsoft_platform[4]");


	if(strlen($get_hgsoft_platform[0]) == 0 )
	{
		print("{\"code\":\"100\",\"msg\":\"Platform not found!\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\"}}");
		return;
	}

	if( $get_hgsoft_platform[1] == 1)
	{
		logd("----------------------------Connect to DB------------------------------------------");
		$db_server = get_db_server();
		$db_user = get_db_user();
		$db_pwd = get_db_pwd();
		logd("DB server : $db_server");
		$mdb = connect_to_mysql_server($db_server,$db_user,$db_pwd);
		select_database('fota',$mdb);
	}

	$version_prefix = "HGSoft-v";
	$version_prefix_check = substr_compare($get_ver,$version_prefix , 0 ,strlen($version_prefix));
	if (strcmp("$get_hgsoft_platform[0]","ibx") == 0 && $version_prefix_check == 0)
	{
		logd("go to ibx project update....$version_prefix_check<--");
		update_project_ibx ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver );
	}
	else
	{
		logd("go to obd project update....");
		if( strcmp($get_hgsoft_platform[0], "obd") ==0 ||
			strcmp($get_hgsoft_platform[0], "obd_app") ==0)
		{
			if(strlen($get_ver) < 4)
				return -1;
			$usr_ver_array = transfer_obd_ver_str_to_int($get_ver);
			logd("---> get version $get_ver ---> $usr_ver_array[0].$usr_ver_array[1].$usr_ver_array[2].$usr_ver_array[3]");
			switch ($usr_ver_array[0])
			{
				case 1:		//obd_simcom_version
					$get_hgsoft_platform[5] = "simcom";
					break;
				case 2:		//obd_ec20_version
					$get_hgsoft_platform[5] = "ec20";
					break;
				case 3:		//obd_zte_version
					$get_hgsoft_platform[5] = "zte";
					break;
				case 4:		//obd_bt_version
					$get_hgsoft_platform[5] = "bt";
					break;
				case 5:		//obd_texi_version
					$get_hgsoft_platform[5] = "texi";
					break;
				default:
					$get_hgsoft_platform[5] = "";
					break;
			}
		}
		update_project_obd ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver, $get_hgsoft_platform );
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


$main_ret=update_server_main( $get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver);
if($main_ret == -1)
{
	print("{\"code\":\"500\",\"msg\":\"Your version is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
}
// update_server_main( $base_info );

?>

<?PHP
require_once('basepage.php');
// get_html_main_end();
?>

