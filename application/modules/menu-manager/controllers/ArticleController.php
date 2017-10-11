<?php
class MenuManager_ArticleController extends Zend_Controller_Action {
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
			$db = new MenuManager_Model_DbTable_DbArticle();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}
			else{
				$search= array(
						'adv_search' => '',
						'status_search'=>'',
						'category'=>0,
						);
			}
			$rs_rows= $db->getAllArticle($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("CATEGORY","TITLE","STATUS");
			$link_info=array('module'=>'menu-manager','controller'=>'article','action'=>'edit',);
			$this->view->list=$list->getCheckList(4, $collumns, $rs_rows,array('title'=>$link_info,'category'=>$link_info),0);
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new MenuManager_Form_FrmCategory();
		$frm_manager=$frm->FrmArticle();
		Application_Model_Decorator::removeAllDecorator($frm_manager);
		$this->view->frm = $frm_manager;
		
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  public function addAction(){
  	try{
  		$db = new MenuManager_Model_DbTable_DbArticle();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addArticle($_data);
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/menu-manager/article");
//   				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/menu-manager/article");
  			}else{
  				Application_Form_FrmMessage::message("INSERT_SUCCESS");
  			}
  		}
  		$frm = new MenuManager_Form_FrmCategory();
  		$frm_manager=$frm->FrmArticle();
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
  		if (!empty($id)){
	  		$db = new MenuManager_Model_DbTable_DbArticle();
	  		if($this->getRequest()->isPost()){
	  			$_data = $this->getRequest()->getPost();
	  			$_data['id']=$id;
	  			$db->addArticle($_data);
	  			$this->_redirect("/menu-manager/article");
// 	  			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/menu-manager/article");
	  		}
	  		$row = $db->getArticleById($id);
	  		$this->view->row = $row;
	  		$this->view->id = $id;
	  		$frm = new MenuManager_Form_FrmCategory();
	  		$frm_manager=$frm->FrmArticle($row);
	  		Application_Model_Decorator::removeAllDecorator($frm_manager);
	  		$this->view->frm = $frm_manager;
  		}else{
  			$this->_redirect("/menu-manager/article/");
  		}
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
  		$db = new MenuManager_Model_DbTable_DbArticle();
  		if(!empty($id)){
  			$db->deleteArticle($id);
  			Application_Form_FrmMessage::Sucessfull("DELETE_SUCCESS","/menu-manager/article");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  }
  function checkTitleAliasAction(){
  	if($this->getRequest()->isPost()){
  		$data = $this->getRequest()->getPost();
  		$db = new MenuManager_Model_DbTable_DbArticle();
  		$data=$db->CheckTitleAlias($data['title_alias']);
  		print_r(Zend_Json::encode($data));
  		exit();
  	}
  }
}

