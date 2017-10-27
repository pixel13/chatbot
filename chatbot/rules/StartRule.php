<?php

namespace chatbot\rules;

use chatbot\actors\Bot;
use chatbot\Message;

class StartRule extends  Rule
{
    protected function ruleApply($message, $history)
    {
        if (count($history) == 0)
            return true;

        foreach ($history as $message)
        {
            if (($message instanceof Message) && ($message->getSender() instanceof Bot))
                return false;
        }

        return true;
    }
}