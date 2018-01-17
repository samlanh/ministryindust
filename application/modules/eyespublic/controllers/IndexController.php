<?php
class Eyespublic_IndexController extends Zend_Controller_Action {
	protected $tr;
	public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function indexAction(){
		$db = new Eyespublic_Model_DbTable_Dbeyespublic();
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'txt_search' => '',
						);
			}
			$rs_rows= $db->getAllDocument($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("ឈ្មោះឯកសារ","SIZE","DESCRIPTION","KEY_WORD","CREATE_DATE","STATUS","BY_USER");
			$link_info=array('module'=>'eyespublic','controller'=>'index','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link_info,'doc_type_title'=>$link_info),0);
				
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
	$this->view->rssearch = $search;  	
  }
  public function addAction(){
  	$db = new Eyespublic_Model_DbTable_Dbeyespublic();
  	try{
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addDocument($_data);
  			if (!empty($_data['save_new'])){
  			}else{
  				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/eyespublic/index");
  			}
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
//   	$row_duc= $db->getAllDocumentType();
//   	array_unshift($row_duc, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
//   	array_unshift($row_duc, array ( 'id' =>'','name' => $this->tr->translate("SELECT_DOCUMENT")));
//   	$this->view->document_type =$row_duc;
  	
//   	$search = array();
//   	$db = new Eyespublic_Model_DbTable_Dbeyespublictype();
//   	$this->view->document_types = $db->getAllDocumentType($search);
  	
//   	$db = new Application_Model_DbTable_DbVdGlobal();
//   	$this->view->rsdepartment = $db->getAllDepartment();
  }  
public function editAction(){
  	$db = new Eyespublic_Model_DbTable_Dbeyespublic();
  	$id = $this->getRequest()->getParam('id');
  	try{
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id'] = $id;
  			$db->editDocument($_data);
  			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS", "/eyespublic/index");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$result =  $db->getDocumentById($id);
  	if(empty($result)){Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/eyespublic/index");}
  	$this->view->rs = $result;
//   	$row_duc = $db->getAllDocumentType();
  	
//   	array_unshift($row_duc, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
//   	array_unshift($row_duc, array ( 'id' =>'','name' => $this->tr->translate("SELECT_DOCUMENT")));
//   	$this->view->document_type =$row_duc;
  	 
  	$search = array();
//   	$db = new Eyespublic_Model_DbTable_Dbeyespublictype();
//   	$this->view->document_types = $db->getAllDocumentType($search);
  	
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->rsdepartment = $db->getAllDepartment();
  	
  }
  
//   function getConameAction(){
//   	if($this->getRequest()->isPost()){
//   		$_data = $this->getRequest()->getPost();
//   		$db = new Eyespublic_Model_DbTable_Dbeyespublic();
//   		$rs_rows= $db->getAllDocument($search);
//   		$this->view->row = $rs_rows;
//   		array_unshift($rs_rows,array(
//   				'id' => -1,
//   				'name' => '---Add New ---',
//   		) );
//   		print_r(Zend_Json::encode($rs_rows));
//   		exit();
//   	}
//   }
  
//   function getstaffcodeAction(){
//   	if($this->getRequest()->isPost()){
//   		$db = new Application_Model_DbTable_DbGlobal();
//   		$_data = $this->getRequest()->getPost();
//   		$id = $db->getStaffNumberByBranch($_data['branch_id']);
//   		print_r(Zend_Json::encode($id));
//   		exit();
//   	}
//   }
  
//   function addDocumentAction(){
//   	if($this->getRequest()->isPost()){
//   		$_data = $this->getRequest()->getPost();
//   		$db = new Eyespublic_Model_DbTable_Dbeyespublictype();
//   		$rs_rows= $db->addDocumenttypeAjax($_data);
//   		print_r(Zend_Json::encode($rs_rows));
//   		exit();
//   	}
//   }
  
}

