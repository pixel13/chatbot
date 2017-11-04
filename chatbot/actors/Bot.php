<?php

namespace chatbot\actors;

use chatbot\Log;
use chatbot\rules\Rule;

class Bot extends Actor
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var  string
	 */
	private $mood;

    /**
     * @var Rule
     */
    private $rules;

	protected $isBot = true;

    /**
     * @param string $id
     */
    function __construct($id)
    {
        $config = json_decode(file_get_contents($this->getBaseDir() . "config/" . strtolower($id) . ".json"));
        if (is_null($config))
            throw new \RuntimeException("Cannot initialize $id bot: file not found " . $this->getBaseDir() . "config/" . strtolower($id) . ".json");

		$this->id = $id;
        $this->name = $config->name;
		$this->mood = $config->mood;
        $this->buildRulesChain($config);
		$this->writeFrontendConfig($id, $config);
    }

    public function reply($message, $history)
    {
		Log::log("Processing message with rule chain: " . $this->rules->showChain());

		$response = null;
		if ($this->rules instanceof Rule)
        	$response = $this->rules->processMessage($message, $history);

		if (is_null($response))
			$response = "...";

		return $response;
    }

    private function getBaseDir()
    {
        $levels = substr_count(__NAMESPACE__, "\\") + 1;
        return dirname(__FILE__) . "/" . str_repeat("../", $levels);
    }

    private function buildRulesChain($config)
    {
		$rules = [];
        foreach ($config as $ruleName => $ruleConfig)
        {
            $className = "chatbot\\rules\\" . ucfirst(strtolower($ruleName)) . "Rule";
			if (!class_exists($className))
				continue;

			$rule = new $className();
			if ($rule instanceof Rule)
				$rule->configure($config);

			$rules[] = $rule;
        }

		for ($i = count($rules) - 1; $i > 0; $i--)
		{
			$rules[$i - 1]->setSuccessor($rules[$i]);
		}

		if (count($rules) > 0)
		{
			$this->rules = $rules[0];
			Log::log("Rule chain: " . $this->rules->showChain());
		}
	}

	private function writeFrontendConfig($id, $config)
	{
		if (!property_exists($config, "options"))
			return;

		$configDir = $this->getBaseDir() . 'public/config';
		if (!file_exists($configDir))
			mkdir($configDir, 0755);

		file_put_contents($configDir . "/" . strtolower($id) . ".json", json_encode($config->options));
		Log::log("Options written in file: " . $configDir . "/" . strtolower($id) . ".json");
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getMood()
	{
		return $this->mood;
	}
}