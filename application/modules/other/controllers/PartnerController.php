<?php
class Other_partnerController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
  const REDIRECT_URL='/other';
    public function init()
    {    	
     /* Initialize action controller here */
     $this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
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
						'status'=>'',
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
  		$db = new Other_Model_DbTable_DbPartner();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addPartner($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/other/partner");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new Other_Form_FrmPartner();
  		$frm_manager=$frm->FrmAddPartner();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }


  public function editAction(){
    $db = new Other_Model_DbTable_DbPartner();
    if($this->getRequest()->isPost()){
      $_data = $this->getRequest()->getPost();
      try{
        $db->addPartner($_data);
        Application_Form_FrmMessage::Sucessfull($this->tr->translate('EDIT_SUCCESS'),self::REDIRECT_URL . '/partner');
      }catch(Exception $e){
        Application_Form_FrmMessage::message($this->tr->translate('EDIT_FAIL'));
        $err =$e->getMessage();
        Application_Model_DbTable_DbUserLog::writeMessageError($err);
      }
    }

    $id = $this->getRequest()->getParam("id");
    $row = $db->getPartnerById($id);
    $this->view->row=$row;
    if(empty($row)){
      $this->_redirect('other/partner');
    }   
    $fm = new Other_Form_FrmPartner();
    $frm = $fm->FrmAddPartner($row);
    Application_Model_Decorator::removeAllDecorator($frm);
    $this->view->frm = $frm;  
    $dbglobal = new Application_Model_DbTable_DbVdGlobal();
    $this->view->lang = $dbglobal->getLaguage();
    
  }


}

