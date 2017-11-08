<?php

namespace chatbot\log;

use chatbot\Chat;

class SystemMessageLogger implements Logger
{
	private static $logMessage = '';

	public function log($message)
	{
		self::$logMessage .= $message . "\r\n";
	}

	public function __destruct()
	{
		Chat::getInstance($_GET['id'])->addSystemMessage(self::$logMessage);
	}
}