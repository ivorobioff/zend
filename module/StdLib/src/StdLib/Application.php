<?php
namespace StdLib;

use StdLib\Singleton;
use Zend\Mvc\ApplicationInterface;

class Application extends Singleton
{
	/**
	 * @var ApplicationInterface
	 */
	private $_application;

	/**
	 * @return \StdLib\Application
	 */
	static public function getInstance()
	{
		return parent::getInstance();
	}

	public function injectApplicationObject(ApplicationInterface $app)
	{
		$this->_application = $app;
	}

	public function getServiceManager()
	{
		return $this->_application->getServiceManager();
	}

	public function getConfiguration()
	{
		return $this->_application->getConfiguration();
	}
}