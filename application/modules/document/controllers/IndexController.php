<?php
class Document_IndexController extends Zend_Controller_Action {
	public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db = new Document_Model_DbTable_Dbdocument();
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'txt_search' => '',
						'document_type'=>-1,
						);
			}
			$rs_rows= $db->getAllDocument($search);
			$this->view->row = $rs_rows;
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("ឈ្មោះឯកសារ","ប្រភេទឯកសារ","កាលបរិច្ឆទ","ស្ថានការ");
			$link_info=array('module'=>'document','controller'=>'index','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('title'=>$link_info,'doc_type_title'=>$link_info),0);
				
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
	$this->view->rssearch = $search;
	$this->view->document_type = $db->getAllDocumentType();
// 	$db = new Application_Model_DbTable_DbGlobal();
//   	$this->view->rsprovince = $db->getAllProvince();
  	
//   	$db = new Application_Model_DbTable_DbVdGlobal();
//   	$this->view->rsdepartment = $db->getAllDepartment();
  }
  public function addAction(){
  	$db = new Document_Model_DbTable_Dbdocument();
  	try{
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addDocument($_data);
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/document/index/add");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$this->view->document_type = $db->getAllDocumentType();
  }
  public function editAction(){
  	$db = new Company_Model_DbTable_Dbcompany();
  	try{
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addCompany($_data);
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/company/index/add");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$id = $this->getRequest()->getParam('id');
  	$result =  $db->getCompanyById($id);
  	if(empty($result)){Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/company/index");}
  	$this->view->rs = $result;
  	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->rsprovince = $db->getAllProvince();
  	 
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->rsdepartment = $db->getAllDepartment();
  	
  }
}

