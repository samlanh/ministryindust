<?php
class Other_partnerController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
// 	public function indexAction(){
// 		try{
// 			$db = new Other_Model_DbTable_DbSlide();
// 			if($this->getRequest()->isPost()){
// 				$data=$this->getRequest()->getPost();
// 				$data['slide_partner']="slide_partner";
// 				$db->updateSlide($data,"slide_partner");
// 			}
// 			$this->view->slide = $db->getWebsiteSetting("slide_partner");
			
// 		}catch (Exception $e){
// 			Application_Form_FrmMessage::message("Application Error");
// 			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
// 		}
// 	}
	
	public function indexAction(){
		try{
			$db = new Other_Model_DbTable_DbPartner();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'adv_search' => '',
						'status_search'=>'',
						'cate_type'=>'',
						);
			}
			$rs_rows= $db->getAllPartner($search);
			$this->view->row = $rs_rows;
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  public function addAction(){
  	try{
  		$db = new About_Model_DbTable_Dbabout();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addAboutministry($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/about/index");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new About_Form_Frmabout();
  		$frm_manager=$frm->Frmdepartment();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }
}

