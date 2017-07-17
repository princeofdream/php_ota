<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<title>Nufront X server</title>
</head>

<style type="text/css">
body {
	border:none;
	margin:0px auto;
	padding:0px auto;
    background-color: rgb(97, 103, 180);
	text-align: center;
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
<div id="container" align=center>
	<div id="header"align=center>
		<h1>Nufront X Server</h1>
	</div>

	<div id=main_cont>
		<div id="menu" align=center>
<!--
			<h2>Menu</h2>
			<ul>
			<li>HTML</li>
			<li>CSS</li>
			<li>JavaScript</li>
			</ul>
-->
		</div><!--end Menu-->

		<div id="content" align=left>
			<p class=cont_left>Nufront Verify Server
				<a href="./verify.php" >Start Verify!</a>
			</p>
			<p class=cont_left>UTF-8 测试</p>
			<p class=cont_left>Nufront James Verify Server
				<a href="./jvry.php?cpuid=1a2b3c4d&mac=00:11:22:33:44:55" >Start James Verify!</a>
			</p>
			<p class=cont_left>
				<a href="/nginx_status" >Nufront X Server Status</a>
			</p>
			<p class=cont_left>
				<a href="/OTA/tl7689/guardphone/versioninfo.xml" >Nufront Guard Phone update XML</a>
			</p>
<!--
			<p class=cont_left>
				<a href="http://172.16.34.35:8080/stat" >rtmp stat</a>
			</p>
			<p class=cont_left>
				<a href="/test.html" >rtmp live test</a>
			</p>
			<p class=cont_left>
				<a href="/test_vod.html" >rtmp vod test</a>
			</p>
			<p class=cont_left>
				<a href="/test01.php" >test01</a>
			</p>
-->
			<p class=cont_left>
				<a href="/git" >Git server</a>
			</p>
			<p class=cont_left>
				<span>check local setting</span>
			<p class=cont_left>
				<a href="/tz.php" target="_blank" class="links">pin</a>
				<a href="/phpmyadmin" target="_blank" class="links">phpmyadmin</a>
			<p class=cont_left>
				<a href="/p.php" target="_blank" class="links">phpinfo</a>
				<a href="/phpPgAdmin" target="_blank" class="links">phpPgAdmin</a>
			</p>
			</p>
			</p>
			
			<?php
				$db_server = "localhost";
				$db_user = "nufront";
				$db_pwd = "nufront.com";
				
				function db_query($server,$user,$pwd)
				{
				  $result=0;
					$mdb = mysql_connect($server,$user,$pwd);
					if(!$mdb)
					{
						echo "connect db Fail!";
						die('Could not connect: ' . mysql_error());
					}
					else
					{
					}
					
					mysql_select_db("nufront_license_management", $mdb);
				
				    $query = "SELECT board FROM license_datasheet";
				    $t_result = mysql_query($query);
				    while($row_result = mysql_fetch_array($t_result))
				    {
				        $result=1;
				        echo($row_result['cpuid'] . " --> " . $row_result['wifi_mac']
				                . " --> " . $row_result['board'] . " --> " . $row_result['platform']);
				        echo("<br />");
				    }
					return $result;
				}
				
/*
				echo "<table class=cont_left border='2'>";
				echo "<tr><td>";
				echo "row	3, cell 1";
				echo "</td><td>";
				echo "row 3, cell 2";
				echo "</td></tr>";
				
				echo "<tr><td>";
				echo "row	4, cell 1";
				echo "</td><td>";
				echo "row 4, cell 2";
				echo "</td></tr>";
				
				echo "</table>";
 */
			?>
			
		</div><!--end content-->
		<div id="footer">Copyright nufront.com By James Lee
		</div>

<?php
	//Run as daemon process.
	function run()
	{
		//$cpuid = $_GET['cpuid'];
		//echo "$cpuid\r\n";
	}
 
	//Entry point.
	run();
?>

	</div>
</div>
</body>
</html>
