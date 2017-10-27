<?php

namespace chatbot\http;

abstract class Response
{
    protected $code;
    protected $status;
    protected $body;

    function __construct($body = null)
    {
        $this->body = $body;
    }

    function __toString()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " $this->code $this->status");
        if (is_string($this->body))
		{
			echo $this->body;
		}
		else
		{
			header("Content-Type: application/json");

			echo json_encode($this->body);
		}
        exit();
    }
}