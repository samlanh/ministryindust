<?php
class MenuManager_MenuItemsController extends Zend_Controller_Action {
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
			$db = new MenuManager_Model_DbTable_DbMenuItems();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}
			else{
				$search= array(
						'advance_search'=>'',
						'menu_manager'=>0,
						'status_search'=>'',
						);
			}
			$rs_rows= $db->getMenuItems(0,'','',$search);
			$this->view->row = $rs_rows;
// 			$glClass = new Application_Model_GlobalClass();
// 			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
// 			$list = new Application_Form_Frmtable();
// 			$collumns = array("TITLE","DESCRIPTION","ALL","ACTIVE","DACTIVE","STATUS");
// 			$link_info=array('module'=>'menu-manager','controller'=>'index','action'=>'edit',);
// 			$this->view->list=$list->getCheckList(4, $collumns, $rs_rows,array('title'=>$link_info,),0);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new MenuManager_Form_FrmMenu();
		$frm_manager=$frm->FrmMenuItems();
		Application_Model_Decorator::removeAllDecorator($frm_manager);
		$this->view->frm = $frm_manager;
  }
  public function addAction(){
  	try{
  		$db = new MenuManager_Model_DbTable_DbMenuItems();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addMenuItems($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/menu-manager/menu-items");
//   				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/menu-manager/menu-items");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuItems();
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
  		$db = new MenuManager_Model_DbTable_DbMenuItems();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id']=$id;
  			$db->addMenuItems($_data);
//   			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/menu-manager/menu-items");
  			$this->_redirect("/menu-manager/menu-items");
  		}
  		$row = $db->getMenuItemsById($id);
  		$this->view->row = $row;
  		$frm = new MenuManager_Form_FrmMenu();
  		$frm_manager=$frm->FrmMenuItems($row);
  		Application_Model_Decorator::removeAllDecorator($frm_manager);
  		$this->view->frm = $frm_manager;
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$this->view->id = $id;
  	$dbglobal = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->lang = $dbglobal->getLaguage();
  }
  public function deleteAction(){
  	try{
  		$id = $this->getRequest()->getParam('id');
  		$db = new MenuManager_Model_DbTable_DbMenuItems();
  		if(!empty($id)){
  			$db->deleteMenu($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/menu-items");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  function checkTitleAliasAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new MenuManager_Model_DbTable_DbMenuItems();
  		$data=$db->CheckTitleAlias($data['title_alias']);
  		print_r(Zend_Json::encode($data));
  		exit();
  	}
  }
  function getcontrolAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new Application_Model_DbTable_DbVdGlobal();
  		$id = $db->getControlByTypeMenu($data['menutype']);
  		print_r(Zend_Json::encode($id));
  		exit();
  	}
  }
  function getmenuitemsajaxAction(){
	  if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new MenuManager_Model_DbTable_DbMenuItems();
  		$id = $db->getMenuItemsajax(0,null,null,$data);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($id, array ( 'id' => 0, 'name' => $tr->translate("MENU_ITEMS_ROOT")) );
  		print_r(Zend_Json::encode($id));
  		exit();
  	}
  }
  
  
}

