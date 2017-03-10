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
    $DEBUG=0;
    if($DEBUG === 1){
		$current_tm = date('H:i:s');
        echo "[ DEBUG $current_tm ]  $str<br />";
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


function get_platform_info($get_platform, $get_ver)
{
	$hgsoft_platform= array
		(
			// project_name, with_db_support
			// "ibx", 1 --> means project ibx, with db support
			// platform, DB, file ext name, keyword, prefix
			array("obd",0,".bin","","",""),
			array("obd_app",0,".apk","","",""),
			array("ibx",1,".zip","v","HGSoft-v",""),
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
	$version_prefix = "HGSoft-v";
	$version_prefix_check = substr_compare($get_ver,$version_prefix , 0 ,strlen($version_prefix));
	if( strlen ($get_hgsoft_platform[0]) == 0  && $version_prefix_check == 0)
	{
		logd("----------------------------Project name Empty, set ibx by default------------------------------------------");
		// $get_hgsoft_platform[0] = "ibx";
		// $get_hgsoft_platform[1] = 1;
		$get_hgsoft_platform = $hgsoft_platform[2];
	}

	return $get_hgsoft_platform;
}

function strip_version_str($ver_str,$end_str,$front_str)
{
	$get_pos = strrpos($ver_str,$end_str);
	if($get_pos > 0)
	{
		$get_ver_str = substr($ver_str, 0, $get_pos);
		$get_ver_str = strrchr($get_ver_str, 'v');
	}
	else
		$get_ver_str = strrchr($ver_str, 'v');
	logd("---$ver_str--<$get_pos>------>>> $get_ver_str <<<---------");
	return $get_ver_str;
}

function get_update_file($get_hgsoft_platform, $get_ver, $get_serv)
{
	$version_prefix = $get_hgsoft_platform[4];
	$ret = strstr($get_ver,$version_prefix);
	// for OBD and OBD app
	$get_pl_ver = 0;
	$get_up_ver = 0;
	$get_sub_ver = 0;

	$get_local_pl_ver = 0;
	$get_local_up_ver = 0;
	$get_local_sub_ver = 0;

	logd("-------$ret");

	if ( $ret != null || strlen($version_prefix) == 0 )
	{
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
				$get_pl_ver = intval(substr($usr_ver,0,2));
				$get_up_ver = intval(substr($usr_ver,2,4));
				$get_sub_ver_tmp = strchr($usr_ver,"-");
				$get_sub_ver = intval(substr($get_sub_ver_tmp,1));
				logd("---> get version $usr_ver ---> $get_pl_ver ---> $get_up_ver");
			}
		}
		// logd("get user version: $usr_ver");
		$get_usr_ver = get_version_detail_by_ver($usr_ver);

		logd("get usr version: $get_usr_ver[0] . $get_usr_ver[1] . $get_usr_ver[2] .$get_usr_ver[3]");

		// **************** Get local list and sort ********************* //
		$ver_dir = sprintf("./version/%s/",$get_hgsoft_platform[0]);
		$ver_list = dir_list($ver_dir);
		$arrlength=count($ver_list);
		sort($ver_list);
		// **************** Get local list and sort ********************* //


		logd("get dir list :");
		$ret = 0;
		for ($i0=$arrlength-1; $i0 >= 0; $i0--)
		{
			$get_ver_info = substr( $ver_list[$i0],2);
			// $get_ver_list = $ver_list[$i0];
			$local_ver_info = "";
			if( strlen($version_prefix) != 0 )
			{
				$get_ver_list = strip_version_str($ver_list[$i0],$get_hgsoft_platform[2],"");
				$get_local_ver_info = strrchr($get_ver_list, $get_hgsoft_platform[3]);
			$local_ver_info = substr( $get_local_ver_info,1);
			}
			else
			{
				if( strcmp($get_hgsoft_platform[0], "obd") ==0 || strcmp($get_hgsoft_platform[0], "obd_app") ==0)
				{
					$get_ver_list = strip_version_str($ver_list[$i0],"-","");
					$get_local_ver_info = strstr($get_ver_list,"JM-");
					$local_ver_info_full=substr( $get_local_ver_info,strlen("JM-"));
					$local_ver_info = substr($local_ver_info_full,0,4);

					$get_local_pl_ver = intval(substr($local_ver_info_full,0,2));
					$get_local_up_ver = intval(substr($local_ver_info_full,2,4));
					$get_local_sub_ver = intval(substr($local_ver_info_full,5,7));
				}
			}
			logd(" -- get local version --> $get_ver_list <----> $local_ver_info <--");
			logd("--usr: $get_pl_ver.$get_up_ver.$get_sub_ver---local: $get_local_pl_ver.$get_local_up_ver.$get_local_sub_ver-----");


			$local_ver = get_version_detail_by_ver($local_ver_info);
			logd("local version $local_ver[0].$local_ver[1].$local_ver[2].$local_ver[3]");
			logd("user version $get_usr_ver[0].$get_usr_ver[1].$get_usr_ver[2].$get_usr_ver[3]");
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
			if(strcmp($get_hgsoft_platform[0],"obd") == 0 ||
				strcmp($get_hgsoft_platform[0],"obd_app") == 0
			)
			{
				if($get_pl_ver != $get_local_pl_ver)
				{
					logd("platform version does not the same,skip...");
					continue;
				}
				if( strcmp(strrchr($ver_list[$i0], $get_hgsoft_platform[2]), $get_hgsoft_platform[2]) == 0)
				{
				$file_path = $get_ver_info;
				$md5_file_path = sprintf("%s.md5",$get_ver_info);
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
				$get_file_length = filesize($file_path);
				$arrlen = count($local_ver);
				$get_local_ver = "$local_ver[0]";
				for( $i0 = 1; $i0 < $arrlen;$i0++)
				{
					$get_local_ver = "$get_local_ver.$local_ver[$i0]";
				}

					for($i1 = 0; $i1 < count($local_ver); $i1++)
					{
						logd("--------get_local:$local_ver[$i1]  --- $get_usr_ver[$i1] ---");
						if( $local_ver[$i1] > $get_usr_ver[$i1] )
						{
							logd("local_ver > get_usr_ver");
							// print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$get_local_ver\"}}");
							print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$local_ver_info_full\"}}");
				return;
			}
						else if( $get_local_up_ver > $get_up_ver)
						{
							logd("$get_local_up_ver > $get_up_ver");
							// print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$get_local_ver\"}}");
							print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$local_ver_info_full\"}}");
							return;
					}
						else if( $get_local_up_ver == $get_up_ver)
						{
							logd("get local up ver -> $get_local_up_ver == $get_up_ver");
							if( $get_local_sub_ver >= $get_sub_ver)
							{
								logd("---local sub ver:$get_local_sub_ver, get $get_sub_ver");
								// print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$get_local_ver\"}}");
								print("{\"code\":\"200\",\"msg\":\"ok\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$local_ver_info_full\"}}");
								return;
							}
							else
							{
								logd("get local_sub_ver < get_sub_ver");
								// logd("---sub ver:$get_local_sub_ver, get $get_sub_ver");
							}
						}
					}
					// print("{\"code\":\"300\",\"msg\":\"Your app is up to date!\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$get_local_ver\"}}");
					print("{\"code\":\"300\",\"msg\":\"Your app is up to date!\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\",\"version\":\"$local_ver_info_full\"}}");
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
		logd("version is not $version_prefix !!");
		logd();
		print("{\"code\":\"500\",\"msg\":\"Your version is incorrect!\",\"data\":{\"url\":\"\",\"md5\":\"\",\"length\":\"\",\"version\":\"\"}}");
	}
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

	$get_hgsoft_platform = get_platform_info($get_platform,$get_ver);
	logd("platform(fixed) : $get_hgsoft_platform[0]");
	logd("db support : $get_hgsoft_platform[1]");
	logd("file type : $get_hgsoft_platform[2]");
	logd("version key word : $get_hgsoft_platform[3]");
	logd("version prefix : $get_hgsoft_platform[4]");

	$db_server = get_db_server();
	$db_user = get_db_user();
	$db_pwd = get_db_pwd();
	logd("DB server : $db_server");

	if(strlen($get_hgsoft_platform[0]) == 0 )
	{
		print("{\"code\":\"100\",\"msg\":\"Platform not found!\",\"data\":{\"url\":\"$full_path\",\"md5\":\"$file_md5\",\"length\":\"$get_file_length\"}}");
		return;
	}

	if( $get_hgsoft_platform[1] == 1)
	{
		logd("----------------------------Connect to DB------------------------------------------");
		$mdb = connect_to_mysql_server($db_server,$db_user,$db_pwd);
		select_database('fota',$mdb);
	}

	$version_prefix = "HGSoft-v";
	$version_prefix_check = substr_compare($get_ver,$version_prefix , 0 ,strlen($version_prefix));
	if (strcmp("$get_hgsoft_platform[0]","ibx") == 0 && $version_prefix_check == 0)
	{
		logd("go to ibx project update....");
		update_project_ibx ( $mdb, $get_serv, $get_port, $get_remoteip, $get_id, $get_sn, $get_ver );
	}
	else
	{
		logd("go to ibx project update....");
		if( strcmp($get_hgsoft_platform[0], "obd") ==0 ||
			strcmp($get_hgsoft_platform[0], "obd_app") ==0)
		{
			if(strlen($get_ver) < 4)
				return -1;
			$get_pl_ver = intval(substr($get_ver,0,2));
			$get_up_ver = intval(substr($get_ver,2,4));
			logd("---> get version $get_ver ---> $get_pl_ver ---> $get_up_ver");
			switch ($get_pl_ver)
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


update_server_main( $get_serv, $get_port, $get_remoteip, $get_platform, $get_id, $get_sn, $get_ver);
// update_server_main( $base_info );


?>
