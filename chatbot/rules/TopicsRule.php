<?php

namespace chatbot\rules;

class TopicsRule extends Rule
{
	protected function ruleApply($message, $history)
	{
		return ($message == '');
	}
}