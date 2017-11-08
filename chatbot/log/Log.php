<?php

namespace chatbot\log;

class Log
{
	const LOG_TYPE_FILE = 'FileLogger';
	const LOG_TYPE_SYSMESSAGE = 'SystemMessageLogger';

	const LOG_ENABLED = false;
	const LOG_TYPE = self::LOG_TYPE_SYSMESSAGE;

	const LOG_OPTIONS_OVERWRITE = true;
	const LOG_OPTIONS_LOGDIR = 'logs';
	const LOG_OPTIONS_FILENAME = 'chatbot.log';

	/**
	 * @var Logger
	 */
	private static $logger;

	private static function getLogger()
	{
		if (is_null(self::$logger))
			self::$logger = self::createLogger();

		return self::$logger;
	}

	public static function log($message)
	{
		if (!self::LOG_ENABLED)
			return;

		self::getLogger()->log($message);
	}

	private static function createLogger()
	{
		$className = "\\chatbot\\log\\" . self::LOG_TYPE;
		$instance = new $className(self::getOptions());

		return $instance;
	}

	private static function getOptions()
	{
		$options = array();
		$currentClass = new \ReflectionClass(__CLASS__);
		$constants = $currentClass->getConstants();
		foreach ($constants as $name => $value)
		{
			if (!preg_match('/^LOG_OPTIONS_(.+)/', $name, $matches))
				continue;

			$options[strtolower($matches[1])] = $value;
		}

		return $options;
	}
}