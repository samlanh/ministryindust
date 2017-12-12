<?php
class Company_ProductController extends Zend_Controller_Action {
	protected $tr;
	public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		try{
			$db = new Company_Model_DbTable_DbProduct();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'adv_search' => '',
						'status'=>-1,
						);
			}
			$rs_rows= $db->getAllProduct($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","STATUS","BY_USER");
			$link_info=array('module'=>'company','controller'=>'product','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link_info,),0);
			
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
  		$db = new Company_Model_DbTable_DbProduct();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addProduct($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/company/product");
  			}else{
  				$this->_redirect("/company/product/add");
  			}
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$fm = new Company_Form_FrmProduct();
  	$frm = $fm->FrmAddCompanyType();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_company_type = $frm;
  	
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  	
  }
  public function editAction(){
  	$db = new Company_Model_DbTable_DbProduct();
  	$id = $this->getRequest()->getParam('id');
  	$this->view->id = $id;
  	try{
  		$db = new Company_Model_DbTable_DbProduct();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addProduct($_data);
  			$this->_redirect("/company/product");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$row = $db->getProductById($id);
  	$this->view->row = $row;
  	if (empty($row)){
  		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/company/product");
  	}
  	$fm = new Company_Form_FrmProduct();
  	$frm = $fm->FrmAddCompanyType($row);
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_company_type = $frm;
  	
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  	
  }

  
}

