<?php

namespace chatbot\actors;

class User extends  Actor
{
	protected $role = self::ROLE_USER;

	public function __construct($name)
	{
		$this->name = $name;
	}
}