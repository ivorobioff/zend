<?php
namespace StdLib\DataBase;

use StdLib\DataBase\Adapters\Factory;
use StdLib\DataBase\QueryBuilder\Builder;

abstract class DefaultDb extends Builder
{
	protected $_db_name = 'default_db';

	/**
	 * (non-PHPdoc)
	 * @see Builder::_getAdapter()
	 */
	protected function _getAdapter()
	{
		return Factory::getInstance()->createMysqliAdapter($this->_db_name);
	}
}