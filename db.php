<?PHP
/*
$host = "10.173.235.228:3306";
$user = "fota";
$password="fota1@#";
$databasename = "fota";
$baseurl = "http://10.173.201.222/fota/";
$basesslurl = "http://10.173.201.222/fota/";

$upload_version_dir="/var/www/html/fota/version/";
$upload_delta_dir="/var/www/html/fota/version/";

function connect_database() {
    global $host, $user, $password, $databasename;
    $db = mysql_connect ( $host, $user, $password );
    $tb = mysql_select_db ( $databasename );
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
	$db_server = "10.173.235.228:3306";
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

function connect_to_mysql_server($db_server,$db_user,$db_pwd)
{
    $result=0;
	$db_mdb = mysql_connect($db_server,$db_user,$db_pwd);
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

?>
