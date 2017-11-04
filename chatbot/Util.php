<?php

namespace chatbot;

class Util 
{
	public static function getBotList()
	{
		$botList = [];
		$available =  glob(self::getBaseDir() . 'config/*.json');
		foreach ($available as $filePath)
		{
			$config = json_decode(file_get_contents($filePath));
			if (!is_null($config) && self::hasProperty($config, 'name') && self::hasProperty($config, 'id'))
				$botList[$config->id] = $config->name;
		}

		return $botList;
	}

	private static function hasProperty($object, $property)
	{
		return (property_exists($object, $property) && ($object->$property != ''));
	}

	private static function getBaseDir()
	{
		$levels = substr_count(__NAMESPACE__, "\\") + 1;
		return dirname(__FILE__) . "/" . str_repeat("../", $levels);
	}
}