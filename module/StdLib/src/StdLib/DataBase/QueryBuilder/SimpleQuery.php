<?php
namespace StdLib\DataBase\QueryBuilder;

use StdLib\DataBase\QueryBuilder\Base;

abstract class SimpleQuery extends Base
{
	public function fetchAllQuery($sql)
	{
		$data = array();

		$res = $this->query($sql);

		while ($row = $res->fetch_assoc())
		{
			$data[]=$row;
		};

		return $data;
	}

	public function fetchOneQuery($sql)
	{
		$res = $this->query($sql);
		return $res->fetch_assoc();
	}
}