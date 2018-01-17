<?php

class Department_Model_DbTable_Dbdepartment extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_department';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllDepartment($parent = 0, $spacing = '', $cate_tree_array = '',$data=null){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	
    	$sql="SELECT c.`id`,
    	(SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`id` AND cd.language_id=$lang LIMIT 1) AS name,
    	c.`depart_parentid`,status FROM `mini_department` AS c WHERE c.`status`>-1 AND c.`depart_parentid`=$parent ";
		if(!empty($data['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['adv_search']));
			$s_where[] = " (SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`id` AND cd.language_id=1 LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`id` AND cd.language_id=2 LIMIT 1)  LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($data['status_search']!=""){
    		$sql.=" AND c.`status`=".$data['status_search'];
    	}
		
    	$sql.=" ORDER BY id DESC";
	
    	$query = $db->fetchAll($sql);
    	$stmt = $db->query($sql);
    	$rowCount = count($query);
    	$id='';
    	if ($rowCount > 0) {
    		foreach ($query as $row){
    			$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name'], "status" =>$row['status']);
    			$cate_tree_array = $this->getAllDepartment($id=$row['id'], $spacing . ' - ', $cate_tree_array,$data);
    		}
    	}
    	return $cate_tree_array;
    }
    function addDepartment($data){
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
	    			'depart_parentid'=>$data['parent'],
					'alias'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d H:i:s"),
	    			'create_date'=>date("Y-m-d H:i:s"),
// 	    			'description'=>$data['description'],
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_department";
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
	    		 		$this->_name="mini_department_detail";
	    		 		$where1=" department_id=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_cate = array(
		    		 					'department_id'=>$cate_id,
		    		 					'title'=>$data['title'.$title],
		    		 					//'sub_title'=>$data['subtitle'.$title],
		    		 					'description'=>$data['description'.$title],
		    		 					'language_id'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_department_detail";
		    		 			$wheredetail=" department_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    					$this->update($arr_cate,$wheredetail);
		    		 		}else{
			    		 		$arr_cate = array(
			    		 				'department_id'=>$cate_id,
			    		 				'title'=>$data['title'.$title],
			    		 				//'sub_title'=>$data['subtitle'.$title],
			    		 				'description'=>$data['description'.$title],
			    		 				'language_id'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_department_detail";
			    		 		$this->insert($arr_cate);
		    		 		}
		    		 	}
	    		 	}
	    	}else{
	    			$this->_name="mini_department";
	    		 	$arr['create_date']= date("Y-m-d");
	    			$cate_id = $this->insert($arr);
	    			
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_cate = array(
	    						'department_id'=>$cate_id,
	    						'title'=>$data['title'.$title],
	    						//'sub_title'=>$data['subtitle'.$title],
	    						'description'=>$data['description'.$title],
	    						'language_id'=>$row['id'],
	    				);
	    				$this->_name="mini_department_detail";
	    				$this->insert($arr_cate);
	    			}
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		echo $e->getMessage();exit();
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getDepartmentById($id){
		$db= $this->getAdapter();
		$sql="SELECT d.*,
		(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = d.`user_id` LIMIT 1) AS user_name
		FROM `mini_department` as d WHERE id =".$id;
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
		$sql="SELECT d.id AS dept_id,cd.id,cd.`title`,cd.description,cd.`language_id` 
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
/*about department*/
	function getAboutFirstRecord($alias = null,$lang=null){
		if($lang==null){
			$lang = $this->getCurrentLang();
		}
		$db = $this->getAdapter();
		$sql="SELECT amd.id,amd.`title`,amd.description,amd.`language_id`
		FROM `mini_aboutministry_detail` AS amd,mini_aboutministry as am
		WHERE
		am.id=amd.ministy_id
		AND amd.`language_id`=$lang ";
		if($alias!=null){
		$sql.=" AND am.alias = '".$alias."'";
		}
		$sql.=' ORDER BY amd.id ASC  LIMIT 1 ';
		return $db->fetchRow($sql);
	}
}

