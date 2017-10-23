<?php
class Setting_generalController extends Zend_Controller_Action {
	
	
public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
	}
	public function indexAction()
	{
		$id = $this->getRequest()->getParam("id");
		if($this->getRequest()->isPost()){
			try{
				$data = $this->getRequest()->getPost();
				$db = new Setting_Model_DbTable_DbGeneral();
				$db->updateWebsitesetting($data);
// 				Application_Form_FrmMessage::message("ការ​បញ្ចូល​ជោគ​ជ័យ !");
			}catch (Exception $e){
				Application_Form_FrmMessage::message("EDIT_FAILE");
				echo $e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$db_gs = new Application_Model_DbTable_DbGlobalSelect();
		$row =array();
		$row['tel'] = $db_gs->getWebsiteSetting('tel');
		$row['email'] = $db_gs->getWebsiteSetting('email');
		$row['address'] = $db_gs->getWebsiteSetting('address');
		$row['items_per_page'] = $db_gs->getWebsiteSetting('items_per_page');
		$row['items_homepage'] = $db_gs->getWebsiteSetting('items_homepage');
		$row['facebook'] = $db_gs->getWebsiteSetting('facebook');
		$row['youtube'] = $db_gs->getWebsiteSetting('youtube');
		$row['twitter'] = $db_gs->getWebsiteSetting('twitter');
		$row['googleplus'] = $db_gs->getWebsiteSetting('googleplus');
		$row['homecategorycontent'] = $db_gs->getWebsiteSetting('homecategorycontent');
		$this->view->logo = $db_gs->getWebsiteSetting('logo');
		$this->view->headerbg = $db_gs->getWebsiteSetting('headebackground');
		$fm = new Setting_Form_FrmGeneral();
		$frm = $fm->FrmGeneral($row);
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_general = $frm;
	}
	
}

