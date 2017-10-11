<?php
class Report_ProductController extends Zend_Controller_Action {
	private $activelist = array('មិនប្រើ​ប្រាស់', 'ប្រើ​ប្រាស់');
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
  function indexAction(){
	  $products=new Report_Model_DbTable_DbProduct();
	  if($this->getRequest()->isPost()){
				$search=$this->getRequest()->getPost();
			}
			else{
				$search = array(
						'adv_search' => '',
						'status' => -1,
						'show_all'=>'',
						'category'=>0,
						'start_date'=> date('Y-m-01'),
						'end_date'=>date('Y-m-d')
				);
			}
			
		$this->view->products_list= $products->getALLProducts($search);
		$fm = new Product_Form_FrmProduct();
		$frm = new Application_Form_FrmAdvanceSearch();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
		
		$fm = new Product_Form_FrmProduct();
		$frm = $fm->FrmProduct();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm = $frm;
		
  }
  
}

