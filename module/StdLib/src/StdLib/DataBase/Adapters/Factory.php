<?php
namespace StdLib\DataBase\Adapters;

use StdLib\Singleton;
use StdLib\Application;
use StdLib\DataBase\Adapters\Mysqli;

class Factory extends Singleton
{
	/**
	 * @return \StdLib\DataBase\Adapters\Factory
	 */
	static public function getInstance()
	{
		return parent::getInstance();
	}

	/**
	 * @param unknown $db_name
	 * @return \StdLib\DataBase\Adapters\Mysqli
	 */
	public function createMysqliAdapter($db_name)
	{
		$mysqli = Application::getInstance()
			->getServiceManager()
			->get($db_name)
			->getDriver()
			->getConnection()
			->getResource();

		return new Mysqli($mysqli);
	}
}