<?php
class Report_ClientController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
  function indexAction(){
			$db = new Report_Model_DbTable_DbClient();
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
						'end_date'=>date('Y-m-d')
						);
			}
			
			$this->view->rs_client= $db->getAllClients($search);
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		$this->view->search = $search;
		$fm = new Group_Form_FrmClient();
		$frm = $fm->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_client = $frm;
		
  }
  public function receiptAction(){
		$db = new Report_Model_DbTable_DbClient();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
						'client_id'=>0,
						'show_all'=>'',
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d')
						);
			}
			
			$this->view->rs_receipts= $db->getAllReciept($search);
			$frm = new Application_Form_FrmAdvanceSearch();
			$this->view->search = $search;
		$frm = $frm->AdvanceSearch(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmReciept();
		$frm = $fm->FrmAddReciept(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
  }
   public function invoiceAction(){
		$db = new Report_Model_DbTable_DbClient();
			if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
						'client_id'=>0,
						'delivery_info'=>0,
						'show_all'=>'',
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d')
						);
			}
		$this->view->rs_invoice= $db->getAllInovice($search);
		$this->view->search = $search;
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Group_Form_FrmInvoice();
		$frm = $fm->FrmAddInvoice(null,1);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;			
   }
   public function receiptDetialsAction(){
	   $id = $this->getRequest()->getParam("id");

		$db = new Report_Model_DbTable_DbClient();	
		$this->view->recieptbyid = $db->getRecieptByID($id);
		$this->view->recieptdetail = $db->getRecieptDetail($id);
		
		//$this->view->search = $search;
   }
  
}

