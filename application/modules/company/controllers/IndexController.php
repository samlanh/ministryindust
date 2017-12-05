<?php
class Company_IndexController extends Zend_Controller_Action {
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
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("លេខកូដ","ឈ្មោះក្រុមហ៊ុន","លេខទូរសព្ទ","ថ្ងៃចុះបញ្ជី","ស្ថាប័នក្រោមចំនុះ","រាជធានី/ខេត្ត","ផលិតផល","ស្ថានការ","BY_USER");
			$link_info=array('module'=>'company','controller'=>'index','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('com_code'=>$link_info,'com_name'=>$link_info),0);
			
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
  			if(!empty($_data['save_close'])){
  				$this->_redirect("/company/index");
  			}else{
  				$this->_redirect("/company/index/add");
  			}
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->rsprovince = $db->getAllProvince();
  	
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$row = $db->getAllDepartment();
  	 
  	array_unshift($row, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
  	array_unshift($row, array ( 'id' =>'','name' => $this->tr->translate("SELECT_DEPARTMENT")));
  	$this->view->rsdepartment=$row;
  	
  }
  public function editAction(){
  	$db = new Company_Model_DbTable_Dbcompany();
  	$id = $this->getRequest()->getParam('id');
  	try{
  		if($this->getRequest()->isPost()){
  			$_data = $this->getRequest()->getPost();
  			$_data['id']=$id;
  			$db->editCompany($_data);
  			$this->_redirect("/company/index");
//   			Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS", "/company");
  		}
  	}catch (Exception $e){
  		Application_Form_FrmMessage::message("Application Error");
  		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
  	}
  	$result =  $db->getCompanyById($id);
  	if(empty($result)){Application_Form_FrmMessage::Sucessfull("NO_RECORD", "/company/index");}
  	$this->view->rs = $result;
  	$db = new Application_Model_DbTable_DbGlobal();
  	$this->view->rsprovince = $db->getAllProvince();
  	 
  	$db = new Application_Model_DbTable_DbVdGlobal();
  	$row= $db->getAllDepartment();
  	array_unshift($row, array ( 'id' => -1,'name' => $this->tr->translate("ADD_NEW")));
  	array_unshift($row, array ( 'id' =>'','name' => $this->tr->translate("SELECT_DEPARTMENT")));
  	$this->view->rsdepartment =$row;
  	
  }
  
  function  getDepartmentnameAction(){
  	if($this->getRequest()->isPost()){
  		$_data = $this->getRequest()->getPost();
  		$db = new Application_Model_DbTable_DbVdGlobal();
  		$row = $db->getAllDepartment();
  		array_unshift($row,array(
  				'id' => -1,
  				'name' => '---Add New ---',
  		) );
  		print_r(Zend_Json::encode($row));
  		exit();
  	}
  }
  
}

