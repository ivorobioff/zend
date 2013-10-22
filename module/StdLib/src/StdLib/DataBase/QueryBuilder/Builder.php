<?php
namespace StdLib\DataBase\QueryBuilder;

use StdLib\DataBase\QueryBuilder\ResultFormat;
use StdLib\DataBase\QueryBuilder\Base;

abstract class Builder extends Base
{
	const MATCH_IN_BOOLEAN_MODE = 'IN BOOLEAN MODE';
	const MATCH_IN_NATURAL_LANGUAGE_MODE = 'IN NATURAL LANGUAGE MODE';
	const MATCH_IN_NATURAL_LANGUAGE_MODE_WITH_QUERY_EXPANSION = 'IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION';
	const MATCH_WITH_QUERY_EXPANSION = 'WITH QUERY EXPANSION';

	/**
	 * Настройки таблицы
	 */
	protected $_table_name;
	protected $_table_alias = '';
	protected $_order_by = '';
	protected $_primary_key = 'id';

	/**
	 * Прототип буфера запросов
	 * @var array
	 */
	private $_init_query = array(
		'select' => array(),
		'where' => array(),
		'orderBy' => array(),
		'groupBy' => array(),
		'duplicate' => '',
		'limit' => '',
		'join' => array(),
		'having' => array(),
	);

	private $_query_buffer = array();
	private $_last_query = '';
	private $_query_return = false;

	public function __construct()
	{
		parent::__construct();

		$this->clear();
	}

	public function setQueryReturnMode()
	{
		$this->_query_return = true;
		return $this;
	}

	/**
	 * $table->select('col1, col2, col3');
	 */
	public function select($q = '')
	{
		$this->_query_buffer['select'][] = $q;
		return $this;
	}

	public function havingQuery($q, $glue = 'AND')
	{
		$this->_query_buffer['having'][] = $glue.' '.$q;
		return $this;
	}

	/**
	 * $table->where('col1 = 10');
	 * @param string $type
	 * @param string $q
	 */
	public function whereQuery($q, $glue = 'AND')
	{
		$this->_query_buffer['where'][] = $glue.' '.$q;
		return $this;
	}

	public function match($match, $against, $mode = self::MATCH_IN_BOOLEAN_MODE)
	{
		$q = 'MATCH ('.$match.') AGAINST(\''.$this->escape($against).'\' '.$mode.')';

		return $this->whereQuery($q);
	}


	public function having($key, $value, $glue = 'AND')
	{
		$this->_query_buffer['having'][] = $glue.' '.$this->_buildWhereQuery($key, $value);
		return $this;
	}

	/**
	 * $table->where('col1', 10);
	 * $table->where('col1!=', '10');
	 * $table->where('col1 =', '10');
	 * $table->where('col1 >', 10);
	 * $table->where('col1 <', 10);
	 * $table->where('col1', array(1, 2, 4));
	 * $table->where('col1 LIKE', '%value%');
	 */
	public function where($key, $value, $glue = 'AND')
	{
		$this->_query_buffer['where'][] = $glue.' '.$this->_buildWhereQuery($key, $value);
		return $this;
	}

	/**
	 * Построить квери для использования в WHERE
	 * @param mixed $key
	 * @param mixed $value
	 * @return string
	 */
	private function _buildWhereQuery($key, $value)
	{
		if (is_array($value))
		{
			return $key.' IN ('.$this->_prepareValues($value).')';
		}

		if (is_null($value))
		{
			return $key.' IS NULL';
		}

		$eq = $this->_getSignsCond($key)  ? '' : '=';
		return $key.$eq.'\''.$this->escape($value).'\'';
	}

	private function _getSignsCond($q)
	{
		return strpos($q, '=')
			|| strpos(strtolower($q), ' like')
			|| strpos($q, '>')
			|| strpos($q, '<');
	}

	public function clear()
	{
		$this->_query_buffer = $this->_init_query;
		$this->_query_return = false;
	}

