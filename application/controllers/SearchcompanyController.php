<?php

class SearchcompanyController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
    	$db = new Application_Model_DbTable_DbGlobal();
    	$this->view->rsprovince = $db->getAllProvince();
    		
    	$db = new Application_Model_DbTable_DbVdGlobal();
    	$this->view->rsdepartment = $db->getAllDepartment();
    	
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$bannerlist = $db->getWebsiteSetting("banner");
    	$this->view->banner = explode(",", $bannerlist['value']);
    	
    }  
}
