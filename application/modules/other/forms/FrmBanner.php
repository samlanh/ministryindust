<?php 
Class Other_Form_FrmBanner extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddBanner($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("TITLE")
		));

		$_link = new Zend_Dojo_Form_Element_TextBox('link');
		$_link->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("LINK")
		));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);

		$_option_link=  new Zend_Dojo_Form_Element_FilteringSelect('option_link');
		$_option_link->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside','onchange'=>'change_link();'));
		$_option_link_opt = array(
				1=>$this->tr->translate("Internal Link"),
				2=>$this->tr->translate("External Link"));
		$_option_link->setMultiOptions($_option_link_opt);

		$_description = new Zend_Dojo_Form_Element_Textarea("description");
		$_description->setAttribs(array(
				'dojoType'=>'dijit.form.Textarea',
				'class'=>'fullside ckeditor',
				'style'=>'width:100%;min-height:60px; font-size:inherit; font-family:Kh Battambang'
		));

		if($data!=null){

			$_title->setValue($data['title']);	
			$_link->setValue($data['link']);
			$_description->setValue($data['description']);
			$_option_link->setValue($data['option_link']);
			$_status->setValue($data['status']);			
		}
		$this->addElements(array($_title,$_link,$_description,$_status,$_option_link));
		return $this;
		
	}	
	
}