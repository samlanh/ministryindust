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
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	
    	$this->view->document_type = $db->getAllDocumentType();
    	$bannerlist = $db->getWebsiteSetting("banner");
    	$this->view->banner = explode(",", $bannerlist['value']);
    	$doc=1;
    	if (!empty($param['dctype'])){
    		$this->view->param = $param['dctype'];
    		$doc = base64_decode(($param['dctype']));
    	}
    	$this->view->document = $db->getDocumentByDocType($doc);
    	
    }  
}
