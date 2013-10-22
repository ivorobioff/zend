<?php
namespace StdLib;

use StdLib\DataBase\SimpleDb;

class GenerateQuery
{
	public function getBunch($offset = 0, $limit = 20)
	{		
		$sql = 'CALL GenerateQuery(\'\', \'ItemsLimit="'.$limit.'"; ItemsOffset="'.$offset.'"\', \'\', \'\', \'Items\')';
		
		return SimpleDb::create()->fetchAllQuery($sql);
	}
}