<?php

class About_Model_DbTable_Dbtab extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_tab';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllTab($search){
    	$db=$this->getAdapter();
    	$sql="SELECT alias,title,
			(SELECT sub.title FROM mini_tab AS sub WHERE sub.alias=mini_tab.alias AND language_id=1 ) AS title_eng
 				,status,create_date,modify_date,
				(SELECT u.first_name FROM `rms_users` As u WHERE u.id=mini_tab.user_id LIMIT 1) As user_name
    	FROM `mini_tab` WHERE language_id=2 ";
		if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[] = "  title LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($search['status_search']!=""){
    		$sql.=" AND `status`=".$search['status_search'];
    	}
    	$sql.=" ORDER BY alias DESC ";
    	return $db->fetchAll($sql);

    }
    function addNewTab($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		$this->_name="mini_tab";
    			$last_tab = $this->getLastTab();
    			if(!empty($lang)) foreach($lang as $row){
    				$title = str_replace(' ','',$row['title']);
    				$arr = array(
    						'title'=>$data['tab'.$title],
    						'description'=>$data['description'.$title],
    						'language_id'=>$row['id'],
    						'alias'=>$last_tab,
    						'status'=>$data['status'],
    						'modify_date'=>date("Y-m-d h:i:s"),
    						'create_date'=>date("Y-m-d h:i:s"),
    						'user_id'=>$this->getUserId(),
    				);
    				
    				$this->insert($arr);
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		$db->rollBack();
    		echo $e->getMessage();exit();
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		
    	}
	}
	function getLastTab(){
		$sql="SELECT COUNT(id)+1 FROM mini_tab WHERE language_id=1 ";
		return $this->getAdapter()->fetchOne($sql);
	}
	function getTabById($lang,$id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_tab` WHERE language_id =$lang AND alias =".$id;
		return $db->fetchRow($sql);
	}
// 	function getDepartmentTitleByLang($cate_id,$lang=null){
// 		if($lang==null){
// 			$lang = $this->getCurrentLang();
// 		}
// 		$db = $this->getAdapter();
// 		$sql="SELECT cd.id,cd.`title`,cd.description,cd.`language_id` FROM `mini_department_detail` AS cd WHERE cd.`department_id`=$cate_id AND cd.`language_id`=$lang ";
// 		return $db->fetchRow($sql);
// 	}
	function getDepartmentFirstRecord($alias = null,$lang=null){
		if($lang==null){
			$lang = $this->getCurrentLang();
		}
		$db = $this->getAdapter();
		$sql="SELECT cd.id,cd.`title`,cd.description,cd.`language_id` 
			FROM `mini_department_detail` AS cd,mini_department as d
		WHERE 
			d.id=cd.department_id
			AND cd.`language_id`=$lang ";
		if($alias!=null){
			$sql.=" AND d.alias = '".$alias."'";
		}
		$sql.=' ORDER BY cd.id ASC  LIMIT 1 ';
		return $db->fetchRow($sql);
	}
// 	public function CheckTitleAlias($alias){
// 		$db =$this->getAdapter();
// 		$sql = "SELECT c.`id` FROM `mini_department` AS c WHERE c.`alias_category`= '$alias'";
// 		return $db->fetchRow($sql);
// 	}
	
}

