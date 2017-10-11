<?php

class AboutministryController extends Zend_Controller_Action
{
    public function init()
    {
    	header('content-type: text/html; charset=utf8');  
    }
    public function indexAction()
    {
		$param = $this->getRequest()->getParam('alias');
		$this->view->param = $param;

		$db = new Department_Model_DbTable_Dbdepartment();
    	$this->view->rsfirst = $db->getAboutFirstRecord($param);
    	
    	$db = new Application_Model_DbTable_DbVdGlobal();
    	$this->view->rsdepartment = $db->getAllParentAbout();
    }  
}
