<?php

class MenuManager_Model_DbTable_DbMenuItems extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_menu';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
	public function getMenuItems($parent = 0, $spacing = '', $cate_tree_array = '',$search=null){
		$db=$this->getAdapter();
		$lang = $this->getCurrentLang();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT m.`id`,
			(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=$lang LIMIT 1) AS name,
			m.ordering,m.`parent`
			 FROM `mini_menu` AS m WHERE m.`status`>-1 AND m.`parent` = $parent ";
		if ($search['status_search']!=''){
			$sql.=" AND m.`status`=".$search['status_search'];
		}
// 		if ($search['menu_manager']>0){
// 			$sql.=" AND m.`menu_manager_id`=".$search['menu_manager'];
// 		}
		if(!empty($search['advance_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['advance_search']));
			$s_where[] = " (SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=$lang LIMIT 1) LIKE '%{$s_search}%'";
			$sql .=' AND ('.implode(' OR ',$s_where).')';
		}
		$sql.=" ORDER BY m.ordering ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getMenuItems($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
    function addMenuItems($data){
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
	    			//'menu_manager_id'=>$data['menu_manager'],
	    	        'ordering'=>$data['ordering'],
	    			'menu_manager_id'=>1,
	    			'parent'=>$data['parent'],
	    			'alias_menu'=>$alias,
	    			'menu_type_id'=>$data['menu_type'],
	    			'target_type'=>$data['target_type'],
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    		);
		    	if($data['menu_type']==1 || $data['menu_type']==2){
		    		$arr['category_id'] = $data['category'];
		    	}elseif ($data['menu_type']==3){
		    		$arr['article_id'] = $data['article'];
		    	}
	    	if (!empty($data['id'])){
	    		$menuid = $data['id'];
	    		$where = " id =".$data['id'];
	    		$this->_name="mini_menu";
	    		$this->update($arr, $where);
	    		
	    		if(!empty($lang)){
	    			$iddetail="";
	    			foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				if (empty($iddetail)){ $iddetail=$data['iddetail'.$title];}
	    				else{$iddetail=$iddetail.",".$data['iddetail'.$title];}
	    			}
		    		$this->_name="mini_menu_detail";
		    		$where1=" menu_id=".$data['id'];
		    		if (!empty($iddetail)){$where1.=" AND id NOT IN (".$iddetail.")";}
		    		$this->delete($where1);
	    		
		    		foreach($lang as $row){
		    			$title = str_replace(' ','',$row['title']);
		    			if (!empty($data['iddetail'.$title])){
		    				$arr_menudetail = array(
		    						'menu_id'=>$menuid,
		    						'title'=>$data['title'.$title],
		    						'languageId'=>$row['id'],
		    						'url'=>$data['url_'.$title],
		    				);
		    				$this->_name="mini_menu_detail";
		    				$wheredetail=" menu_id=".$data['id']." AND id=".$data['iddetail'.$title];
		    				$this->update($arr_menudetail,$wheredetail);
		    			}else{
			    			$arr_menudetail = array(
			    					'menu_id'=>$menuid,
			    					'title'=>$data['title'.$title],
			    					'languageId'=>$row['id'],
			    					'url'=>$data['url_'.$title],
			    			);
			    			$this->_name="mini_menu_detail";
			    			$this->insert($arr_menudetail);
		    			}
		    		}
	    		}
	    	}else{
	    		$arr['create_date']=date("Y-m-d");
	    		$this->_name="mini_menu";
	    		$menuid=$this->insert($arr);
	    		
	    		if(!empty($lang)) foreach($lang as $row){
	    			$title = str_replace(' ','',$row['title']);
	    			$arr_menudetail = array(
	    					'menu_id'=>$menuid,
	    					'title'=>$data['title'.$title],
	    					'url'=>$data['url_'.$title],
	    					'languageId'=>$row['id'],
	    			);
	    			$this->_name="mini_menu_detail";
	    			$this->insert($arr_menudetail);
	    		}
	    	}
		    	
		    	if ($data['menu_type']==4){
		    		$arr_menucontact = array(
		    				'menu_id'=>$menuid,
		    				'address'=>$data['address'],
		    				'tel'=>$data['phone'],
		    				'email'=>$data['email'],
		    				'fax'=>$data['fax'],
		    				'map'=>$data['map'],
		    				'description'=>$data['description'],
		    		);
		    		$this->_name="mini_menu_contact";
		    		$this->insert($arr_menucontact);
		    	}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	public function CheckTitleAlias($alias){
		$db =$this->getAdapter();
		$sql = "SELECT c.`id` FROM $this->_name AS c WHERE c.`alias_menu`= '$alias'";
		return $db->fetchRow($sql);
	}
	function getMenuItemsById($id){
		$db= $this->getAdapter();
		$sql="SELECT *,
		(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` LIMIT 1 ) AS title,
		(SELECT mt.title FROM `mini_menu_type` AS mt WHERE mt.id = m.`menu_type_id` LIMIT 1) AS menu_type,
		(SELECT mc.address FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS address,
		(SELECT mc.tel FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS tel,
		(SELECT mc.email FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS email,
		(SELECT mc.map FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS map,
		(SELECT mc.description FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS description,
		(SELECT mc.fax FROM `mini_menu_contact` AS mc WHERE mc.menu_id = m.`id` LIMIT 1) AS fax
		 FROM `mini_menu` AS m WHERE m.id =".$id;
		return $db->fetchRow($sql);
	}
	function getMenuItemsTitleByLang($menu_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_menu_detail` AS md WHERE md.`menu_id`=$menu_id AND md.`languageId`=$lang";
		return $db->fetchRow($sql);
	}
	function deleteMenu($id){
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
	public function getMenuItemsajax($parent = 0, $spacing = '', $cate_tree_array = '',$menumanager=null){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		
		$sql="SELECT m.`id`,
			(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=1 LIMIT 1) AS name
			 FROM `mini_menu` AS m WHERE m.`status`=1 AND m.`parent` = $parent  ";
			if(!empty($menumanager['menu_manager'])){
				$sql.="AND m.menu_manager_id=".$menumanager['menu_manager'];
			}
			if(!empty($menumanager['current_id'])){
				$sql.=" AND m.id !=".$menumanager['current_id'];
			}
			$sql.=' ORDER BY id ASC';
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getMenuItemsajax($id=$row['id'], $spacing . ' - ', $cate_tree_array, $menumanager);
			}
		}
		return $cate_tree_array;
	}

}

