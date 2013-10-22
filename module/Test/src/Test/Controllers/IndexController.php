<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Test\Controllers;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Test\ResultPrinter;
use StdLib\Application;

class IndexController extends AbstractActionController
{
    public function runAction()
    {
    	$request = $this->getRequest();

    	if (!$request instanceof ConsoleRequest)
    	{
    		throw new \RuntimeException('You can only use this action from a console!');
    	}

    	$class_name = Application::getInstance()->getServiceManager()->get('config');
      	
    	$suite = $request->getParam('suite');

    	$class_name = $class_name['suites'][$suite];

		$suite = new \PHPUnit_Framework_TestSuite();
    	$suite->setName($class_name);

    	$suite->addTestSuite($class_name);

    	$listener = new ResultPrinter();

    	$test_result = new \PHPUnit_Framework_TestResult();
    	
    	$test_result->addListener($listener);

    	$suite->run($test_result);
    	
    	if ($listener->hasErrors())
    	{
    		$listener->printErrors();
    	}

        return '';
    }
}
