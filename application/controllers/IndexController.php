<?php

class IndexController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
//     	$this->_helper->layout()->disableLayout();
		$db = new Application_Model_DbTable_DbGlobalSelect();
		
		$slidepartner = $db->getWebsiteSetting("slide_partner");
		if(!empty($slidepartner)){
			if(!empty($slidepartner['value'])){
				$partner = explode(",", $slidepartner['value']);
				$this->view->partner = $partner;
			}			
		}
		
		$video = $db->getWebsiteSetting("youtubeframe");
		if(!empty($video)){
			if(!empty($video['value'])){
				$this->view->videos = $video;
			}			
		}
		
		$homecategory = $db->getWebsiteSetting("homecategorycontent");
		if (!empty($homecategory['value'])){
			$this->view->categoryinfo = $db->getCategoryByID($homecategory['value']);
			$this->view->gethomearticle = $db->getArcticleByCateLastesArticle($homecategory['value']);
		}
		
		$slideshow = $db->getWebsiteSetting("slideshow");
		if(!empty($slideshow)){
			if(!empty($slideshow['value'])){
				$image = explode(",", $slideshow['value']);
				$this->view->slideshow = $image;
			}
		}
		
		$document = $db->getHomeDocument();
		$this->view->document = $document;
		$this->view->home_article = $db->getWebsiteSetting("home_article");
