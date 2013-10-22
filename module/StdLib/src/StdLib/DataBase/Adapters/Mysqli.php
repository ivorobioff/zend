<?php
namespace StdLib\DataBase\Adapters;

use StdLib\DataBase\Adapters\AdapterInterface;

class Mysqli implements AdapterInterface
{
	/**
	 * @var \mysqli
	 */
	private $_driver;
	
	public function __construct(\mysqli $driver)
	{
		$this->_driver = $driver;
	}
	
	public function query($sql)
	{
		return $this->_driver->query($sql);
	}
	
	public function escape($str)
	{
		return $this->_driver->escape_string($str);
	}
	
	public function getAffectedRows()
	{
		return $this->_driver->affected_rows;
	}
	
	public function getError()
	{
		return $this->_driver->error;
	}
	
	public function getInsertId()
	{
		$this->_driver->insert_id;
	}
}