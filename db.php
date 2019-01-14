<?PHP
/*
$host = "10.173.235.228:3306";
$user = "perseus_tire_base";
$password="fota1@#";
$databasename = "perseus_tire_base";
$baseurl = "http://10.173.201.222/perseus_tire_base/";
$basesslurl = "http://10.173.201.222/perseus_tire_base/";

$upload_version_dir="/var/www/html/perseus_tire_base/version/";
$upload_delta_dir="/var/www/html/perseus_tire_base/version/";

function connect_database() {
    global $host, $user, $password, $databasename;
    $db = mysqli_connect ( $host, $user, $password );
    $tb = mysqli_select_db ( $databasename );
    if ($tb) {
		// echo "-----OK----";
        return true;
    } else {
		// echo "-----False----";
        return false;
    }
}
 */
// connect_database();

function get_db_server()
{
	// $db_server = "193.112.59.210:3306";
	$db_server = "127.0.0.1:3306";
	// $db_server = "libraryclub.cn:3306";
	return $db_server;
}

function get_db_user()
{
	$db_user = "perseus";
	return $db_user;
}
function get_db_pwd()
{
	$db_pwd = "123qweASD";
	return $db_pwd;
}
/*mysqli_connect(server,user,pwd,newlink(optional),clientflag(optional));*/

function connect_to_mysqli_server($db_server,$db_user,$db_pwd)
{
    $result=0;
	logd("connecting to db $db_server, $db_user, $db_pwd");
	$db_mdb = mysqli_connect($db_server,$db_user,$db_pwd);
	// $db_mdb = mysqli_connect("127.0.0.1:3306","root","123qweASD");
	if(!$db_mdb)
	{
		logd("connect db Fail!");
	}
	else
	{
		logd("connect sucess");
	}
	return $db_mdb;
}

function select_database($sel_db,$mdb)
{
	$result  = mysqli_select_db($mdb, $sel_db);
}

function run_database_command($mdb, $query)
{
    // $query = "SELECT cpuid FROM license_datasheet where cpuid='".$_GET['cpuid']."'";
    $result = mysqli_query($mdb, $query);
}

function disconnect_from_mysqli_server($db_mdb)
{
	mysqli_close($db_mdb);
}


function db_insert($mdb,$get_ver,$get_id,$get_sn,$get_remoteip_dec)
{
	$ret = mysqli_select_db( $mdb, "perseus");

	$insert_str = sprintf("INSERT INTO `perseus_tire_base`(`timestamp`, `sn`, `version`, `fp`,`remoteip`) VALUES (now(),'%s','%s','%s','%s')",$get_sn,$get_ver,$get_id,$get_remoteip_dec);
	$result = mysqli_query($mdb, $insert_str);
	logd("------------>$insert_str<--<$ret:$result>--------");

	return 1;
}

function perseus_db_insert($mdb,$get_idx,$get_sn,$get_vendor,$get_area,$get_timestamp)
{
	$ret = mysqli_select_db( $mdb, "perseus");

	$insert_str = sprintf("INSERT INTO `perseus_tire_base`(`idx`, `sn`, `vendor`, `area`,`timestamp`) VALUES ('%d','%s','%s','%s',now())",$get_idx,$get_sn,$get_vendor,$get_area);
	$result = mysqli_query($mdb, $insert_str);
	logd("------------>$insert_str<--<$ret:$result>--------");
	if(!$result)
		return -1;

	return 0;
}

?>
