<?PHP

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
	if( strlen ($get_hgsoft_platform[0]) == 0  && strlen($version_prefix_check) != 0 && $version_prefix_check == 0)
	{
		logd("----------------------------Project name Empty, set ibx by default------------------------------------------");
		// $get_hgsoft_platform[0] = "ibx";
		// $get_hgsoft_platform[1] = 1;
		$get_hgsoft_platform = $hgsoft_platform[2];
	}

	return $get_hgsoft_platform;
}

?>
