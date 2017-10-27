<?php

namespace chatbot\rules;

class AnswersRule extends Rule
{
	protected function ruleApply($message, $history)
	{
		$message = trim($message);
		return substr($message, -1) == '?';
	}
}