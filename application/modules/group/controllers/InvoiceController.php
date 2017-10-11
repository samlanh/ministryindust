<?php
class Group_invoiceController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/invoice';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbInvoice();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
						'client_id'=>0,
						'show_all'=>'',
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'));
			}
			
			$rs_rows= $db->getAllInovice($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("INVOICE_NO","CUSTOMER","AMOUNT","BALANCE","NOTE","DATE","DELIVERY_INFO","STATUS");
			$link=array(
					'module'=>'group','controller'=>'invoice','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('invoice_no'=>$link,'client_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmInvoice();
		$frm = $fm->FrmAddInvoice(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		

		$this->view->result=$search;	
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Group_Model_DbTable_DbInvoice();
				try{
				 if(isset($data['save_new'])){
					$id= $db->addInvoice($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				else if (isset($data['save_close'])){
					$id= $db->addInvoice($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/invoice");
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}		
		
		$db_vdG = new Application_Model_DbTable_DbVdGlobal();
		$rows =  $db_vdG->getAllClient();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
			array_unshift($rows,array('id' => 0,'name' => $tr->translate("SELECT_CUSTOMER"),),array('id' => -1,'name' => $tr->translate("ADD_NEW_CUSTOMER"),
			));
		$this->view->client = $rows;
		$fm = new Group_Form_FrmInvoice();
		$frm = $fm->FrmAddInvoice();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
		$frmpop = new Application_Form_FrmPopupGlobal();
		$this->view->client_form = $frmpop->frmPopupCustomer();
	}
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Group_Model_DbTable_DbInvoice();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				try{
					$data['id']=$id;
					$id= $db->addInvoice($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/invoice");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}		
		
		$row = $db->getInvoiceByID($id);
		$db_vdG = new Application_Model_DbTable_DbVdGlobal();
		$rows =  $db_vdG->getAllClient();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
			array_unshift($rows,array('id' => 0,'name' => $tr->translate("SELECT_CUSTOMER"),),array('id' => -1,'name' => $tr->translate("ADD_NEW_CUSTOMER"),
			));
		$this->view->client = $rows;
		$fm = new Group_Form_FrmInvoice();
		$frm = $fm->FrmAddInvoice($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
		$frmpop = new Application_Form_FrmPopupGlobal();
		$this->view->client_form = $frmpop->frmPopupCustomer();
		
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
	
	
}

