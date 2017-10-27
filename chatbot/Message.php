<?php

namespace chatbot;

use \DateTime,
    chatbot\actors\Actor;

class Message implements \JsonSerializable
{
	/**
	 * @var string
	 */
	private $id;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Actor
     */
    private $sender;

    /**
     * @var string
     */
    private $message;

    function __construct($sender, $message)
    {
        $this->dateTime = new DateTime();
        $this->sender = $sender;
        $this->message = $message;
		$this->id = spl_object_hash($this);
    }

    /**
     * @return string
     */
    public function getTimeStr()
    {
        return $this->dateTime->format("H:i:s");
    }

	/**
	 * @return DateTime
	 */
	public function getDateTime()
	{
		return $this->dateTime;
	}

	/**
     * @return Actor
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
}