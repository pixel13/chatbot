<?php

namespace chatbot\rules;

use chatbot\Log;

class PatternRule extends Rule
{
	/**
	 * @param string $message
	 * @param array $history
	 * @return string
	 */
	public function processMessage($message, $history)
	{
		Log::log("Trying to apply " . __CLASS__);
		if (($responses = $this->match($message)) != false)
		{
			Log::log("Rule applies");
			return $this->bestAnswer($message, $history, $responses);
		}

		Log::log("Rule doesn't apply, passing to successor");
		if (!is_null($this->successor))
			return $this->successor->processMessage($message, $history);

		return null;
	}

	private function match($message)
	{
		foreach ($this->configuration->pattern as $regex => $responses)
		{
			Log::log("Verifico se '$message' fa match con '$regex'");
			if (preg_match("/$regex/i", $message))
				return $responses;
		}

		return false;
	}

	protected function ruleApply($message, $history)
	{
		return true;
	}
}