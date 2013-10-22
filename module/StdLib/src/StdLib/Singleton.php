<?php
namespace StdLib;

class Singleton
{
	static protected $_instances;
	
	/**
	 * @return Singleton
	 */
	static public function getInstance()
	{
		$class = get_called_class();
	
		if (!isset(static::$_instances[$class]))
		{
			static::$_instances[$class] = new static();
		}
	
		return static::$_instances[$class];
	}
}