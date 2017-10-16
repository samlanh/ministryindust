<?php
class About_HotnewsController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
		try{
			$db = new About_Model_DbTable_Dbabout();
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
			$rs_rows= $db->getAllAbout(0,"","",$search);
			$this->view->row = $rs_rows;
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new About_Form_Frmabout();
  		$frm_manager=$frm->Frmdepartment();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
		
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
  public function editAction(){
  	$id = $this->getRequest()->getParam('id');
  	try{
  		$db = new About_Model_DbTable_Dbabout();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id']=$id;
  			$db->addDepartment($_data);
			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/department/index");
  		}
  		$row = $db->getDepartmentById($id);
  		$this->view->row = $row;
  		$this->view->id = $id;
  		$frm = new About_Form_Frmabout();
  		$frm_manager=$frm->Frmdepartment($row);
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }
  public function deleteAction(){
  	try{
  		$id = $this->getRequest()->getParam('id');
  		$db = new MenuManager_Model_DbTable_DbCategory();
  		if(!empty($id)){
  			$db->deleteCategory($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/category");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  function checkTitleAliasAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new MenuManager_Model_DbTable_DbCategory();
  		$data=$db->CheckTitleAlias($data['title_alias']);
  		print_r(Zend_Json::encode($data));
  		exit();
  	}
  }
  function loadimgAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new Application_Model_DbTable_DbVdGlobal();
  		$data=$db->loadImageFromDir($data);
  		print_r(Zend_Json::encode($data));
  		exit();
  	}
  }
}

