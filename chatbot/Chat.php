<?php

namespace chatbot;

use chatbot\actors\Bot;
use chatbot\actors\User;

class Chat
{
    /**
     * @var Chat
     */
    private static $instance;

    /**
     * @var array
     */
    private $history = [];

    /**
     * @var Bot
     */
    private $bot;

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $lastMessages = [];

	/**
	 * @var bool
	 */
	private $terminated = false;

	/**
	 * @var bool
	 */
	private $generateAnotherMessage = false;

    private function __construct($id)
    {
        $this->bot = new Bot($id);
        $this->user = new User("Utente");

		Log::log("Creating chat with a User and a Chatbot named " . $id);
    }

	public function start()
	{
		if (!array_key_exists("chat_" . $this->bot->getId(), $_SESSION))
			$_SESSION['chat_' . $this->bot->getId()] = $this;
	}

	public function end()
	{
		session_destroy();
		Log::log("Session destroyed");
	}

	public function terminate()
	{
		$this->terminated = true;
	}

    public static function getInstance($id)
    {
		if (session_status() == PHP_SESSION_NONE)
			session_start();

        if (is_null($_SESSION) || !array_key_exists('chat_' . $id, $_SESSION) || is_null($_SESSION['chat_' . $id]))
            self::$instance = new Chat($id);
		else
			self::$instance = $_SESSION['chat_' . $id];

        return self::$instance;
    }

	public function getHistory()
	{
		return $this->history;
	}

    public function enter($message)
    {
		$this->messageAndReply($message);

		if ($this->generateAnotherMessage)
		{
			$this->generateAnotherMessage = false;
			$this->messageAndReply();
		}
    }

	public function generateAnotherMessage()
	{
		$this->generateAnotherMessage = true;
	}

	private function messageAndReply($message = null)
	{
		Log::log("Entering message '$message'");

		if ($message != '')
			$this->history[] = new Message($this->user, $message);

		$response = $this->bot->reply($message, $this->history);

		$replyMessage = new Message($this->bot, $response);
		$this->lastMessages[] = $replyMessage;
		$this->history[] = $replyMessage;

		Log::log("Generated response '" . $replyMessage->getMessage() . "'");
	}

	/**
	 * @return array
	 */
	public function getMessages()
	{
		$messages = $this->lastMessages;
		$this->lastMessages = [];

		if ($this->terminated)
			$this->end();

		return $messages;
	}

	public function getBotName()
	{
		return $this->bot->getName();
	}

	public function getBotMood()
	{
		return $this->bot->getMood();
	}

	public function getBotAvatar()
	{
		return $this->bot->getAvatar();
	}
}