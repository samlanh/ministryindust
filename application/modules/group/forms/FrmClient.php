<?php 
Class Group_Form_FrmClient extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddClient($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_user_name = new Zend_Dojo_Form_Element_ValidationTextBox('user_name');
		$_user_name->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("CUSTOMER_NAME")
		));
		$_email = new Zend_Dojo_Form_Element_ValidationTextBox('email');
		$_email->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'onChange'=>'checkMail();',
				'placeholder'=>$this->tr->translate("Email")
		));
		
		$_password = new Zend_Dojo_Form_Element_PasswordTextBox('password');
		$_password->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("Password")
		));
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'));
		
		$_company_name = new Zend_Dojo_Form_Element_TextBox('company_name');
		$_company_name->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside'));
		
		$_website = new Zend_Dojo_Form_Element_TextBox('website');
		$_website->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_address = new Zend_Dojo_Form_Element_Textarea("address");
		$_address->setAttribs(array(
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
		
		$_package=  new Zend_Dojo_Form_Element_FilteringSelect('package');
		$_package->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_package_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_package->setMultiOptions($_package_opt);
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		if($data!=null){
			$_user_name->setValue($data['user_name']);
			$_email->setValue($data['email']);
			$_phone->setValue($data['phone']);
			$_website->setValue($data['website']);
			$_address->setValue($data['address']);
			$_status->setValue($data['status']);
			$_company_name->setValue($data['company_name']);
		}
		$this->addElements(array($_user_name,$_email,$_password,$_phone,$_website,$_address,$_province,
				$_company_name,
				$_package,$_status));
		return $this;
		
	}	
	
}