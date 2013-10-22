<?php 
namespace Test;

class ResultPrinter extends \PHPUnit_Util_Log_TAP
{
	private $_errors = array();
	
	public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
	{
		parent::addError($test, $e, $time);
		
		$message = $e->getMessage().' in file '.$e->getFile().' on line '.$e->getLine();
		$this->_errors[md5($message)] = $message;
	}
	
	public function hasErrors()
	{
		return (bool) $this->_errors;
	}
	
	public function printErrors()
	{
		foreach ($this->_errors as $error)
		{
			echo $error."\n\r";
		}
	}
}