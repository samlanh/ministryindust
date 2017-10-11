<?php 
Class MenuManager_Form_FrmCategory extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmCategory($data=null){
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
		
		$_title_alias = new Zend_Dojo_Form_Element_TextBox('title_alias');
		$_title_alias->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'onblur'=>'checkTitle();',
				'onkeypress'=>'return checkSpcialChar(event);',
				'placeholder'=>$this->tr->translate("Alias auto generate from title")
		));
		$_title_alias->setValue($request->getParam("title_alias"));
		
		
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

		$note = new Zend_Dojo_Form_Element_TextBox('note');
		$note->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		$note->setValue($request->getParam('note'));
		
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
		
		$_cateory_parent = new Zend_Dojo_Form_Element_FilteringSelect("parent");
		$_cateory_parent->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$option = array("0"=>$this->tr->translate("NO_PARENT"));
		$result = $db->getCategory();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['name'];
		}
		if (!empty($data)){unset($option[$data['id']]);}
		$_cateory_parent->setMultiOptions($option);
		$_cateory_parent->setValue($request->getParam('parent'));
		
		$_cate_type = new Zend_Dojo_Form_Element_FilteringSelect("cate_type");
		$_arr=array(
				"1"=>$this->tr->translate("FOR_PRODUCT"),
				"2"=>$this->tr->translate("FOR_MENU_MANAGER"),
		);
		$_cate_type->setMultiOptions($_arr);
		$_cate_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$_cate_type->setValue($request->getParam('cate_type'));
		if($data!=null){
			$_cate_type->setValue($data['cate_type']);
			$_cateory_parent->setValue($data['parent']);
			$_status->setValue($data['status']);
			$id->setValue($data['id']);
// 			$note->setValue($data['description']);
// 			$_title->setValue($data['title']);
			$_title_alias->setValue($data['alias_category']);
		}
		
		$this->addElements(array($id,$_btn_search,$_title,$_status,$note,$_status_search,$_title_alias,
				$_cateory_parent,$_cate_type
				));
		return $this;
	}	
	public function FrmArticle($data=null){
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
	
		$_title_alias = new Zend_Dojo_Form_Element_TextBox('title_alias');
		$_title_alias->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
				'onblur'=>'checkTitle();',
				'onkeypress'=>'return checkSpcialChar(event);',
				'placeholder'=>$this->tr->translate("Alias auto generate from title")
		));
		$_title_alias->setValue($request->getParam("title_alias"));
	
	
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
	
		$note = new Zend_Dojo_Form_Element_TextBox('note');
		$note->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		$note->setValue($request->getParam('note'));
	
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
			$_category->setValue($data['category_id']);
			$_status->setValue($data['status']);
			$id->setValue($data['id']);
			$_title_alias->setValue($data['alias_article']);
		}
	
		$this->addElements(array($id,$_title,$_status,$note,$_status_search,$_title_alias,
				$_category
		));
		return $this;
	}
}