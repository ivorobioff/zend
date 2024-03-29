<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
	'controllers' => array(
		'invokables' => array(
			'Test\Controllers\Index' => 'Test\Controllers\IndexController'
		),
	),

    'console' => array(
        'router' => array(
            'routes' => array(
            	'test-run-suit' => array(
            		'options' => array(
            			'route'    => 'test suite <suite>',
            			'defaults' => array(
            				'controller' => 'Test\Controllers\Index',
            				'action'     => 'run'
            			)
            		)
            	)
            ),
        ),
    ),
);
