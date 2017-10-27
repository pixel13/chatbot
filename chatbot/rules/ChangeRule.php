<?php

namespace chatbot\rules;

use chatbot\Chat;

class ChangeRule extends Rule
{
	const DEFAULT_FREQUENCY = 10;

	protected function ruleApply($message, $history)
	{
		$frequency = $this->getOption("change_frequency", self::DEFAULT_FREQUENCY);
		if (mt_rand(1,100) <= $frequency)
		{
			Chat::getInstance($this->configuration->id)->generateAnotherMessage();
			return true;
		}

		return false;
	}
}