	public function setAlias($alias)
	{
		$this->_table_alias = $alias;
		return $this;
	}

	public function getAlias()
	{
		return $this->_table_alias;
	}

	public function prepareAlias()
	{
		return  $this->_table_alias ? 'AS '.$this->_table_alias : '';
	}

	public function getTableName()
	{
		return $this->_table_name;
	}

	public function escape($str)
	{
		return $this->_db()->escape($str);
	}

	public function limit($param1, $param2 = null)
	{
		$this->_query_buffer['limit'] = 'LIMIT '.intval($param1);

		if ($param2)
		{
			$this->_query_buffer['limit'] .= ', '.intval($param2);
		}

		return $this;
	}

	public function orderBy($field, $direction = 'DESC')
	{
		$this->_query_buffer['orderBy'][] = $this->escape($field).' '.$this->escape($direction);

		return $this;
	}

	public function groupBy($field)
	{
		$this->_query_buffer['groupBy'][] = $this->escape($field);
		return $this;
	}

	public function join(Builder $table, $cond, $type = 'LEFT JOIN')
	{
		$this->_query_buffer['join'][] = $type.' '.$table->getTableName().' '.$table->prepareAlias().' ON '.$cond;

		return $this;
	}

	/**
	 * $table->update('a=a+2');
	 * $this->update('a', 2);
	 * $this->update('a=a+', 2);
	 * $this->update(array('a'=> 2, 'b' => '3'));
	 * @return int
	 */
	public function update($data, $value = false)
	{
		if (is_string($data))
		{
			if ($value !== false)
			{
				$data = array($data => $value);
			}
		}

		$sql = 'UPDATE '.$this->_table_name.
			' SET '.$this->_prepareUpdates($data).
			' '.$this->_prepareWheres();

		if ($this->_query_return)
		{
			$this->clear();
			return $sql;
		}

		$this->query($sql);

		$this->clear();

		return $this->_db()->getAffectedRows();
	}

	/**
	 * $table->duplicate('a=a+1');
	 * $table->duplicate('a=a+', 1);
	 * $table->duplicate('a', 1);
	 * $table->duplicate(array('a' => 1, 'b' => 2));
	 */
	public function duplicate($data, $value = false)
	{
		if (is_string($data))
		{
			if ($value !== false)
			{
				$data = array($data => $value);
			}
		}

		$this->_query_buffer['duplicate'] = 'ON DUPLICATE KEY UPDATE '.$this->_prepareUpdates($data);
		return $this;
	}

	public function insert(array $data)
	{
		$sql = 'INSERT INTO '.$this->_table_name.' ('.$this->_prepareKeys($data).')
				VALUES('.$this->_prepareValues($data).') '.$this->_query_buffer['duplicate'];


		if ($this->_query_return)
		{
			$this->clear();
			return $sql;
		}

		$res = $this->query($sql);

		$this->clear();

		return $res ? $this->_db()->getInsertId() : false;
	}

	public function insertAll(array $data)
	{
		$values = '';
		$d = '';

		foreach ($data as $row)
		{
			$values .= $d.'('.$this->_prepareValues($row).')';
			$d = ',';
		}

		$sql = 'INSERT INTO '.$this->_table_name.' ('.$this->_prepareKeys($data[0]).')
				VALUES'.$values.' '.$this->_query_buffer['duplicate'];

		if ($this->_query_return)
		{
			$this->clear();
			return $sql;
		}

		$this->query($sql);

		$this->clear();

		return $this->_db()->getAffectedRows();
	}

	public function delete()
	{
		$sql = 'DELETE FROM '.$this->_table_name.' '.$this->_prepareWheres();

		if ($this->_query_return)
		{
			$this->clear();
			return $sql;
		}

		$this->query($sql);

		$this->clear();

		return $this->_db()->getAffectedRows();
	}

