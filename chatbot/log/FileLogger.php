<?php

namespace chatbot\log;

class FileLogger implements Logger
{
	private $log;

	public function __construct($options)
	{
		$folder = dirname(__FILE__) . '/../../' . $options['logdir'];
		if (substr($folder, -1) != '/')
			$folder .= '/';

		if (!file_exists($folder))
			mkdir($folder, 0755);

		$this->log = fopen($folder . $options['filename'], ($options['overwrite'] ? 'w' : 'a'));
		fputs($this->log, "\r\n========================================================r\n");
	}

	public function log($message)
	{
		fputs($this->log, date('Y-m-d H:i:s') . ' - ' . $message . "\r\n");
	}

	public function __destruct()
	{
		fclose($this->log);
	}
}