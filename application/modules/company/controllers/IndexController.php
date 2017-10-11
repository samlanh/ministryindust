<?php
class Company_IndexController extends Zend_Controller_Action {
	public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction(){
		try{
			$db = new Company_Model_DbTable_Dbcompany();
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search= array(
						'txt_search' => '',
						'department'=>-1,
						'province_id'=>-1,
						);
			}
			$rs_rows= $db->getAllCompany($search);
			$this->view->row = $rs_rows;
			
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
	$this->view->rssearch = $search;
	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->rsprovince = $db->getAllProvince();
  	
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->rsdepartment = $db->getAllDepartment();
  }
  public function addAction(){
  	try{
  		$db = new Company_Model_DbTable_Dbcompany();
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$db->addCompany($_data);
  			Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", "/company/index/add");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->rsprovince = $db->getAllProvince();
  	
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$this->view->rsdepartment = $db->getAllDepartment();
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

