<?php

class About_Model_DbTable_Dbabout extends Zend_Db_Table_Abstract
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
    public function getAllAbout($parent = 0, $spacing = '', $cate_tree_array = '',$data=null){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	
    	$sql="SELECT am.`id`, 
(SELECT amd.title FROM `mini_aboutministry_detail` AS amd WHERE amd.ministy_id = am.`id`
 AND amd.language_id=$lang LIMIT 1) AS name, 
 am.`parent_id`,status FROM `mini_aboutministry` AS am WHERE am.`status`>-1  AND am.`parent_id`=$parent ";
		if(!empty($data['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['adv_search']));
			$s_where[] = " (SELECT amd.title FROM `mini_aboutministry_detail` AS amd WHERE amd.ministy_id = md.`id` AND amd.language_id=1 LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT amd.title FROM `mini_aboutministry_detail` AS amd WHERE amd.ministy_id = md.`id` AND amd.language_id=2 LIMIT 1)  LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($data['status_search']!=""){
    		$sql.=" AND md.`status`=".$data['status_search'];
    	}
    	$sql.=" ORDER BY id DESC";
	
    	$query = $db->fetchAll($sql);
    	$stmt = $db->query($sql);
    	$rowCount = count($query);
    	$id='';
    	if ($rowCount > 0) {
    		foreach ($query as $row){
    			$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name'], "status" =>$row['status']);
    			$cate_tree_array = $this->getAllAbout($id=$row['id'], $spacing . ' - ', $cate_tree_array,$data);
    		}
    	}
    	return $cate_tree_array;
    }
    function addAboutministry($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		if (!empty(trim($data['alias']))){
    			$alias = $data['alias'];
    		}else{
    			$alias = md5(date("Y-m-d H:i:s"));
    		}
	    	$arr = array(
	    			'parent_id'=>$data['parent'],
					'alias'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d"),
	    			'date'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_aboutministry";
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
	    		 		$this->_name="mini_aboutministry_detail";
	    		 		$where1=" ministy_id=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_cate = array(
		    		 					'ministy_id'=>$cate_id,
		    		 					'title'=>$data['title'.$title],
		    		 					'description'=>$data['description'.$title],
		    		 					'language_id'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_aboutministry_detail";
		    		 			$wheredetail=" department_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    					$this->update($arr_cate,$wheredetail);
		    		 		}else{
			    		 		$arr_cate = array(
			    		 				'ministy_id'=>$cate_id,
			    		 				'title'=>$data['title'.$title],
			    		 				'description'=>$data['description'.$title],
			    		 				'language_id'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_aboutministry_detail";
			    		 		$this->insert($arr_cate);
		    		 		}
		    		 	}
	    		 	}
	    	}else{
	    			$this->_name="mini_aboutministry";
	    		 	$arr['date']= date("Y-m-d");
	    			$cate_id = $this->insert($arr);
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_cate = array(
	    						'ministy_id'=>$cate_id,
	    						'title'=>$data['title'.$title],
	    						'description'=>$data['description'.$title],
	    						'language_id'=>$row['id'],
	    				);
	    				$this->_name="mini_aboutministry_detail";
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
	function getDepartmentById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_department` WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getDepartmentTitleByLang($cate_id,$lang=null){
		if($lang==null){
			$lang = $this->getCurrentLang();
		}
		$db = $this->getAdapter();
		$sql="SELECT cd.id,cd.`title`,cd.description,cd.`language_id` FROM `mini_department_detail` AS cd WHERE cd.`department_id`=$cate_id AND cd.`language_id`=$lang ";
		return $db->fetchRow($sql);
	}
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

