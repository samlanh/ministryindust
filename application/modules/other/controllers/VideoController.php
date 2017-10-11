<?php
class Other_VideoController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbVideo();
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$data['youtubeframe']="youtubeframe";
				$db->updateVideo($data,"youtubeframe");
			}
			$this->view->youtubeframe = $db->getWebsiteSetting("youtubeframe");
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		
	}

}

