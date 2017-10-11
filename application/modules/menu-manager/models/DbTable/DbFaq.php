<?php

class MenuManager_Model_DbTable_DbFaq extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_faq';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllFaq($search){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	$sql="SELECT
			act.`id`,
			(SELECT ad.title FROM `mini_faq_detail` AS ad WHERE ad.faq_id = act.`id` AND language_id=$lang LIMIT 1) AS title,
			act.`status`
			 FROM `mini_faq` AS act 
			 WHERE act.`status`>-1";
    	$where='';
    	if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[] = " (SELECT ad.title FROM `mini_faq_detail` AS ad WHERE ad.faq_id = act.`id` AND language_id=2 LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT ad.title FROM `mini_faq_detail` AS ad WHERE ad.faq_id = act.`id` AND language_id=1 LIMIT 1) LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($search['status_search']!=""){
    		$where.=" AND act.`status` =".$search['status_search'];
    	}
    	
    	$order = "  ORDER BY act.`id` DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addFAQ($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
			
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
	    	$arr = array(
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d H:i"),
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_faq";
	    	 if (!empty($data['id'])){
	    		 	$where=" id=".$data['id'];
	    		 	$this->update($arr, $where);
	    		 	$faq_id =$data['id'];
	    		 	
	    		 	if(!empty($lang)){
	    		 		$iddetail="";
	    		 		foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    				if (empty($iddetail)){ $iddetail=$data['iddetail'.$title];}
		    				else{$iddetail=$iddetail.",".$data['iddetail'.$title];}
	    		 		}
	    		 		$this->_name="mini_faq_detail";
	    		 		$where1=" faq_id=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_article = array(
		    		 					'faq_id'=>$faq_id,
		    		 					'title'=>$data['title'.$title],
		    		 					'description'=>$data['description'.$title],
		    		 					'short_descript'=>strip_tags($data['description'.$title]),
		    		 					'language_id'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_faq_detail";
		    		 			$wheredetail=" faq_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    					$this->update($arr_article,$wheredetail);
		    		 		}else{
			    		 		$arr_article = array(
			    		 				'faq_id'=>$faq_id,
			    		 				'title'=>$data['title'.$title],
			    		 				'description'=>$data['description'.$title],
			    		 				'short_descript'=>strip_tags($data['description'.$title]),
			    		 				'language_id'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_faq_detail";
			    		 		$this->insert($arr_article);
		    		 		}
		    		 	}
	    		 	}
	    	}else{
	    			$this->_name="mini_faq";
	    		 	$arr['create_date']= date("Y-m-d H:i");
	    			$faq_id = $this->insert($arr);
	    			
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_article = array(
	    						'faq_id'=>$faq_id,
	    						'title'=>$data['title'.$title],
	    						'description'=>$data['description'.$title],
	    						'short_descript'=>strip_tags($data['description'.$title]),
	    						'language_id'=>$row['id'],
	    				);
	    				$this->_name="mini_faq_detail";
	    				$this->insert($arr_article);
	    			}
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getFaqById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM $this->_name WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getFaqTitleByLang($faq_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT acd.id,acd.`title`,acd.`language_id`,acd.description FROM `mini_faq_detail` AS acd WHERE acd.`faq_id`=$faq_id AND acd.`language_id`=$lang";
		return $db->fetchRow($sql);
	}
	
	function deleteFaq($id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr = array(
					'status'=>-1,
			);
				$where = " id =".$id;
				$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