	/**
	 * Получить объект для получения результата запроса в спец. формате
	 * @return ResultFormat
	 */
	public function createResultFormat()
	{
		return new ResultFormat($this);
	}

	/**
	 * Проставя проверка на наличие результата
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return boolean
	 */
	public function check()
	{
		$res = $this->fetchOne();

		return $res ? true : false;
	}

	public function fetchOne()
	{
		$res = $this->limit(1)->_fetch();

		return $res ? $res[0] : array();
	}

	public function fetchAll()
	{
		return $this->_fetch();
	}

	private function _fetch()
	{
		$sql = 'SELECT '.$this->_prepareSelects().
			' FROM '.$this->_table_name.' '.$this->prepareAlias().
			' '.$this->_prepareJoins().
			' '.$this->_prepareWheres().
			' '.$this->_prepareGroupBys().
			' '.$this->_prepareHavings().
			' '.$this->_prepareOrderBys().
			' '.$this->_query_buffer['limit'];

		if ($this->_query_return)
		{
			$this->clear();
			return $sql;
		}

		$res = $this->_select($sql);

		$this->clear();

		return $res;
	}


	public function getResult($sql)
	{
		return $this->_select($sql);
	}

	public function getPrimaryKey()
	{
		return $this->_primary_key;
	}

	public function getLastQuery()
	{
		return $this->_last_query;
	}

	public function query($sql)
	{
		$this->_last_query = $sql;

		return parent::query($sql);
	}

	private function _select($sql)
	{
		$data = array();

		$res = $this->query($sql);

		while ($row = $res->fetch_assoc())
		{
			$data[]=$row;
		};

		return $data;
	}

	private function _prepareUpdates($data)
	{
		if (is_string($data))
		{
			return $data;
		}

		$updates = '';
		$d = '';

		foreach ($data as $k => $v)
		{
			$eq = strpos($k, '=') ? '' : '=';

			$updates .=$d.$k.$eq.(is_null($v) ? ' NULL ' : '\''.$this->escape($v).'\'');
			$d = ',';

			$eq = '';
		}

		return $updates;
	}

	private function _prepareJoins()
	{
		return implode(' ', $this->_query_buffer['join']);
	}

	private function _prepareGroupBys()
	{
		if (!$this->_query_buffer['groupBy'])
		{
			return '';
		}

		return 'GROUP BY '.implode(',', $this->_query_buffer['groupBy']);
	}

	private function _prepareOrderBys()
	{
		if (!$this->_query_buffer['orderBy'])
		{
			if (!$this->_order_by)
			{
				return '';
			}

			return	'ORDER BY '.$this->_order_by;
		}

		return 'ORDER BY '.implode(',', $this->_query_buffer['orderBy']);
	}
	private function _prepareSelects()
	{
		if (!$this->_query_buffer['select'])
		{
			return '*';
		}

		return implode(',', $this->_query_buffer['select']);
	}

	private function _prepareWheres()
	{
		$wheres = '1=1';

		foreach ($this->_query_buffer['where'] as $value)
		{
			$wheres .= ' '.$value;
		}

		return 'WHERE '.$wheres;
	}

	private function _prepareHavings()
	{
		$havings = '1=1';

		foreach ($this->_query_buffer['having'] as $value)
		{
			$havings .= ' '.$value;
		}

		return 'HAVING '.$havings;
	}

	private function _prepareValues(array $data)
	{
		$d = '';
		$values = '';

		foreach ($data as $value)
		{
			if (is_null($value))
			{
				$values .= $d.'NULL';
			}
			else
			{
				$values .= $d.'\''.$this->escape($value).'\'';
			}

			$d = ',';
		}

		return $values;
	}

	private function _prepareKeys(array $data)
	{
		$d = '';
		$keys = '';

		foreach ($data as $key => $value)
		{
			$keys .= $d.$key;
			$d = ',';
		}

		return $keys;
	}
}
