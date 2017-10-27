<?php

namespace chatbot\actors;

class User extends  Actor
{
	public function __construct($name)
	{
		$this->name = $name;
	}
}