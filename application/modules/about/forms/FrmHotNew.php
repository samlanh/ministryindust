<?php 
Class About_Form_FrmHotNew extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddHotnew($data=null){
		$db = new Application_Model_DbTable_DbVdGlobal();
		$language = $db->getLaguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("TITLE")
		));
		$_title->setValue($request->getParam("title"));
		
		$_status_search = new Zend_Dojo_Form_Element_FilteringSelect("status_search");
		$_arrsearch=array(
				""=>$this->tr->translate("SELECT_STATUS"),
				"1"=>$this->tr->translate("ACTIVE"),
				"0"=>$this->tr->translate("DACTIVE"),
		);
		$_status_search->setMultiOptions($_arrsearch);
		$_status_search->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				));
		$_status_search->setValue($request->getParam('status_search'));
		
		$id = new Zend_Form_Element_Hidden("id");
		
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch'
		));

		
		$_status = new Zend_Dojo_Form_Element_FilteringSelect("status");
		$_arr=array(
				"1"=>$this->tr->translate("ACTIVE"),
				"0"=>$this->tr->translate("DACTIVE"),
		);
		$_status->setMultiOptions($_arr);
		$_status->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$_status->setValue($request->getParam('status'));
		
		if($data!=null){
			$_title->setValue($data['title']);
			$_status->setValue($data['status']);
			$id->setValue($data['id']);
		}
		
		$this->addElements(array($id,$_btn_search,$_title,$_status,$_status_search
				));
		return $this;
	}	
}