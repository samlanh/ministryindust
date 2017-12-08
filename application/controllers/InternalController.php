<?php

class InternalController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
    	$db = new Application_Model_DbTable_DbGlobalSelect();
    	$param = $this->getRequest()->getParams();
        $partner_id =  base64_decode($param['partner']);        
        $this->view->partner_info = $db->getPartnerInfoById($partner_id);

        $bannerlist = $db->getWebsiteSetting("banner");
        $this->view->banner = $bannerlist;//explode(",", $bannerlist['value']);
        $this->view->param = $param;
        
       // echo $partner_id;exit();
          // $this->view->company_info = $db->getCompanyDetailById($com_id);
    }  
}
