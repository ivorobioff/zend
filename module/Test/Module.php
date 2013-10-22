<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Test;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
    	$config = include __DIR__ . '/config/module.config.php';
    	$config['suites'] = include __DIR__ . '/config/suites.php';
        
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            	'autoregister_zf' => true,
            	'prefixes' => array(
            		'PHPUnit_'     => __DIR__.'/../../vendor/PHPUnit',
            		'PHP_'     => __DIR__.'/../../vendor/PHP'
            	),
            ),
        );
    }
}
