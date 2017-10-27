<?php

namespace chatbot;

class Log
{
	const LOG_ENABLED = false;
	const LOG_OVERWRITE = true;
	const LOG_DIR = 'logs/';
	const LOG_FILE = 'chatbot.log';

	private static $logger;

	private static function getLogger()
	{
		if (is_null(self::$logger))
		{
			$logDir = dirname(__FILE__) . '/../' . self::LOG_DIR;
			if (!file_exists($logDir))
				mkdir($logDir, 0755);

			self::$logger = fopen($logDir . self::LOG_FILE, (self::LOG_OVERWRITE ? 'w' : 'a'));
			fputs(self::$logger, "\r\n========================================================r\n");
			self::closeOnExit();
		}

		return self::$logger;
	}

	public static function log($message)
	{
		if (!self::LOG_ENABLED)
			return;

		fputs(self::getLogger(), date('Y-m-d H:i:s') . ' - ' . $message . "\r\n");
	}

	private static function closeOnExit()
	{
		register_shutdown_function(function() {
			fclose(self::$logger);
		});
	}
}