<?php
namespace Application\Controllers;

use StdLib\Application;
use Zend\Mvc\Controller\AbstractActionController;
use StdLib\GenerateQuery;
use Zend\View\Model\ViewModel;
use StdLib\AjaxModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
    	$section = $this->getEvent()->getRouteMatch()->getParam('section', 'music');
    	
    	$this->jsBootstrap('application');
    	
    	$genq = new GenerateQuery();
    
    	$items = $genq->getBunch(0, 100);

    	return new ViewModel(array(
    		'items' => json_encode($items),
    		'items_count' => count($items)
    	));
    }
    
    public function loadItemsAction()
    {
    	$count = $this->getRequest()->getPost('count');
   
    	$genq = new GenerateQuery();
    
    	$items = $genq->getBunch($count, 100);
    	
    	return AjaxModel::create()
    		->setSuccess($items);
    }
}
