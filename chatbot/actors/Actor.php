<?php

namespace chatbot\actors;

abstract class Actor implements \JsonSerializable
{
	const ROLE_BOT = 'bot';
	const ROLE_SYSTEM = 'system';
	const ROLE_USER = 'user';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $role;

	public function getName()
	{
		return $this->name;
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}
}