<?php
namespace StdLib\ViewHelpers;

use Zend\Form\View\Helper\AbstractHelper;

use StdLib\JsComposer\Exceptions\NoStart;
use StdLib\Application;
use StdLib\JsComposer\Composer;

class JsComposer extends AbstractHelper
{
	public function __invoke()
	{
		$bootstrap_name = 'js_dummy';
		
		if (isset($this->getView()->js_bootstrap))
		{
			$bootstrap_name = $this->getView()->js_bootstrap;
		}
			
		$bootstrap_name = strtolower($bootstrap_name);

		$bin = md5($bootstrap_name);

		if (config('is_production'))
		{
			return '<script src="/js/app/bin/'.$bin.'.js"></script>';
		}

		$composer = new Composer(config('js_composer'));

		try
		{
			$composer
				->addBootstrap('common.js')
				->addBootstrap($bootstrap_name.'.js')
				->process()
				->save($bin.'.js');
		}
		catch (NoStart $ex)
		{
			return '';
		}

		return '<script src="/js/app/bin/'.$bin.'.js"></script>';
	}
}