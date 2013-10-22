<?php
namespace StdLib;

use Zend\View\Model\JsonModel;

class AjaxModel extends JsonModel
{
	/**
	 * @return AjaxModel
	 */
	static public function create()
	{
		return new static();
	}
	
	public function setSuccess(array $data = array())
	{
		$result['data'] = $data;
		$result['status'] = 'success';
				
		return $this->setVariables($result);
	}
	
	public function setError(array $data = array())
	{
		$result['data'] = $data;
		$result['status'] = 'error';
		return $this->setVariables($data);
	}
}