// 		$this->view->getAllDepart = $db->getAllDepartment();
		$this->view->tab = $db->getAllTab();
		
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->rsprovince = $db->getAllProvince();
		$db = new Application_Model_DbTable_DbVdGlobal();
		$this->view->rsdepartment = $db->getAllDepartment();
		$this->view->rsmessage = $db->getMessageHomepage();
		
		
    }
	
   
    function administratorAction(){
		// action body
        /* set this to login page to change the character charset of browsers to Utf-8  ...*/ 
    	$session_user=new Zend_Session_Namespace('auth');
    	if (!empty($session_user->user_id)){
    		$this->_redirect("/menu-manager/menu-items");
    	}
    	$this->_helper->layout()->disableLayout();
		$form=new Application_Form_FrmLogin();				
		$form->setAction('index');		
		$form->setMethod('post');
		$form->setAttrib('accept-charset', 'utf-8');
		$this->view->form=$form;
		$key = new Application_Model_DbTable_DbKeycode();
		$this->view->data=$key->getKeyCodeMiniInv(TRUE);		
		$dbsele = new Application_Model_DbTable_DbGlobalSelect();
		$this->view->logo = $dbsele->getWebsiteSetting('logo');
		if($this->getRequest()->isPost())		
		{
			$formdata=$this->getRequest()->getPost();
			if($form->isValid($formdata))
			{
				$session_lang=new Zend_Session_Namespace('lang');
				$session_lang->lang_id=$formdata["lang"];//for creat session
				Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
				$user_name=$form->getValue('txt_user_name');
				$password=$form->getValue('txt_password');
				$db_user=new Application_Model_DbTable_DbUsers();
				if($db_user->userAuthenticate($user_name,$password)){					
// 					$this->view->msg = 'Authentication Sucessful!';
// 					$this->view->err="0";
					
					
					$user_id=$db_user->getUserID($user_name);
					$user_info = $db_user->getUserInfo($user_id);
					
					$arr_acl=$db_user->getArrAcl($user_info['user_type']);
					$session_user->url_report=$db_user->getArrAclReport($user_info['user_type']);
					$session_user->user_id=$user_id;
					$session_user->user_name=$user_name;
					$session_user->pwd=$password;		
					$session_user->level= $user_info['user_type'];
					$session_user->last_name= $user_info['last_name'];
					$session_user->first_name= $user_info['first_name'];
// 					$session_user->theme_style=$db_user->getThemeByUserId($user_id);
					
					$a_i = 0;
					$arr_actin = array();	
					for($i=0; $i<count($arr_acl);$i++){
						$arr_module[$i]=$arr_acl[$i]['module'];
					}
						
					$arr_module=(array_unique($arr_module));
					$arr_actin=(array_unique($arr_actin));
					$arr_module=$this->sortMenu($arr_module);
					
					$session_user->arr_acl = $arr_acl;
					$session_user->arr_module = $arr_module;
					$session_user->arr_actin = $arr_actin;
						
					$session_user->lock();
					
					$log=new Application_Model_DbTable_DbUserLog();
					$log->insertLogin($user_id);
					foreach ($arr_module AS $i => $d){
						if($d !== 'transfer'){
							$url = '/' . $arr_module[0];
						}
						else{
							$url = self::REDIRECT_URL;
							break;
						}
					}
					
					Application_Form_FrmMessage::redirectUrl("/menu-manager/menu-items");	
					exit();
				}
				else{					
					$this->view->msg = 'ឈ្មោះ​អ្នក​ប្រើ​ប្រាស់ និង ពាក្យ​​សំងាត់ មិន​ត្រឺម​ត្រូវ​ទេ ';
				}
					
			}
			else{				
				$this->view->msg = 'លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់ទេ!';
			}			
		}
	}
    protected function sortMenu($menus){
    	$menus_order = Array ( 'home','menu-manager','department','document','company','about','product','report','rsvacl','setting','other');
    	$temp_menu = Array();
    	$menus=array_unique($menus);
    	foreach ($menus_order as $i => $val){
    		foreach ($menus as $k => $v){
    			if($val == $v){
    				$temp_menu[] = $val;
    				unset($menus[$k]);
    				break;
    			}
    		}
    	}
    	return $temp_menu;    	
    }

    public function logoutAction()
    {
        // action body
       // $this->_redirect("/index");
        if($this->getRequest()->getParam('value')==1){        	
        	$aut=Zend_Auth::getInstance();
        	$aut->clearIdentity();        	
        	$session_user=new Zend_Session_Namespace('auth');
        	
        	$log=new Application_Model_DbTable_DbUserLog();
			$log->insertLogout($session_user->user_id);
			
        	$session_user->unsetAll();       	
	        if (empty($session_user->user_id)){ 
        	Application_Form_FrmMessage::redirectUrl("/");
	        }
        	exit();
        } 
    }

    public function changepasswordAction()
    {
        // action body
        if ($this->getRequest()->isPost()){ 
			$session_user=new Zend_Session_Namespace('auth');    		
    		$pass_data=$this->getRequest()->getPost();
    		if ($pass_data['password'] == $session_user->pwd){
    			    			 
				$db_user = new Application_Model_DbTable_DbUsers();				
				try {
					$db_user->changePassword($pass_data['new_password'], $session_user->user_id);
					$session_user->unlock();	
					$session_user->pwd=$pass_data['new_password'];
					$session_user->lock();
					Application_Form_FrmMessage::Sucessfull('ការផ្លាស់ប្តូរដោយជោគជ័យ', self::REDIRECT_URL);
				} catch (Exception $e) {
					Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
				}				
    		}
    		else{
    			Application_Form_FrmMessage::message('ការផ្លាស់ប្តូរត្រូវបរាជ័យ');
    		}
        }   
    }

    public function errorAction()
    {
        // action body
        
    }
    public static function start(){
    	return ($this->getRequest()->getParam('limit_satrt',0));
    }
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id = $data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }
   
    function checkmailAction(){ //ajax check email has been use or not
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Application_Model_DbTable_DbVdGlobal();
    		$rs = $db->checkEmailClient($data['email']);
    		print_r(Zend_Json::encode($rs));
    		exit();
    	}
    }
    function checkmediaAction(){
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$media_session=new Zend_Session_Namespace('mediascreen');
    		$media_session->width = $data['screenwidth'];
    		print_r(Zend_Json::encode($media_session->width));
    		exit();
    	}
    }
	function contactFormSendMailAction(){
    	
    	if ($this->getRequest()->isPost()){
			$db_select = new Application_Model_DbTable_DbGlobalSelect();
			$row = $db_select->getWebsiteSetting('email');
			
			$db = new Application_Model_DbTable_DbSentMail();
    		$data = $this->getRequest()->getPost();
			
    		$data['website_email']=$row['value'];
    		$message = $db->ContactFormSendmail($data);
			
			$session_message_session = new Zend_Session_Namespace('sentmail_msg');
			$session_message_session->message = $message;
			$this->_redirect($data['come']);
    	}
    	
    }
	

}



