<?php

use chatbot\ChatApi;

spl_autoload_register(function ($class_name) {
	$path = dirname(__FILE__) . '/../' . $class_name . '.php';
	if (file_exists($path))
		include $path;
});

$api = new ChatApi();
$api->processRequest();