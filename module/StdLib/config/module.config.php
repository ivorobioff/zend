<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
    ),
	'view_helpers' => array(
		'invokables'=> array(
			'jsComposer' => 'StdLib\ViewHelpers\JsComposer',
			'jsCore' => 'StdLib\ViewHelpers\JsCore'
		)
	),
	
	'controller_plugins' => array(
		'invokables' => array(
			'isAjax' => 'StdLib\ControllerPlugins\IsAjax',
			'jsBootstrap' => 'StdLib\ControllerPlugins\JsBootstrap',
		)
	),
);
