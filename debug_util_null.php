
<?PHP
function get_debug_info()
{
	// $DEBUG=1;
	$DEBUG=0;

	return $DEBUG;
}

function logd($str)
{
    $DEBUG=get_debug_info();
    if($DEBUG === 1){
		$current_tm = date('H:i:s');
        echo "[ DEBUG $current_tm ]  $str<br />";
    }else{
    }
}

function logs($str)
{
    $DEBUG=get_debug_info();
    if($DEBUG === 1){
		print($str);
    }else{
    }
}

logd("Debug util by JamesL");

?>

