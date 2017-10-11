<?php 
Class Group_Form_FrmInvoice extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddInvoice($data=null,$action=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		$db_vdG = new Application_Model_DbTable_DbVdGlobal();
		$_invoice_no = new Zend_Dojo_Form_Element_ValidationTextBox('invoice_no');
		$_invoice_no->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("INVOICE_NO")
		));
		$_client_id = new Zend_Dojo_Form_Element_FilteringSelect('client_id');
		$_client_id->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'showFormClient();',
		));
		
		$rows =  $db_vdG->getAllClient();
		if (!empty($action)){
			$options=array(0=>$this->tr->translate("SELECT_CUSTOMER"));
		}else{
			$options=array(0=>$this->tr->translate("SELECT_CUSTOMER"),"-1"=>$this->tr->translate("ADD_NEW_CUSTOMER"));
		}
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_client_id->setMultiOptions($options);
		
		$_order_date = new Zend_Dojo_Form_Element_DateTextBox('order_date');
		$_order_date->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				//'required'=>'true',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		
		$_date = date("Y-m-d");
		$_order_date->setValue($_date);
		
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
		
		$_delivery_info=  new Zend_Dojo_Form_Element_FilteringSelect('delivery_info');
		$_delivery_info->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		if (!empty($action)){
			$_delivery_info_opt = array(
					0=>$this->tr->translate("DELIVERY_INFO"),
					1=>$this->tr->translate("SHIPPING"),
					2=>$this->tr->translate("RECEICED"));
		}else{
		$_delivery_info_opt = array(
				1=>$this->tr->translate("SHIPPING"),
				2=>$this->tr->translate("RECEICED"));
		
		}
		$_delivery_info->setMultiOptions($_delivery_info_opt);
		if (empty($action)){
		$_delivery_info->setValue(2);
		}
		$_amount = new Zend_Dojo_Form_Element_NumberTextBox('amount');
		$_amount->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'onKeyup'=>'setBalance();',
				'required'=>true,
		));
		$_balance = new Zend_Dojo_Form_Element_NumberTextBox('balance');
		$_balance->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
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
			$_invoice_no->setValue($data['invoice_no']);
			$_client_id->setValue($data['client_id']);
			$_order_date->setValue($data['order_date']);
			$_delivery_info->setValue($data['delivery_status']);
			$_note->setValue($data['note']);
			$_status->setValue($data['status']);
			$_amount->setValue($data['amount']);
			$_balance->setValue($data['balance']);
		}
		$this->addElements(array($_invoice_no,$_client_id,$_order_date,
				$_delivery_info,$_amount,$_balance,
			$_note,$_province,
			$_status));
		return $this;
		
	}	
	
}