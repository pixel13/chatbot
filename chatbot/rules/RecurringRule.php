<?php

namespace chatbot\rules;

class RecurringRule extends Rule
{
	const DEFAULT_FREQUENCY = 10;

	protected function ruleApply($message, $history)
	{
		$frequency = self::DEFAULT_FREQUENCY;
		if (property_exists($this->configuration, "options") && property_exists($this->configuration->options, "recurring_frequency"))
			$frequency = intval($this->configuration->options->recurring_frequency);

		if (mt_rand(1,100) <= $frequency)
			return true;

		return false;
	}
}