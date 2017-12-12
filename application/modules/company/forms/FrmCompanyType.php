<?php 
Class Company_Form_FrmCompanyType extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddCompanyType($data=null){
		
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Application_Model_DbTable_DbGlobal();
		
		$_main_type=  new Zend_Dojo_Form_Element_FilteringSelect('main_type');
		$_main_type->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_main_type_opt = array(
				1=>$this->tr->translate("For Handicrafts,SME"),
				2=>$this->tr->translate("For The Water Supply Sector"));
		$_main_type->setMultiOptions($_main_type_opt);
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		
		$_main_type_search=  new Zend_Dojo_Form_Element_FilteringSelect('main_type_search');
		$_main_type_search->setAttribs(array('dojoType'=>'dijit.form.FilteringSelect','class'=>'fullside',));
		$_main_type_search_opt = array(
				-1=>$this->tr->translate("Choose Type"),
				1=>$this->tr->translate("For Handicrafts,SME"),
				2=>$this->tr->translate("For The Water Supply Sector"));
		$_main_type_search->setMultiOptions($_main_type_search_opt);
		$_main_type_search->setValue($request->getParam("main_type_search"));
		
		if($data!=null){
			$_main_type->setValue($data['type']);
			$_status->setValue($data['status']);
		}
		$this->addElements(array(
				$_main_type,$_status,$_main_type_search));
		return $this;
		
	}	
	
}