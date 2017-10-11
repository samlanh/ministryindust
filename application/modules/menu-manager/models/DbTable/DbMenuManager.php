<?php

class MenuManager_Model_DbTable_DbMenuManager extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_menu_manager';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllMainMenu($search){
    	$db = $this->getAdapter();
//     	$sql="mini_menu";
    	$sql="SELECT mm.`id`,mm.`title`,mm.`description`,
		(SELECT COUNT(m.id) FROM `mini_menu` AS m WHERE m.status>-1 AND m.menu_manager_id = mm.`id`) AS all_menu,
		(SELECT COUNT(m.id) FROM `mini_menu` AS m WHERE m.status=1 AND m.menu_manager_id = mm.`id`) AS all_menu_active,
		(SELECT COUNT(m.id) FROM `mini_menu` AS m WHERE m.status=0 AND m.menu_manager_id = mm.`id`) AS all_menu_deactive
		,mm.`status` FROM `mini_menu_manager` AS mm WHERE mm.`status` >-1";
    	$where='';
    	if($search['status_search']!=""){
    		$where.=" AND mm.`status`=".$search['status_search'];
    	}
    	return $db->fetchAll($sql.$where);
    }
    function addMainMenu($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    	$arr = array(
	    			'title'=>$data['title'],
					'description'=>$data['note'],
	    			'status'=>$data['status'],
	    			'create_date'=>date("Y-m-d"),
					'date_modify'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    			'position'=>1,
	    		);
	    	if (!empty($data['id'])){
	    		$where = " id =".$data['id'];
	    		$this->update($arr, $where);
	    	}else{
	    		$this->insert($arr);
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getMainMenuById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_menu_manager` WHERE id =".$id;
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
}

