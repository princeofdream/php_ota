<?php
$host = "127.0.0.1:3306";
$user = "fota";
$password="fota1234";
$databasename = "fota";
$baseurl = "http://10.173.235.228/fota/fota/";
$basesslurl = "http://10.173.235.228/fota/fota/";
// $baseurl="http://127.0.0.1/";
// $basesslurl="https://127.0.0.1/";
// $upload_version_dir='/var/www/OTA/';
// $upload_delta_dir='/var/www/OTA/';
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
// connect_database();

?>
