<?php

class Other_Model_DbTable_DbPartner extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_website_setting';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    
    }
    public function getAllPartner($search=null){
    	$db=$this->getAdapter();
    	$sql="SELECT p.`id`,p.`title`,p.image,p.`status`,
			(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = p.`user_id` LIMIT 1) AS user_name
			 FROM `mini_partner` AS p WHERE 1";
    	if(!empty($search['adv_search'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($search['adv_search']));
	    	$s_where[] = " p.`title` LIKE '%{$s_search}%'";
	    	$sql.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if ($search['status_search']!=""){
    		$sql.=" AND p.`status`=".$search['status_search'];
    	}
    	$sql.=" ORDER BY id DESC";
    	return $db->fetchAll($sql);
    }
}

