<?php
namespace StdLib\ControllerPlugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class IsAjax extends AbstractPlugin
{
	public function __invoke()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
}