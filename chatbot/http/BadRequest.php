<?php

namespace chatbot\http;

class BadRequest extends Response
{
    protected $code = 400;
    protected $status = "Bad Request";
}