<?php

class EyepublicController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
    	$param = $this->getRequest()->getParams();
    	$dbgb = new Application_Model_DbTable_DbGlobal();
    	$dbvg = new Application_Model_DbTable_DbVdGlobal();
    	$db = new Application_Model_DbTable_DbGlobalSelect();
    	
    	$bannerlist = $db->getWebsiteSetting("banner");
        $this->view->banner = $bannerlist;//explode(",", $bannerlist['value']);
    	
    	$documents = $db->getEyepublicDocument();
    	
    	$limits = $db->getWebsiteSetting("items_per_page");
    	$paginator = Zend_Paginator::factory($documents);
    	$paginator->setDefaultItemCountPerPage($limits['value']);
    	$allItems = $paginator->getTotalItemCount();
    	$countPages= $paginator->count();
    	$p = Zend_Controller_Front::getInstance()->getRequest()->getParam('pages');
    	
    	if(isset($p))
    	{
    		$paginator->setCurrentPageNumber($p);
    	} else $paginator->setCurrentPageNumber(1);
    	 
    	$currentPage = $paginator->getCurrentPageNumber();
    	
    	$this->view->document  = $paginator;
    	$this->view->countItems = $allItems;
    	$this->view->countPages = $countPages;
    	$this->view->currentPage = $currentPage;
    	 
    	if($currentPage == $countPages)
    	{
    		$this->view->nextPage = $countPages;
    		$this->view->previousPage = $currentPage-1;
    	}
    	else if($currentPage == 1)
    	{
    		$this->view->nextPage = $currentPage+1;
    		$this->view->previousPage = 1;
    	}
    	else {
    		$this->view->nextPage = $currentPage+1;
    		$this->view->previousPage = $currentPage-1;
    	}
    	
    }  
}
