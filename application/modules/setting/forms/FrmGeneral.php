<?php 
Class Setting_Form_FrmGeneral extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate ;//text validate
	protected $filter;
	protected $t_date;
	protected $t_num;
	protected $text;
	protected $check;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->t_date = 'dijit.form.DateTextBox';
		$this->t_num = 'dijit.form.NumberTextBox';
		$this->text = 'dijit.form.TextBox';
		$this->check='dijit.form.CheckBox';
	}
	public function FrmGeneral($data=null){
		$db = new Application_Model_DbTable_DbVdGlobal();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("Phone")
		));
		$_email = new Zend_Dojo_Form_Element_TextBox('email');
		$_email->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("Email")
		));
		$_address = new Zend_Dojo_Form_Element_TextBox('address');
		$_address->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("Address")
		));
		$_items_per_page = new Zend_Dojo_Form_Element_NumberTextBox('items_per_page');
		$_items_per_page->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
		));
		$_items_homepage = new Zend_Dojo_Form_Element_NumberTextBox('items_homepage');
		$_items_homepage->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>true,
		));
		
		$_facebook = new Zend_Dojo_Form_Element_TextBox('facebook');
		$_facebook->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("FACEBOOK")
		));
		$_youtube = new Zend_Dojo_Form_Element_TextBox('youtube');
		$_youtube->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("YOUTUBE")
		));
		$_twitter = new Zend_Dojo_Form_Element_TextBox('twitter');
		$_twitter->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("TWITTER")
		));
		$_googleplus = new Zend_Dojo_Form_Element_TextBox('googleplus');
		$_googleplus->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'placeholder'=>$this->tr->translate("GOOGLE_PLUS")
		));
		
		$_category = new Zend_Dojo_Form_Element_FilteringSelect("category");
		$_category->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$option = array("0"=>$this->tr->translate("CHOOSE_CATEGORY"));
		$result = $db->getCategoryForMenu();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		$_category->setMultiOptions($option);
		$_category->setValue($request->getParam('category'));
		
		if($data!=null){
			$_category->setValue($data['homecategorycontent']['value']);
			$_items_homepage->setValue($data['items_homepage']['value']);
			$_items_per_page->setValue($data['items_per_page']['value']);
			$_address->setValue($data['address']['value']);
			$_email->setValue($data['email']['value']);
			$_phone->setValue($data['tel']['value']);
			$_facebook->setValue($data['facebook']['value']);
			$_youtube->setValue($data['youtube']['value']);
			$_twitter->setValue($data['twitter']['value']);
			$_googleplus->setValue($data['googleplus']['value']);
		}
		$this->addElements(array($_phone,$_email,$_address,$_items_per_page,$_items_homepage,
				$_facebook,$_youtube,$_twitter,$_googleplus,$_category));
		
		return $this;
		
	}
	
}