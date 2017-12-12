<?php

class Company_Model_DbTable_Dbcompanytype extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_company_type';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllCompanyType($data){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$sql="
	    	SELECT ct.`id`,
			(SELECT ctd.title FROM `mini_company_type_detail` AS ctd WHERE ctd.languageId= $lang AND ctd.company_type_id = ct.`id`) AS title,
			CASE    
						WHEN  ct.`type` = 1 THEN '".$tr->translate("For Handicrafts,SME")."'
						WHEN  ct.`type` = 2 THEN '".$tr->translate("For The Water Supply Sector")."'
						END AS `type`,
			ct.`status`,
			(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = ct.`user_id` LIMIT 1) AS user_name
			 FROM `mini_company_type` AS ct WHERE 1 ";
    	
		if(!empty($data['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['adv_search']));
			$s_where[] = " (SELECT ctd.title FROM `mini_company_type_detail` AS ctd WHERE ctd.languageId= 1 AND ctd.company_type_id = ct.`id`) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT ctd.title FROM `mini_company_type_detail` AS ctd WHERE ctd.languageId= 2 AND ctd.company_type_id = ct.`id`) LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($data['status']>-1){
			$sql.=' AND ct.status = '.$data['status'];
		}
		if ($data['main_type_search']>-1){
			$sql.=' AND ct.`type` = '.$data['main_type_search'];
		}
		$sql.=" ORDER BY ct.id DESC";
    	return $db->fetchAll($sql);
    }
	function addCompanyType($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
	    	$arr = array(
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d H:i:s"),
	    			'description'=>$data['description'],
					'type'=>$data['main_type'],
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_company_type";
	    	 if (!empty($data['id'])){
	    		 	$where=" id=".$data['id'];
	    		 	$this->update($arr, $where);
	    		 	$cate_id =$data['id'];
	    		 	if(!empty($lang)){
	    		 		$iddetail="";
	    		 		foreach($lang as $row){
	    		 			$title = str_replace(' ','',$row['title']);
	    		 			if (empty($iddetail)){
	    		 				$iddetail=$data['iddetail'.$title];
	    		 			}else{
	    		 				$iddetail=$iddetail.",".$data['iddetail'.$title];
	    		 			}
	    		 		}
	    		 		$this->_name="mini_company_type_detail";
	    		 		$where1=" company_type_id=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_cate = array(
		    		 					'company_type_id'=>$cate_id,
		    		 					'title'=>$data['title'.$title],
		    		 					'languageId'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_company_type_detail";
		    		 			$wheredetail=" company_type_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    					$this->update($arr_cate,$wheredetail);
		    		 		}else{
			    		 		$arr_cate = array(
			    		 				'company_type_id'=>$cate_id,
			    		 				'title'=>$data['title'.$title],
			    		 				'languageId'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_company_type_detail";
			    		 		$this->insert($arr_cate);
		    		 		}
		    		 	}
	    		 	}
	    	}else{
	    			$this->_name="mini_company_type";
	    		 	$arr['create_date']= date("Y-m-d H:i:s");
	    			$cate_id = $this->insert($arr);
	    			
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_cate = array(
	    						'company_type_id'=>$cate_id,
	    						'title'=>$data['title'.$title],
	    						'languageId'=>$row['id'],
	    				);
	    				$this->_name="mini_company_type_detail";
	    				$this->insert($arr_cate);
	    			}
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getCompanyTypeById($id){
		$db= $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT ct.*,
		(SELECT ctd.title FROM `mini_company_type_detail` AS ctd WHERE ctd.languageId= $lang AND ctd.company_type_id = ct.`id`) AS title
		 FROM `mini_company_type` as ct WHERE ct.id =".$id;
		return $db->fetchRow($sql);
	}
	function getCompanyTypeTitleByLang($cate_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_company_type_detail` AS cd WHERE cd.`company_type_id`=$cate_id AND cd.`languageId`=$lang";
		return $db->fetchRow($sql);
	}
}

