<?PHP
function get_html_main_header(){
print('
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-CN" />
<title>HGSoft X server</title>
</head>

<style type="text/css">
body {
	border:none;
	margin:0px auto;
	padding:0px auto;
    background-color: rgb(97, 103, 180);
	text-align: left;
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


</br>
');
}


function get_html_main_end()
{
	print('
</body>
</html>
');
}


get_html_main_header();

?>
