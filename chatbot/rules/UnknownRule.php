<?php

namespace chatbot\rules;

class UnknownRule extends Rule
{
	protected function ruleApply($message, $history)
	{
		return true;
	}
}