<?php

class Other_Model_DbTable_DbFeaturearticle extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_website_setting';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
    function getWebsiteSetting($label){
    	$db = $this->getAdapter();
    	$sql="SELECT * FROM `mini_website_setting` AS ws WHERE ws.`label`='$label' AND ws.`status`=1";
    	return $db->fetchRow($sql);
    }
	function updateFeaturearticle($_data,$label_name){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$identity = $_data['identity'];
    		$ids = explode(',', $identity);
			$value='';
    		foreach ($ids as $i){
				if(empty($value)){$value= $_data['feature_article'.$i];}else{ $value= $value.",".$_data['feature_article'.$i];}
			}
				$_arr=array(
    				'value'      => $value,
    				'date_modify'  =>date("Y-m-d"),
    				'status'=>1,
    				'user_id'      => $this->getUserId(),
    		);
			$this->_name="mini_website_setting";
    		$where=" label= '".$label_name."'";
			$this->update($_arr, $where);
			$db->commit();
		}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
}

