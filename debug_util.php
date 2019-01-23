
<?PHP

$DEBUG=1;

function get_debug_info()
{
	// $DEBUG=1;
	$DEBUG=1;
	return $DEBUG;
}

function set_debug_info($value)
{
	$DEBUG=$value;
}

function logd($str)
{
	$DEBUG=get_debug_info();
	// $DEBUG=0;
    if($DEBUG === 1){
		$current_tm = date('H:i:s');
        echo "[ DEBUG $current_tm ]  $str<br />";
    }else{
    }
}

function logs($str)
{
    // $DEBUG=get_debug_info();
	$DEBUG=1;
    if($DEBUG === 1){
		print($str);
    }else{
    }
}

logd("Debug util by JamesL");

?>

