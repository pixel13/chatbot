<?php

namespace chatbot\rules;

use chatbot\Log;

class GenericRule extends Rule
{
	const APPLY_REGEXP = true;
	const DEFAULT_FREQUENCY = 90;
	// This regexp should detect a valid text in Italian language
	const REGEXP = "^([0-9A-Za-zòàùèéì']{1,12}(\\s?[,.':!?-])?\\s{1,3})*[0-9A-Za-zòàùèéì']{1,12}(\\s?[,.':!?-])?$";

	protected function ruleApply($message, $history)
	{
		$useRegexp = $this->getOption("generic_apply.regexp", self::APPLY_REGEXP);
		$frequency = $this->getOption("generic_apply.frequency", self::DEFAULT_FREQUENCY);
		if ((strtolower($useRegexp) == 'true') || ($useRegexp == 1))
		{
			if (!preg_match("/" . self::REGEXP . "/", trim($message)))
				return false;
		}
		elseif ($frequency > 0)
		{
			$random = mt_rand(1, 100);
			if ($random > $frequency)
			{
				Log::log("Not applying rule because $random is greater than $frequency");
				return false;
			}
		}

		return true;
	}
}