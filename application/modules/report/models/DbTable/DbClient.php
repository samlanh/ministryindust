<?php

class Report_Model_DbTable_DbClient extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_client';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    } 
    function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
	function getAllClients($search = null){		
		try{	
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT * FROM $this->_name AS c WHERE status > -1  ";
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
	function getAllReciept($search = null){
		try{
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "re.`paid_date` >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "re.`paid_date` <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT re.`id`,re.`reciept_no`,cl.`user_name`,re.`amount`,re.`total_paid`,re.`balance`,re.`paid_date`,re.`status`,
				(SELECT u.user_name FROM rms_users AS u WHERE u.id=re.user_id) AS user_mod
			FROM `mini_reciept`  AS re, `mini_client` AS cl WHERE re.`client_id`=cl.`id` ";
			if(empty($search['show_all'])){
				$where = " AND  ".$from_date." AND ".$to_date;
				if(!empty($search['adv_search'])){
					$s_where = array();
					$s_search = addslashes(trim($search['adv_search']));
					$s_where[] = " re.`reciept_no` LIKE '%{$s_search}%'";
					$s_where[] = " re.`amount` LIKE '%{$s_search}%'";
					$s_where[] = " re.`total_paid` LIKE '%{$s_search}%'";
					$s_where[] = " re.`balance` LIKE '%{$s_search}%'";
					$where .=' AND ('.implode(' OR ',$s_where).')';
				}
				if($search['client_id']>0){
					$where.= " AND  re.`client_id` = ".$search['client_id'];
				}
			}else{ $where='';
			}
			$order=" ORDER BY re.`id` DESC ";
			return $db->fetchAll($sql.$where.$order);
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	function getAllInovice($search = null){
		try{
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "i.`order_date` >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "i.`order_date` <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT i.`id`,i.`invoice_no`,(SELECT c.user_name FROM `mini_client` AS c WHERE c.id = i.`client_id` LIMIT 1) AS client_name
			,i.`amount`,i.`balance_after`,i.paid,i.`is_paid`,i.`order_date`,(SELECT v.name_en FROM `ln_view` AS v WHERE v.key_code = i.`delivery_status` AND v.type=10) AS delivery_status,i.status,
			(SELECT u.user_name FROM rms_users AS u WHERE u.id=i.user_id) AS user_mod
			 FROM `mini_invoice` AS i WHERE i.status=1  ";
			if(empty($search['show_all'])){
				$where = " AND  ".$from_date." AND ".$to_date;
				if(!empty($search['adv_search'])){
					$s_where = array();
					$s_search = addslashes(trim($search['adv_search']));
					$s_where[] = " i.`invoice_no` LIKE '%{$s_search}%'";
					$s_where[] = " i.`amount` LIKE '%{$s_search}%'";
					$s_where[] = " i.`balance_after` LIKE '%{$s_search}%'";
					$where .=' AND ('.implode(' OR ',$s_where).')';
				}
				if($search['client_id']>0){
					$where.= " AND  i.`client_id` = ".$search['client_id'];
				}
				if($search['delivery_info']>0){
					$where.= " AND  i.`delivery_status` = ".$search['delivery_info'];
				}
				if($search['is_paid']!=-1){
					$where.= " AND  i.`is_paid` = ".$search['is_paid'];
				}
			}else{ $where='';
			}
			$order=" ORDER BY i.`id` DESC ";
			return $db->fetchAll($sql.$where.$order);
		}catch (Exception $e){
			echo $e->getMessage();
		}
	}
	function getRecieptByID($id){
		$db = $this->getAdapter();
		$sql="SELECT *,
			(SELECT c.customer_code FROM mini_client as c WHERE v.client_id=c.id ) as customer_code,
			(SELECT c.user_name FROM mini_client as c WHERE v.client_id=c.id ) as customer_name,
			(SELECT c.email FROM mini_client as c WHERE v.client_id=c.id ) as customer_email
		FROM `mini_reciept` AS v WHERE v.`id`='".$id."' LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getRecieptDetail($id){
		$db = $this->getAdapter();
		$sql="SELECT rcd.*,
		(SELECT i.invoice_no FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS inovoice_no,
		(SELECT i.amount FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS amount_old,
		(SELECT i.balance FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS balance_old
		 FROM `mini_reciept_detail` AS rcd WHERE rcd.`reciept_id`=$id";
		return $db->fetchAll($sql);
	}
}

