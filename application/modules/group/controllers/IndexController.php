<?php
class Group_indexController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/index';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbClient();
			if($this->getRequest()->isPost()){
				$formdata=$this->getRequest()->getPost();
				$search = array(
						'adv_search' => $formdata['adv_search'],
						'show_all'=>empty($formdata['show_all'])?"":$formdata['show_all'],
						'status'=>$formdata['status'],
						'start_date'=> $formdata['start_date'],
						'end_date'=>$formdata['end_date'],
						);
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
						'show_all'=>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'));
			}
			
			$rs_rows= $db->getAllClients($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("CUSTOMER_CODE","USER_NAME","EMAIL","PHONE","ADDRESS","STATUS");
			$link=array(
					'module'=>'group','controller'=>'index','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(3, $collumns, $rs_rows,array('user_name'=>$link,'email'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		

		$this->view->result=$search;	
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$data['old_photo']=null;
				$db = new Group_Model_DbTable_DbClient();
				try{
				 if(isset($data['save_new'])){
					$id= $db->addClient($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				else if (isset($data['save_close'])){
					$id= $db->addClient($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/index");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}		
		$fm = new Group_Form_FrmClient();
		
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
	}
	public function editAction(){
		$db = new Group_Model_DbTable_DbClient();
		$id = $this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$data['id'] = $id;
				$db->addClient($data);
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS',"/group/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$row = $db->getClientById($id);
	    $this->view->row=$row;
		$this->view->photo = $row['photo'];
		if(empty($row)){
			$this->_redirect("/group/client");
		}
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
	}
	function deleteAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Group_Model_DbTable_DbClient();
		if (!empty($id)){
			try{
				$db->deleteClient($id);
				Application_Form_FrmMessage::Sucessfull('DELETE_SUCCESS',"/group/index");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("DELETE_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}else{
			$this->_redirect("/group/index");
		}
	}
	function viewAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Group_Model_DbTable_DbClient();
		$this->view->client_list = $db->getClientDetailInfo($id);
	}
	function addClientAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db_com = new Group_Model_DbTable_DbClient();
			$id = $db_com->addClientAjax($data);
			print_r(Zend_Json::encode($id));
			exit();
		}
	}
	
}

