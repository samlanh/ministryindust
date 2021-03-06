<?php

class About_Model_DbTable_DbHotNew extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_aboutministry';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllHotNew($data=null){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	$sql="SELECT id,title_en,title_kh,create_date,modify_date,
		               (SELECT u.first_name FROM `rms_users` AS u WHERE u.id = user_id LIMIT 1) AS user_name,
		               status
		    	 FROM `mini_hotnews` where 1 ";
		if(!empty($data['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['adv_search']));
			$s_where[] = " title_en LIKE '%{$s_search}%'";
			$s_where[] = " title_kh LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($data['status_search']!=""){
    		$sql.=" AND `status`=".$data['status_search'];
    	}
    	$sql.=" ORDER BY id DESC";
    	return $db->fetchAll($sql);
    }
    function getLastHotNew(){
    	$sql="SELECT COUNT(id)+1 FROM mini_hotnews WHERE language_id=1 order BY id DESC";
    	return $this->getAdapter()->fetchOne($sql);
    }
    function addHotNew($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		$this->_name="mini_hotnews";
    	
    		if(!empty($lang)) foreach($lang as $row){
    			$title = str_replace(' ','',$row['title']);
    			if($title=='English'){
    				$arr['title_en']=$data['title_'.$title];
    				$arr['url_en']=$data['url_'.$title];
    			}else{
    				$arr['title_kh']=$data['title_'.$title];
    				$arr['url_kh']=$data['url_'.$title];
    				
    				$arr['status']=$data['status'];
    				$arr['create_date']=date("Y-m-d H:i:s");
    				$arr['modify_date']=date("Y-m-d H:i:s");
    				$arr['user_id']=$this->getUserId();
    			}
    		}
    		$this->insert($arr);
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function updateHotNew($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$dbglobal = new Application_Model_DbTable_DbVdGlobal();
			$lang = $dbglobal->getLaguage();
			$this->_name="mini_hotnews";
			 
			if(!empty($lang)) foreach($lang as $row){
				$title = str_replace(' ','',$row['title']);
				if($title=='English'){
					$arr['title_en']=$data['title_'.$title];
					$arr['url_en']=$data['url_'.$title];
				}else{
					$arr['title_kh']=$data['title_'.$title];
					$arr['url_kh']=$data['url_'.$title];
			
					$arr['status']=$data['status'];
					$arr['create_date']=date("Y-m-d H:i:s");
					$arr['modify_date']=date("Y-m-d H:i:s");
					$arr['user_id']=$this->getUserId();
				}
			}
			$where = " id = ".$data['id'];
			$this->update($arr, $where);
			
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getHotnewById($id,$lang){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_hotnews` WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getAboutById($id){
		$db= $this->getAdapter();
		$sql="SELECT  m.*,
			(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = m.`user_id` LIMIT 1) AS user_name
		 FROM `mini_aboutministry` as m WHERE m.id =".$id;
		return $db->fetchRow($sql);
	}
	function getAboutTitleByLang($cate_id,$lang=null){
		if($lang==null){
			$lang = $this->getCurrentLang();
		}
		$db = $this->getAdapter();
		$sql="SELECT cd.id,cd.`title`,cd.description,cd.`language_id` FROM `mini_aboutministry_detail` AS cd WHERE cd.`ministy_id`=$cate_id AND cd.`language_id`=$lang ";
		return $db->fetchRow($sql);
	}

	
}

