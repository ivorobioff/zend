<?php
namespace StdLib\ControllerPlugins;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class JsBootstrap extends AbstractPlugin
{
	public function __invoke($name)
	{
		$this->getController()->layout()->setVariable('js_bootstrap', $name);
	}
}