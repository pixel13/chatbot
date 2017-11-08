<?php

namespace chatbot\actors;

class System extends Actor
{
	protected $role = self::ROLE_SYSTEM;

	private static $instance;

	private function __construct()
	{}

	public static function getInstance()
	{
		if (is_null(self::$instance))
			self::$instance = new System();

		return self::$instance;
	}
}