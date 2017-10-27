<?php

namespace chatbot;

use chatbot\http\BadRequest;
use chatbot\http\Gone;
use chatbot\http\MethodNotAllowed;
use chatbot\http\Ok;

// TODO sta classe fa schifo
class ChatApi
{
    public function processRequest()
    {
		$id = $this->getId();
		$method = $this->getMethod();

		if ($method == 'GET')
		{
			$messages = Chat::getInstance($id)->getMessages();

			if (session_status() == PHP_SESSION_NONE)
				echo new Gone($messages);

			echo new Ok($messages);
		}

        $postBody = file_get_contents('php://input');
        $payload = json_decode($postBody);
        if (($postBody == '') || (is_null($payload)) || !property_exists($payload, 'message'))
            echo new BadRequest("Empty or wrong body: a valid JSON with a 'message' property is expected");

        Chat::getInstance($id)->enter($payload->message);
        echo new Ok();
    }

	private function getId()
	{
		$id = null;
		if (is_array($_GET) && array_key_exists('id', $_GET) && ($_GET['id'] != ''))
			$id = $_GET['id'];
		if (is_null($id))
			echo new BadRequest("Chatbot id must be specified in the request querystring: api.php?id=...");

		return $id;
	}

	private function getMethod()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if (($method != 'POST') && ($method != 'GET'))
			echo new MethodNotAllowed();

		return $method;
	}
}