<?php

class Group_Model_DbTable_DbInvoice extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_invoice';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    } 
    function getClientId(){
    	$db= $this->getAdapter();
    	$sql="SELECT c.`id` FROM `mini_client` AS c ORDER BY c.`id` DESC LIMIT 1";
    	return $db->fetchOne($sql);
    }
	public function addInvoice($_data){
		try{
		    $_arr=array(
				'invoice_no'	  => $_data['invoice_no'],
				'client_id'	      => $_data['client_id'],
		    	'order_date'      => $_data['order_date'],
	    		'delivery_status'      => $_data['delivery_info'],
		    	'amount'=> $_data['amount'],
				'balance'  =>$_data['balance'],
	    		'note'=>$_data['note'],
		    	'modify_date'      => date("Y-m-d"),
		    	'status'=>$_data['status'],
	    		'user_id'=>$this->getUserId(),
		);
		if(!empty($_data['id'])){
			$where = 'id = '.$_data['id'];
			$this->update($_arr, $where);
			return $_data['id'];
			 
		}else{
			$_arr['amount_after']= $_data['amount'];
			$_arr['balance_after'] = $_data['balance'];
			$_arr['date_create']=date("Y-m-d");
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function getInvoiceByID($id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_invoice` AS v WHERE v.`id`=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAllInovice($search = null){
		try{
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "i.`order_date` >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "i.`order_date` <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT i.`id`,i.`invoice_no`,(SELECT c.user_name FROM `mini_client` AS c WHERE c.id = i.`client_id` LIMIT 1) AS client_name
			,i.`amount`,i.`balance`,i.`note`,i.`order_date`,(SELECT v.name_en FROM `ln_view` AS v WHERE v.key_code = i.`delivery_status` AND v.type=10) AS delivery_status,i.status
			 FROM `mini_invoice` AS i WHERE 1  ";
			if(empty($search['show_all'])){
				$where = " AND  ".$from_date." AND ".$to_date;
				if(!empty($search['adv_search'])){
					$s_where = array();
					$s_search = addslashes(trim($search['adv_search']));
					$s_where[] = " i.`invoice_no` LIKE '%{$s_search}%'";
					$s_where[] = " i.`amount` LIKE '%{$s_search}%'";
					$s_where[] = " i.`balance` LIKE '%{$s_search}%'";
					$where .=' AND ('.implode(' OR ',$s_where).')';
				}
				if($search['client_id']>0){
					$where.= " AND  i.`client_id` = ".$search['client_id'];
				}
			}else{ $where='';
			}
			$order=" ORDER BY i.`id` DESC ";
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
	
	
	
	
	
	
	
	
	
	
}

