<?php

class MenuManager_Model_DbTable_DbCategory extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_category';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllCategory($parent = 0, $spacing = '', $cate_tree_array = '',$data=null){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	
    	$sql="SELECT c.`id`,
    	(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=$lang LIMIT 1) AS name,
    	c.`parent` FROM `mini_category` AS c WHERE c.`status`>-1 AND c.`parent`=$parent ";
		if(!empty($data['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['adv_search']));
			$s_where[] = " (SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=1 LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=2 LIMIT 1)  LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($data['status_search']!=""){
    		$sql.=" AND c.`status`=".$data['status_search'];
    	}
		//if(!empty($data['cate_type'])){
			//$sql.=" AND c.`cate_type`=".$data['cate_type'];
		//}
    	$sql.=" ORDER BY id DESC";
	
    	$query = $db->fetchAll($sql);
    	$stmt = $db->query($sql);
    	$rowCount = count($query);
    	$id='';
    	if ($rowCount > 0) {
    		foreach ($query as $row){
    			$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
    			$cate_tree_array = $this->getAllCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array,$data);
    		}
    	}
    	return $cate_tree_array;
    }
    function addCategory($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		if (!empty(trim($data['title_alias']))){
    			$alias = $data['title_alias'];
    		}else{
    			$alias = md5(date("Y-m-d H:i:s"));
    		}
	    	$arr = array(
	    			'parent'=>$data['parent'],
					'alias_category'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d"),
	    			'description'=>$data['description'],
					'cate_type'=>2,
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_category";
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
	    		 		$this->_name="mini_category_detail";
	    		 		$where1=" category_id=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_cate = array(
		    		 					'category_id'=>$cate_id,
		    		 					'title'=>$data['title'.$title],
		    		 					'sub_title'=>$data['subtitle'.$title],
		    		 					'languageId'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_category_detail";
		    		 			$wheredetail=" category_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    					$this->update($arr_cate,$wheredetail);
		    		 		}else{
			    		 		$arr_cate = array(
			    		 				'category_id'=>$cate_id,
			    		 				'title'=>$data['title'.$title],
			    		 				'sub_title'=>$data['subtitle'.$title],
			    		 				'languageId'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_category_detail";
			    		 		$this->insert($arr_cate);
		    		 		}
		    		 	}
	    		 	}
	    	}else{
	    			$this->_name="mini_category";
	    		 	$arr['create_date']= date("Y-m-d");
	    			$cate_id = $this->insert($arr);
	    			
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_cate = array(
	    						'category_id'=>$cate_id,
	    						'title'=>$data['title'.$title],
	    						'sub_title'=>$data['subtitle'.$title],
	    						'languageId'=>$row['id'],
	    				);
	    				$this->_name="mini_category_detail";
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
	function getCategoryById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_category` WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getCategoryTitleByLang($cate_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT cd.id,cd.`title`,cd.`sub_title`,cd.`languageId` FROM `mini_category_detail` AS cd WHERE cd.`category_id`=$cate_id AND cd.`languageId`=$lang";
		return $db->fetchRow($sql);
	}
	public function CheckTitleAlias($alias){
		$db =$this->getAdapter();
		$sql = "SELECT c.`id` FROM `mini_category` AS c WHERE c.`alias_category`= '$alias'";
		return $db->fetchRow($sql);
	}
	function deleteCategory($id){
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

