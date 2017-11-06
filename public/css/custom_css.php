<?php
header("Content-type: text/css");

$css = file_get_contents("main.css");
if (array_key_exists("avatar", $_GET) && $_GET['avatar'] != '')
{
	$css = str_replace("avatar.svg", $_GET['avatar'], $css);
}

echo $css;