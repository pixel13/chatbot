<?php

namespace chatbot\rules;

use chatbot\log\Log;

abstract class Rule
{
    /**
     * @var \stdClass
     */
    protected $configuration;

    /**
     * @var Rule
     */
    protected $successor;

    /**
     * @param \stdClass $configuration
     */
    public function configure($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param Rule $successor
     */
    public function setSuccessor($successor)
    {
        $this->successor = $successor;
    }

    protected function bestAnswer($message, $history, $configuration = null)
    {
		if (is_null($configuration))
		{
			$ruleName = strtolower($this->getRuleName());
			$configuration = $this->configuration->$ruleName;
		}

		$answer = $this->choose($configuration);
        return $this->replaceVariables($answer);
    }

	/**
	 * @param \stdClass $optionsObj
	 * @return string
	 */
	protected function choose($optionsObj)
	{
		$options = (array) $optionsObj;
		$this->normalizeProbabilities($options);

		Log::log('Choosing between :' . print_r($options, true));

		$random = mt_rand(1, 100);

		Log::log("Random value equals to: " . $random);

		$probability = 0;
		foreach ($options as $option => $optionProb)
		{
			if (($random > $probability) && ($random <= $probability + $optionProb))
				return $option;

			$probability += $optionProb;
		}

		return null;
	}

	private function normalizeProbabilities(&$options)
	{
		if (count($options) == 0)
			return;

		if (count($options) == 1)
		{
			$options[key($options)] = 100;
			return;
		}

		$total = 0;
		$undefined = [];
		foreach ($options as $key => $value)
		{
			if ($value == '')
				$undefined[] = $key;

			$val = intval($value);
			$options[$key] = $val;
			$total += $val;
		}

		if (count($undefined) > 0)
		{
			$value = intval(ceil(max(0, 100 - $total) / count($undefined)));
			foreach ($undefined as $key) {
				$options[$key] = $value;
			}
		}
		else
		{
			$increment = intval(ceil(max(0, 100 - $total) / count($options)));
			foreach ($options as $key => $value)
			{
				$options[$key] += $increment;
			}
		}
	}

	private function getRuleName()
	{
		$name = null;
		if (preg_match('/([A-Za-z0-9_-]+)Rule/', get_class($this), $matches))
			$name = $matches[1];

		return $name;
	}

	public function showChain()
	{
		$chain = $this->getRuleName();
		if (!is_null($this->successor))
			$chain .= " -> " . $this->successor->showChain();

		return $chain;
	}

    /**
     * @param string $message
     * @param array $history
     * @return string
     */
    public function processMessage($message, $history)
	{
		Log::log("Trying to apply " . get_called_class());
		if ($this->ruleApply($message, $history))
		{
			Log::log("Rule applies");
			return $this->bestAnswer($message, $history);
		}

		Log::log("Rule doesn't applies, passing to successor");
		if (!is_null($this->successor))
			return $this->successor->processMessage($message, $history);

		return null;
	}

	/**
	 * @param string $key Should be in the form key or key.subkey or key.subkey.subsubkey, etc.
	 * @param mixed $default The default value if option is not set
	 * @return mixed
	 */
	protected function getOption($key, $default)
	{
		$parts = explode(".", $key);
		array_unshift($parts, "options");

		$config = $this->configuration;
		foreach ($parts as $element)
		{
			if (!property_exists($config, $element))
				return $default;

			$config = $config->$element;
		}

		return $config;
	}

	protected abstract function ruleApply($message, $history);

	private function replaceVariables($answer)
	{
		if (!property_exists($this->configuration, "variables"))
			return $answer;

		if (!preg_match_all('/\\${([^}]+)}/', $answer, $matches))
			return $answer;

		foreach ($matches[1] as $key => $variable)
		{
			if (!property_exists($this->configuration->variables, $variable))
				continue;

			$value = $this->choose($this->configuration->variables->$variable);
			$answer = str_replace($matches[0][$key], $value, $answer);
		}

		return $answer;
	}
}