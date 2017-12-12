<?php 
Class Company_Form_FrmCompany extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddCompany($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_company_code = new Zend_Dojo_Form_Element_ValidationTextBox('company_code');
		$_company_code->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("Company Code")
		));
		
		$_company_name = new Zend_Dojo_Form_Element_ValidationTextBox('company_name');
		$_company_name->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("Company Name")
		));
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'));
		
		$_address = new Zend_Dojo_Form_Element_Textarea("address");
		$_address->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside',
				'style'=>'width:100%;min-height:60px; font-size:inherit; font-family:Kh Battambang'
		));
		
		$_date_register = new Zend_Dojo_Form_Element_DateTextBox('date_register');
		$_date_register->setAttribs(array('dojoType'=>'dijit.form.DateTextBox',
				//'required'=>'true',
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		$_date_register->setValue(date("Y-m-d"));
	
		$_note = new Zend_Dojo_Form_Element_Textarea("note");
		$_note->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside ckeditor',
				'style'=>'width:100%; min-height:60px; height:200px; font-size:inherit; font-family:Kh Battambang'
		));
		
		$_province = new Zend_Dojo_Form_Element_FilteringSelect('province_id');
		$_province->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
			//	'onchange'=>'filterDistrict();',
		));
		
		$rows =  $db->getAllProvince();
		$options=array($this->tr->translate("SELECT_PROVINCE"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_province->setMultiOptions($options);
		
		$_main_type=  new Zend_Dojo_Form_Element_FilteringSelect('main_type');
		$_main_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside','onChange'=>'getCompanyType();'));
		$_main_type_opt = array(
				1=>$this->tr->translate("For Handicrafts,SME"),
				2=>$this->tr->translate("For The Water Supply Sector"));
		$_main_type->setMultiOptions($_main_type_opt);
		
		$_product = new Zend_Dojo_Form_Element_FilteringSelect('product_name');
		$_product->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				//'onchange'=>'filterDistrict();',
		));
		
		$rows =  $db->getProduct();
		$options=array($this->tr->translate("Select Product"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_product->setMultiOptions($options);
		
		$_company_type = new Zend_Dojo_Form_Element_FilteringSelect('company_type');
		$_company_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'getCompanyTypeInfor();',
		));
		
		$rows =  $db->getCompanyType();
		$options=array($this->tr->translate("Select Company Type"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_company_type->setMultiOptions($options);
		
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		
		$_company_type_search = new Zend_Dojo_Form_Element_FilteringSelect('company_type_search');
		$_company_type_search->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
				'onchange'=>'getCompanyTypeInfor();',
		));
		
		$rows =  $db->getCompanyType();
		$options=array($this->tr->translate("Select Company Type"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_company_type_search->setMultiOptions($options);
		$_company_type_search->setValue($request->getParam("company_type_search"));
		
		$_product_search = new Zend_Dojo_Form_Element_FilteringSelect('product_search');
		$_product_search->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'class'=>'fullside',
		));
		
		$rows =  $db->getProduct();
		$options=array($this->tr->translate("Select Product"));
		if(!empty($rows))foreach($rows AS $row) $options[$row['id']]=$row['name'];
		$_product_search->setMultiOptions($options);
		$_product_search->setValue($request->getParam("product_search"));
		if($data!=null){
// 			$_main_type->setValue($data['type']);
			$_company_code->setValue($data['com_code']);
			$_company_name->setValue($data['com_name']);
			$_phone->setValue($data['com_phone']);
			$_address->setValue($data['address']);
			$_date_register->setValue(date("Y-m-d",strtotime($data['register_date'])));
			$_note->setValue($data['note']);
			$_province->setValue($data['province_id']);
// 			$_main_type->setValue($data['status']);
			$_product->setValue($data['product_id']);
			$_company_type->setValue($data['company_type']);
			$_status->setValue($data['status']);
			
		}
		$this->addElements(array(
				$_company_code,
				$_company_name,
				$_phone,
				$_address,
				$_date_register,
				$_note,
				$_province,
				$_main_type,
				$_product,
				$_company_type,
				$_status,
				$_company_type_search,
				$_product_search
				));
		return $this;
		
	}	
	
}