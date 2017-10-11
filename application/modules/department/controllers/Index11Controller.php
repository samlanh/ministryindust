<?php
class MenuManager_IndexController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	private $sex=array(1=>'M',2=>'F');
	public function indexAction(){
		$this->_redirect("/menu-manager/menu-items");
		try{
			$db = new MenuManager_Model_DbTable_DbMenuManager();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}
			else{
				$search= array(
						'status_search'=>'',
						);
			}
			$rs_rows= $db->getAllMainMenu($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("TITLE","DESCRIPTION","ALL","ACTIVE","DACTIVE","STATUS");
			$link_info=array('module'=>'menu-manager','controller'=>'index','action'=>'edit',);
			$this->view->list=$list->getCheckList(4, $collumns, $rs_rows,array('title'=>$link_info,),0);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new MenuManager_Form_FrmMenu();
		$frm_manager=$frm->FrmMenuManager();
		Application_Model_Decorator::removeAllDecorator($frm_manager);
		$this->view->frm = $frm_manager;
  }
  public function addAction(){
  	try{
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addMainMenu($_data);
  			if(!empty($_data['save_close'])){
  				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/menu-manager/index");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuManager();
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  
  }
  public function editAction(){
  	$id = $this->getRequest()->getParam('id');
  	try{
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id']=$id;
  			$db->addMainMenu($_data);
  			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/menu-manager/index");
  		}
  		$row = $db->getMainMenuById($id);
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuManager($row);
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  public function deleteAction(){
  	try{
  		$id = $this->getRequest()->getParam('id');
  		$db = new MenuManager_Model_DbTable_DbMenuManager();
  		if(!empty($id)){
  			$db->deleteMenu($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/index");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  
}

