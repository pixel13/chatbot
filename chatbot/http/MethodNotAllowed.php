<?php

namespace chatbot\http;

class MethodNotAllowed extends Response
{
    protected $code = 405;
    protected $status = "Method Not Allowed";
}