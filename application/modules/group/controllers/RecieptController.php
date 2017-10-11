<?php
class Group_recieptController extends Zend_Controller_Action {
	const REDIRECT_URL = '/group/reciept';
	public function init()
	{
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			$db = new Group_Model_DbTable_DbReciept();
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
			
			$rs_rows= $db->getAllReciept($search);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("RECIEPT_NO","CUSTOMER","AMOUNT","PAID","BALANCE","PAID_DATE","STATUS");
			$link=array(
					'module'=>'group','controller'=>'reciept','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('reciept_no'=>$link,'user_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmReciept();
		$frm = $fm->FrmAddReciept(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		

		$this->view->result=$search;	
	}
	public function addAction(){
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$db = new Group_Model_DbTable_DbReciept();
				try{
				 if(isset($data['save_new'])){
					$id= $db->addReciept($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
				}
				else if (isset($data['save_close'])){
					$id= $db->addReciept($data);
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/reciept");
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
		$fm = new Group_Form_FrmReciept();
		$frm = $fm->FrmAddReciept();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
		$frmpop = new Application_Form_FrmPopupGlobal();
		$this->view->client_form = $frmpop->frmPopupCustomer();
	}
	public function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Group_Model_DbTable_DbReciept();
		if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				try{
					$data['id']=$id;
					$id= $db->updateReciept($data);
					
					Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
					Application_Form_FrmMessage::redirectUrl("/group/reciept");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}else{		
		$row = $db->getRecieptByID($id);
		$this->view->rs = $row;
		$this->view->recieptdetail = $db->getRecieptDetail($id);
		$db_vdG = new Application_Model_DbTable_DbVdGlobal();
		$rows =  $db_vdG->getAllClient();
		$tr= Application_Form_FrmLanguages::getCurrentlanguage();
			array_unshift($rows,array('id' => 0,'name' => $tr->translate("SELECT_CUSTOMER"),),array('id' => -1,'name' => $tr->translate("ADD_NEW_CUSTOMER"),
			));
		$this->view->client = $rows;
		$this->view->reciept_id =$id;
		$fm = new Group_Form_FrmReciept();
		$frm = $fm->FrmAddReciept($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
		$frmpop = new Application_Form_FrmPopupGlobal();
		$this->view->client_form = $frmpop->frmPopupCustomer();
		}
		
	}
	function getinvoiceAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Group_Model_DbTable_DbReciept();
			$row=$db->getInvoiceBYClient($data['client_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	function getinvoiceforeditAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db=new Group_Model_DbTable_DbReciept();
			$row=$db->getInvoiceBYClientForedit($data['client_id'],$data['receipt_id']);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}

	
	
}

