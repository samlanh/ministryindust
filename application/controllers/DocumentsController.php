<?php

class DocumentsController extends Zend_Controller_Action
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
    	
    	$this->view->document_type = $db->getAllDocumentType();
    	$bannerlist = $db->getWebsiteSetting("banner");
        $this->view->banner = $bannerlist;//explode(",", $bannerlist['value']);
    	$doc=0;
    	if (!empty($param['dctype'])){
    		$this->view->param = $param['dctype'];
    		$doc = base64_decode(($param['dctype']));
    	}
    	$this->view->doc_type = $db->getDocumentTypeInfoById($doc);
    	$documents = $db->getDocumentByDocType($doc);
    	
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
