<?php 
Class MenuManager_Form_FrmMenu extends Zend_Dojo_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmMenuManager($data=null){
		$db = new Application_Model_DbTable_DbVdGlobal();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("TITLE")
		));
		$_title->setValue($request->getParam("title"));
		
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
		$id_cient = new Zend_Form_Element_Hidden("idclient");
// 		print_r($data);exit();
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch'
		));
		$note = new Zend_Dojo_Form_Element_TextBox('note');
		$note->setAttribs(array('dojoType'=>'dijit.form.TextBox','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		$note->setValue($request->getParam('note'));
		
		if($data!=null){
				
			$_status->setValue($data['status']);
			$id->setValue($data['id']);
			$note->setValue($data['description']);
			$_title->setValue($data['title']);
		}
		
		$this->addElements(array($id,$_btn_search,$_title,$_status,$note,$_status_search));
		return $this;
	}	
	public function FrmMenuItems($data=null){
		$db = new Application_Model_DbTable_DbVdGlobal();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_ValidationTextBox('title');
		$_title->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'required'=>'true',
				'placeholder'=>$this->tr->translate("TITLE")
		));
		
		$ordering = new Zend_Dojo_Form_Element_NumberTextBox('ordering');
		$ordering->setAttribs(array(
				'dojoType'=>'dijit.form.NumberTextBox',
				'class'=>'fullside',
				'required'=>'true',
		));
		
		$url_link = new Zend_Dojo_Form_Element_TextBox('url_link');
		$url_link->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		
		$_title_alias = new Zend_Dojo_Form_Element_ValidationTextBox('title_alias');
		$_title_alias->setAttribs(array(
				'dojoType'=>'dijit.form.ValidationTextBox',
				'class'=>'fullside',
				'onblur'=>'checkTitle();',
				'onkeypress'=>'return checkSpcialChar(event);',
				'placeholder'=>$this->tr->translate("Alias auto generate from title")
		));
		$_title_alias->setValue($request->getParam("title_alias"));
		
		$_parent = new Zend_Dojo_Form_Element_FilteringSelect("parent");
		$_parent->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$option = array("0"=>$this->tr->translate("MENU_ITEMS_ROOT"));
		$result = $db->getMenuItems();
		//if(!empty($result))foreach($result AS $row){$option[$row['id']]=$row['name'];}
		$_parent->setMultiOptions($option);
		//$_parent->setValue($request->getParam('parent'));
		
		$_menu_type = new Zend_Dojo_Form_Element_FilteringSelect("menu_type");
		$_menu_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside',
				'onChange'=>'getControllByMenuType();',
				));
		$option = array("-1"=>$this->tr->translate("CHOOSE_MENU_TYPE"));
		$result_menutype = $db->getMenuType();
		if(!empty($result_menutype))foreach($result_menutype AS $rs){
			$option[$rs['id']]=$rs['name'];
		}
		$_menu_type->setMultiOptions($option);
		$_menu_type->setValue($request->getParam('parent'));
		
		$_target_type = new Zend_Dojo_Form_Element_FilteringSelect("target_type");
		$_target_type->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$_arr_taget=array(
				"1"=>$this->tr->translate("PARENT"),
				"0"=>$this->tr->translate("NEW_WINDOW"),
		);
		$_target_type->setMultiOptions($_arr_taget);
		$_target_type->setValue($request->getParam('target_type'));
		
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
		
		$_address = new Zend_Dojo_Form_Element_TextBox('address');
		$_address->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_address->setValue($request->getParam("address"));
		
		$email = new Zend_Dojo_Form_Element_TextBox('email');
		$email->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$email->setValue($request->getParam("email"));
		
		$_phone = new Zend_Dojo_Form_Element_TextBox('phone');
		$_phone->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_phone->setValue($request->getParam("phone"));
		
		$_fax = new Zend_Dojo_Form_Element_TextBox('fax');
		$_fax->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$_fax->setValue($request->getParam("fax"));
		
		$map = new Zend_Dojo_Form_Element_Textarea('map');
		$map->setAttribs(array('dojoType'=>'dijit.form.Textarea','class'=>'fullside',
				'style'=>'width:100%;min-height:60px;'));
		$map->setValue($request->getParam('map'));
		
		$_article = new Zend_Dojo_Form_Element_FilteringSelect("article");
		$_article->setAttribs(array(
				'dojoType'=>'dijit.form.FilteringSelect',
				'required'=>'true',
				'class'=>'fullside'));
		$option = array("-1"=>$this->tr->translate("CHOOSE_ARTICLE"));
		$result = $db->getAllArticle();
		if(!empty($result))foreach($result AS $row){
			$option[$row['id']]=$row['title'];
		}
		$_article->setMultiOptions($option);
		$_article->setValue($request->getParam('article'));
		
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
		
// 		$_menu_manager = new Zend_Dojo_Form_Element_FilteringSelect("menu_manager");
// 		$_menu_manager->setAttribs(array(
// 				'dojoType'=>'dijit.form.FilteringSelect',
// 				'required'=>'true',
// 				'onchange'=>'getMenuItemsByMenumanager();',
// 				'class'=>'fullside'));
// 		$option_manager = array("0"=>$this->tr->translate("SELECT_MENU_MANAGER"));
// 		$result = $db->getMenuManager();
// 		if(!empty($result))foreach($result AS $row){
// 			$option_manager[$row['id']]=$row['name'];
// 		}
// 		$_menu_manager->setMultiOptions($option_manager);
// 		$_menu_manager->setValue($request->getParam('menu_manager'));
		
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
		
		$advance_search = new Zend_Dojo_Form_Element_TextBox('advance_search');
		$advance_search->setAttribs(array(
				'dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',
		));
		$advance_search->setValue($request->getParam("advance_search"));
		
		if($data!=null){
			$_parent->setValue($data['parent']);
			$_status->setValue($data['status']);
			$_title_alias->setValue($data['alias_menu']);
			$_title->setValue($data['title']);
			$_target_type->setValue($data['target_type']);
			$_menu_type->setValue($data['menu_type_id']);
// 			$_menu_manager->setValue($data['menu_manager_id']);
			$ordering->setValue($data['ordering']);
			$_fax->setValue($data['fax']);
			$_phone->setValue($data['tel']);
			$_address->setValue($data['address']);
			$email->setValue($data['email']);
			$map->setValue($data['map']);
			$_article->setValue($data['article_id']);
			$_category->setValue($data['category_id']);
		}
		
		$this->addElements(array($url_link,$ordering,$_title,$_status,$_parent,$_title_alias,$_menu_type,$_target_type,
// 				$_menu_manager,
				$_fax,$_phone,$_address,$email,$_article,$_category,$map,$_status_search,$advance_search
				));
		return $this;
	}
}