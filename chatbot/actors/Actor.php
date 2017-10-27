<?php

namespace chatbot\actors;

abstract class Actor implements \JsonSerializable
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var boolean
	 */
	protected $isBot = false;

	public function getName()
	{
		return $this->name;
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}

	/**
	 * @return boolean
	 */
	public function isBot()
	{
		return $this->isBot;
	}
}