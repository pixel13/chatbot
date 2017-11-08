<?php

namespace chatbot\rules;

use chatbot\actors\Actor;
use chatbot\Chat;
use chatbot\log\Log;
use chatbot\Message;
use \DateInterval;
use \DateTime;

class EndRule extends  Rule
{
	const DEFAULT_MAX_MESSAGES = 5;
	const DEFAULT_SECONDS = 0;
	const DEFAULT_RANDOM = 0;

    protected function ruleApply($message, $history)
    {
		$maxMessages = $this->getOption("end_frequency.own_messages", self::DEFAULT_MAX_MESSAGES);
		$maxSeconds = $this->getOption("end_frequency.seconds", self::DEFAULT_SECONDS);
		$random = $this->getOption("end_frequency.random", self::DEFAULT_RANDOM);

		if (($maxMessages > 0) && ($maxMessages <= count($history)))
		{
			$counter = 0;
			for ($i = count($history) - 1; $i >= count($history) - $maxMessages; $i--)
			{
				$message = $history[$i];
				if (($message instanceof Message) && ($message->getSender()->getRole() == Actor::ROLE_BOT))
					$counter++;
			}

			if ($counter == $maxMessages)
			{
				Log::log("EndRule applies because last $maxMessages messages have been all sent by Chatbot");
				Chat::getInstance($this->configuration->id)->terminate();
				return true;
			}
		}

		if (($maxSeconds > 0) && (count($history) > 0))
		{
			$now = new DateTime();
			$firstMessage = $history[0];
			if ($firstMessage instanceof Message)
			{
				$start = $firstMessage->getDateTime();
				$limit = $start->add(DateInterval::createFromDateString($maxSeconds . " seconds"));

				if ($now > $limit)
				{
					Log::log("EndRule applies because the chat exists for more than $maxSeconds seconds (" . $now->format("H:i:s") . " > " . $limit->format("H:i:s") . ")");
					Chat::getInstance($this->configuration->id)->terminate();
					return true;
				}
			}
		}

        if ($random > 0)
		{
			$value =  mt_rand(1,100);
			if ($value <= $random)
			{
				Log::log("EndRule applies because the random value $value is in the range of the first $random values");
				Chat::getInstance($this->configuration->id)->terminate();
				return true;
			}
		}

        return false;
    }
}