<?php

namespace chatbot\http;

class Gone extends Response
{
    protected $code = 410;
    protected $status = "Gone";
}