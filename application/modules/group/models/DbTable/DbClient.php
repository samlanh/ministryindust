<?php

class Group_Model_DbTable_DbClient extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_client';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    } 
    function getClientId(){
    	$db= $this->getAdapter();
    	$sql="SELECT c.`id` FROM `mini_client` AS c ORDER BY c.`id` DESC LIMIT 1";
    	return $db->fetchOne($sql);
    }
	public function addClient($_data){
		try{
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    		$part= PUBLIC_PATH.'/images/directory/';
    		$name = $_FILES['profile']['name'];
    		$size = $_FILES['profile']['size'];
    		$photo="";
    		if (!empty($name)){
	    		list($txt, $ext) = explode(".", $name);
	    		if(in_array($ext,$valid_formats)) {
	    				$image_name = time().($this->getClientId()+1).".".$ext;
	    				$tmp = $_FILES['profile']['tmp_name'];
	    				if(move_uploaded_file($tmp, $part.$image_name)){
	    					$photo = $image_name;
	    				}
	    				else
	    					$string = "Image Upload failed";
	    		}
    		}
    		$dbgb = new Application_Model_DbTable_DbVdGlobal();
    		$cus_code = $dbgb->getCustomerCode();
		    $_arr=array(
				'user_name'	  => $_data['user_name'],
				'email'	      => $_data['email'],
		    	'phone'      => $_data['phone'],
	    		'company_name'      => $_data['company_name'],
		    	'address'=> $_data['address'],
				'website'  =>$_data['website'],
	    		'status'=>$_data['status'],
		    	'create_date'      => date("Y-m-d"),
		    	'modify_date'      => date("Y-m-d"),
		    	'package_id'      => 1,
	    		'is_verify_acc'=>1,
		    	'registerby'=>1,
		);
		if(!empty($_data['id'])){
			if (!empty($name)){
				$_arr['photo']= $photo;
			}
			if (!empty($_data['check_change'])){
				$_arr['password']= md5($_data['password']);
			}
			$where = 'id = '.$_data['id'];
			$this->update($_arr, $where);
			return $_data['id'];
			 
		}else{
			$_arr['customer_code']= $cus_code;
			$_arr['password']= md5($_data['password']);
			$_arr['photo']= $photo;
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getClientById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllClients($search = null){		
		try{	
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT c.`id`,c.`customer_code`,c.`user_name`,c.`email`,c.`phone`,c.`address`,c.`status` FROM `mini_client` AS c WHERE STATUS > -1  ";
			if(empty($search['show_all'])){
				$where = " AND  ".$from_date." AND ".$to_date;
				if(!empty($search['adv_search'])){
					$s_where = array();
					$s_search = addslashes(trim($search['adv_search']));
					$s_where[] = " c.`user_name` LIKE '%{$s_search}%'";
					$s_where[] = " c.`email` LIKE '%{$s_search}%'";
					$s_where[] = " c.`phone` LIKE '%{$s_search}%'";
					$where .=' AND ('.implode(' OR ',$s_where).')';
				}
				if($search['status']>-1){
					$where.= " AND status = ".$search['status'];
				}
			}else{ $where='';}
			$order=" ORDER BY c.`id` DESC ";
			return $db->fetchAll($sql.$where.$order);
		}catch (Exception $e){
			echo $e->getMessage();
		}	
	}
	
	function deleteClient($id){
		$db = $this->getAdapter();
		$arr = array( 'status'=> -1);
		$where = ' id = '.$id;
		$this->_name = "mini_client";
		$this->update($arr, $where);
	}
	
	function addClientAjax($_data){
		$_arr=array(
				'user_name'	  => $_data['customer_name'],
				'email'	      => $_data['email'],
				'password'      => md5($_data['password']),
				'status'=>1,
				'is_verify_acc'=>1,
				'create_date'      => date("Y-m-d"),
				'modify_date'      => date("Y-m-d"),
		);
		$this->_name = "mini_client";
		return  $this->insert($_arr);
	}
	
	
	
	
	
	
	
	
}

