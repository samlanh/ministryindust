<?php
class Company_CompanytypeController extends Zend_Controller_Action {
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
			$db = new Company_Model_DbTable_Dbcompanytype();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'adv_search' => '',
						'main_type_search'=>-1,
						'status'=>-1,
						);
			}
			$rs_rows= $db->getAllCompanyType($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","TYPE","STATUS","BY_USER");
			$link_info=array('module'=>'company','controller'=>'companytype','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link_info,'type'=>$link_info),0);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$fm = new Company_Form_FrmCompanyType();
		$frm = $fm->FrmAddCompanyType();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_company_type = $frm;
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  public function addAction(){
  	try{
  		$db = new Company_Model_DbTable_Dbcompanytype();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addCompanyType($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/company/companytype");
  			}else{
  				$this->_redirect("/company/companytype/add");
  			}
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$fm = new Company_Form_FrmCompanyType();
  	$frm = $fm->FrmAddCompanyType();
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_company_type = $frm;
  	
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  	
  }
  public function editAction(){
  	$db = new Company_Model_DbTable_Dbcompanytype();
  	$id = $this->getRequest()->getParam('id');
  	$this->view->id = $id;
  	try{
  		$db = new Company_Model_DbTable_Dbcompanytype();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addCompanyType($_data);
  			$this->_redirect("/company/companytype");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$row = $db->getCompanyTypeById($id);
  	$this->view->row = $row;
  	if (empty($row)){
  		Application_Form_FrmMessage::Sucessfull("NO_RECORD","/company/companytype");
  	}
  	$fm = new Company_Form_FrmCompanyType();
  	$frm = $fm->FrmAddCompanyType($row);
  	Application_Model_Decorator::removeAllDecorator($frm);
  	$this->view->frm_company_type = $frm;
  	
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  	
  }
  
}

