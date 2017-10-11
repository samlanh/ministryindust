<?php 
Class Group_Form_FrmReciept extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddReciept($data=null,$action=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		$db_vdG = new Application_Model_DbTable_DbVdGlobal();
		$_invoice_no = new Zend_Dojo_Form_Element_ValidationTextBox('reciept_no');
		$_invoice_no->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'readonly'=>true,
				'style'=>' color: #ca1212; font-weight: 600;',
				'placeholder'=>$this->tr->translate("RECIEPT_NO")
		));
		$_invoice_no->setValue($db_vdG->getRecieptNo());
		$_client_id = new Zend_Dojo_Form_Element_FilteringSelect('client_id');
		$_client_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'showFormClient(),getAllInvoice();',
		));
		
		$rows =  $db_vdG->getAllClient();
		if (!empty($action)){
			$options=array(0=>$this->tr->translate("SELECT_CUSTOMER"));
		}else{
			$options=array(0=>$this->tr->translate("SELECT_CUSTOMER"));
// 			$options=array(0=>$this->tr->translate("SELECT_CUSTOMER"),"-1"=>$this->tr->translate("ADD_NEW_CUSTOMER"));
		}
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_client_id->setMultiOptions($options);
		
		$_reciept_date = new Zend_Dojo_Form_Element_DateTextBox('reciept_date');
		$_reciept_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				//'required'=>'true',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		
		$_date = date("Y-m-d");
		$_reciept_date->setValue($_date);
		
		$_note = new Zend_Dojo_Form_Element_Textarea("note");
		$_note->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'width:100%;min-height:60px; font-size:inherit; font-family:Kh Battambang'
		));
		
		
		$_province = new Zend_Dojo_Form_Element_FilteringSelect('province');
		$_province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'filterDistrict();',
		));
		
		$rows =  $db->getAllProvince();
		$options=array($this->tr->translate("SELECT_PROVINCE"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['province_en_name'];
		$_province->setMultiOptions($options);
		
		$_amount = new Zend_Dojo_Form_Element_NumberTextBox('paid_amount');
		$_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readonly'=>true,
				'required'=>true,
		));
		$_balance = new Zend_Dojo_Form_Element_NumberTextBox('balance');
		$_balance->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'readonly'=>true,
				'required'=>true,
		));
		$_total_paid = new Zend_Dojo_Form_Element_NumberTextBox('total_paid');
		$_total_paid->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'onKeyup'=>'calculateBalance();',
				'class'=>'fullside',
				'required'=>true,
		));
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		if($data!=null){
			$_invoice_no->setValue($data['reciept_no']);
			$_client_id->setValue($data['client_id']);
			$_reciept_date->setValue($data['paid_date']);
			$_note->setValue($data['note']);
			$_status->setValue($data['status']);
			$_amount->setValue($data['amount']);
			$_balance->setValue($data['balance']);
			$_total_paid->setValue($data['total_paid']);
		}
		$this->addElements(array($_invoice_no,$_client_id,$_reciept_date,
				$_amount,$_balance,$_total_paid,
			$_note,$_province,
			$_status));
		return $this;
		
	}	
	
}