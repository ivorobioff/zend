<?php
namespace StdLib\DataBase\QueryBuilder;

use StdLib\DataBase\Exceptions\MysqlError;

abstract class Base
{
	protected $_db_name;

	static protected $_db;

	public function __construct()
	{
		if (!isset(self::$_db[$this->_db_name]))
		{
			self::$_db[$this->_db_name] = $this->_getAdapter();
		}
	}

	static public function create()
	{
		return new static();
	}

	public function query($sql)
	{
		if (!$res = $this->_db()->query($sql))
		{
			throw new MysqlError($this->_db()->getError());
		}

		return $res;
	}

	protected function _db()
	{
		return self::$_db[$this->_db_name];
	}

	/**
	 * @return \StdLib\DataBase\Adapters\AdapterInterface
	 */
	abstract protected function _getAdapter();
}