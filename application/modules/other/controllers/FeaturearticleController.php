<?php
class Other_featurearticleController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbFeaturearticle();
			if($this->getRequest()->isPost()){
				$data=$this->getRequest()->getPost();
				$data['feature_article']="feature_article";
				$db->updateFeaturearticle($data,"feature_article");
			}
			$this->view->featurearticle = $db->getWebsiteSetting("feature_article");
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$db = new Application_Model_DbTable_DbVdGlobal();
		$this->view->article = $db->getAllArticle();
	}
	
	public function iconAction(){
		
	}
}

