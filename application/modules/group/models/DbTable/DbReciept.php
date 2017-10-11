<?php

class Group_Model_DbTable_DbReciept extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_reciept';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    } 
    function getClientId(){
    	$db= $this->getAdapter();
    	$sql="SELECT c.`id` FROM `mini_client` AS c ORDER BY c.`id` DESC LIMIT 1";
    	return $db->fetchOne($sql);
    }
	public function addReciept($_data){
		try{
			$db_vdG = new Application_Model_DbTable_DbVdGlobal();
			$recieptno = $db_vdG->getRecieptNo();
		    $_arr=array(
				'reciept_no'	  => $recieptno,
				'client_id'	      => $_data['client_id'],
	    		'amount'      => $_data['paid_amount'],
		    	'total_paid'      => $_data['total_paid'],
	    		'balance'      => $_data['balance'],
		    	'create_date'=> date("Y-m-d"),
				'modify_date'  =>date("Y-m-d"),
	    		'note'=>$_data['note'],
		    	'paid_date'      => $_data['reciept_date'],
		    	'status'=>$_data['status'],
	    		'user_id'=>$this->getUserId(),
		);
			$id = $this->insert($_arr);
			
			$identity = explode(',', $_data['identity']);
			
			$paid = $_data['total_paid'];
			$compelted = 0;
			foreach ($identity as $key => $i){
				$paid = $paid -($_data['balance_after'.$identity[$key]]);
    			$recipt_paid = 0;
    			if ($paid>=0){
    				$paided = $_data['balance_after'.$identity[$key]];
    				$balance=0;
    				$compelted=1;
    			}else{
    				$sds =abs($paid);
    				$paided = $_data['balance_after'.$identity[$key]]-$sds;
    				$balance= $sds;
    				$compelted=0;
    			}
    			$data_item= array(
    					'reciept_id' =>  $id,
    					'inovoice_id' =>  $_data['invoice_id'.$identity[$key]],
    					'total'      =>  $_data['balance_after'.$identity[$key]],
    					'paid'      =>  $paided,
    					'balance'  =>  $balance,
    					'is_completed'=>    $compelted,
//     					'status'      => 1,
//     					'date_input'  => date("Y-m-d"),
    			);
    			$this->_name='mini_reciept_detail';
    			$this->insert($data_item);
    			
    			$rsinvoice = $this->getInvoiceByID($_data['invoice_id'.$identity[$key]]);
    			if(!empty($rsinvoice)){
    				$data_invoice = array(
    						'paid'=>$rsinvoice['paid']+$paided,
//     						'discount_after'  =>  0,
    						'amount_after'   =>  $_data['balance_after'.$identity[$key]]-$paided,
    						'balance_after'   =>  $balance,
//     						'net_totalafter'  =>    $balance,
//     						'is_fullpaid'   =>  1,
    						'is_paid'   =>  $compelted,
    				);
    				$this->_name='mini_invoice';
    				$where = ' id = '.$_data['invoice_id'.$identity[$key]];
    				//print_r($where);exit();
    				$this->update($data_invoice, $where);
    			}
    			
    			if($paid<=0){
    				break;
    			}
			}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function updateReciept($_data){//proccess
		try{
			$db_vdG = new Application_Model_DbTable_DbVdGlobal();
			$recieptno = $db_vdG->getRecieptNo();
			$_arr=array(
// 					'reciept_no'	  => $recieptno,
					'client_id'	      => $_data['client_id'],
					'amount'      => $_data['paid_amount'],
					'total_paid'      => $_data['total_paid'],
					'balance'      => $_data['balance'],
// 					'create_date'=> date("Y-m-d"),
					'modify_date'  =>date("Y-m-d"),
					'note'=>$_data['note'],
					'paid_date'      => $_data['reciept_date'],
					'status'=>$_data['status'],
					'user_id'=>$this->getUserId(),
			);
			$where=" id=".$_data['id'];
			$this->update($_arr, $where);
			$id = $_data['id'];
			
			$reciept_detail = $this->getRecieptDetail($_data['id']);
			
			if (!empty($reciept_detail)) foreach ($reciept_detail as $ss){
				$invoice = $this->getInvoiceByID($ss['inovoice_id']);
				$data_invoice = array(
						'paid'=>$invoice['paid']-$ss['paid'],
						'amount_after'   =>  $invoice['amount_after']+$ss['paid'],
						'balance_after'   =>  $invoice['balance_after']+$ss['paid'],
						'is_paid'   =>  0,
				);
				$this->_name='mini_invoice';
				$this->update($data_invoice, ' id = '.$ss['inovoice_id']);
			}
			
			$this->_name='mini_reciept_detail';
			$this->delete(" reciept_id =".$_data['id']);
			
			$identity = explode(',', $_data['identity']);
				
			$paid = $_data['total_paid'];
			$compelted = 0;
			foreach ($identity as $key => $i){
				$paid = $paid -($_data['balance_after'.$identity[$key]]);
				$recipt_paid = 0;
				if ($paid>=0){
					$paided = $_data['balance_after'.$identity[$key]];
					$balance=0;
					$compelted=1;
				}else{
					$sds =abs($paid);
					$paided = $_data['balance_after'.$identity[$key]]-$sds;
					$balance= $sds;
					$compelted=0;
				}
				$data_item= array(
						'reciept_id' =>  $id,
						'inovoice_id' =>  $_data['invoice_id'.$identity[$key]],
						'total'      =>  $_data['balance_after'.$identity[$key]],
						'paid'      =>  $paided,
						'balance'  =>  $balance,
						'is_completed'=>    $compelted,
						//     					'status'      => 1,
				//     					'date_input'  => date("Y-m-d"),
				);
				$this->_name='mini_reciept_detail';
				$this->insert($data_item);
				 
				$rsinvoice = $this->getInvoiceByID($_data['invoice_id'.$identity[$key]]);
				
				if(!empty($rsinvoice)){
					$data_invoice = array(
							'paid'=>$rsinvoice['paid']+$paided,
							//     						'discount_after'  =>  0,
							'amount_after'   =>  $_data['balance_after'.$identity[$key]]-$paided,
							'balance_after'   =>  $balance,
							//     						'net_totalafter'  =>    $balance,
					//     						'is_fullpaid'   =>  1,
							'is_paid'   =>  $compelted,
					);
					$this->_name='mini_invoice';
					$where = ' id = '.$_data['invoice_id'.$identity[$key]];
					$this->update($data_invoice, $where);
				}
				
				if($paid<=0){
					break;
				}
				
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
	function getRecieptByID($id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_reciept` AS v WHERE v.`id`=$id LIMIT 1";
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
	function getAllReciept($search = null){
		try{
			$db = $this->getAdapter();
			$from_date =(empty($search['start_date']))? '1': "re.`paid_date` >= '".$search['start_date']." 00:00:00'";
			$to_date = (empty($search['end_date']))? '1': "re.`paid_date` <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT re.`id`,re.`reciept_no`,cl.`user_name`,re.`amount`,re.`total_paid`,re.`balance`,re.`paid_date`,re.`status` 
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
	function getInvoiceBYClient($client_id){// user for clear invoice in reciept controller
		try{	
			$db= $this->getAdapter();
			$sql="SELECT * FROM `mini_invoice` AS i WHERE i.`client_id`=$client_id AND i.`delivery_status`=2 AND i.is_paid =0 ORDER BY i.id ASC";
			 $row = $db->fetchAll($sql);
			 $row_data='';
			 $i=0;
			 $total=0;
			 $total_balance=0;
			 $identity='';
			 if (!empty($row)) foreach ($row as $rs){ $i++;
			 	$total = $total+$rs['amount_after'];
			 	$total_balance = $total_balance+$rs['balance_after'];
			 	$row_data.='<tr id="row'.$i.'" style="background: #fff;border: solid 1px #eee;text-align: center;">';
			 		$row_data.='<td align="center">&nbsp;<input type="checkbox" class="checkbox" id="mfdid_'.$rs['id'].'" value="'.$rs['id'].'" OnChange="calculateTotal('.$rs['id'].')" checked="checked" name="selector[]"/></td>';
			 		$row_data.='<td class="border-td"><label>'.$i.'</label></td>';
			 		$row_data.='<td class="border-td"><label>'.date("d-M-Y",strtotime($rs['order_date'])).'</label></td>';
			 		$row_data.='<td class="border-td"><label>'.$rs['invoice_no'].'</label><input type="hidden" dojoType="dijit.form.TextBox" id="invoice_id'.$rs['id'].'" name="invoice_id'.$rs['id'].'" value="'.$rs['id'].'"></td>';
			 		$row_data.='<td class="border-td"><label>'.$rs['amount_after'].'</label><input type="hidden" dojoType="dijit.form.TextBox" id="amount'.$rs['id'].'" name="amount'.$rs['id'].'" value="'.$rs['amount_after'].'"></td>';
			 		$row_data.='<td class="border-td"><label>'.$rs['balance_after'].'</label><input type="hidden" dojoType="dijit.form.TextBox" id="balance_after'.$rs['id'].'" name="balance_after'.$rs['id'].'" value="'.$rs['balance_after'].'"></td>';
		 		$row_data.='</tr>';
		 		if (!empty($identity)){
		 			$identity= $identity.",".$rs['id'];
		 		}else{ $identity=$rs['id'];
		 		}
			 }
			 $result = array('table'=>$row_data,
			 		'total'=>$total,
			 		'total_balance'=>$total_balance,
			 		'identity'=>$identity);
			 return $result;
		}catch (Exception $e){
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function getRecieptDetailById($invoice_id,$reciept_id){
		$db= $this->getAdapter();
		$sql="SELECT rcd.*,
		(SELECT i.invoice_no FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS inovoice_no,
		(SELECT i.amount FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS amount_old,
		(SELECT i.balance FROM `mini_invoice` AS i WHERE i.id = rcd.`inovoice_id` LIMIT 1)AS balance_old
		FROM `mini_reciept_detail` AS rcd WHERE rcd.inovoice_id=$invoice_id and rcd.`reciept_id`=$reciept_id limit 1";
		return $db->fetchRow($sql);
	}
	function getInvoiceBYClientForedit($client_id,$reciept_id){// user for clear invoice in reciept controller
		try{
			$reciept_detail = $this->getRecieptDetail($reciept_id);
			$identityss = "";
			$array = array();
			if (!empty($reciept_detail)){
				foreach ($reciept_detail as $rs){
					$newarray = array($rs['inovoice_id']=>$rs['inovoice_id']);
					$array = array_merge($array,$newarray);
					if (empty($identityss)){$identityss=$rs['inovoice_id'];}else{$identityss=$identityss.",".$rs['inovoice_id'];}
				}
			}
			$db= $this->getAdapter();
			$sql="SELECT * FROM `mini_invoice` AS i WHERE i.`client_id`=$client_id AND i.`delivery_status`=2 AND i.is_paid =0 ";
			if (!empty($identityss)){
				$sql.=" OR i.id IN($identityss)";
			}
			$sql.="ORDER BY i.id ASC";
			$row = $db->fetchAll($sql);
			$row_data='';
			$i=0;
			$total=0;
			$total_balance=0;
			$identity='';
			$oldidentity='';
			if (!empty($row)) foreach ($row as $rs){
				$i++;
				$checked='';
				$amount=$rs['amount_after'];
				$balance=$rs['balance_after'];
				if(in_array($rs['id'],$array)) {
					$checked = 'checked="checked" ';
					
					$total_balance = $total_balance+$rs['balance_after'];
					$reciept = $this->getRecieptDetailById($rs['id'], $reciept_id);
					$amount = $rs['amount_after']+$reciept['paid'];
					$balance=$rs['balance_after']+$reciept['paid'];
					$total = $total+$amount;
					
					if (!empty($identity)){
						$identity= $identity.",".$rs['id'];
					}else{ $identity=$rs['id'];
					}
				}
				$row_data.='<tr id="row'.$i.'" style="background: #fff;border: solid 1px #eee;text-align: center;">';
				$row_data.='<td align="center">&nbsp;<input type="checkbox" '.$checked.' class="checkbox" id="mfdid_'.$rs['id'].'" value="'.$rs['id'].'" OnChange="calculateTotal('.$rs['id'].')" name="selector[]"/></td>';
				$row_data.='<td class="border-td"><label>'.$i.'</label></td>';
				$row_data.='<td class="border-td"><label>'.date("d-M-Y",strtotime($rs['order_date'])).'</label></td>';
				$row_data.='<td class="border-td"><label>'.$rs['invoice_no'].'</label><input type="hidden" dojoType="dijit.form.TextBox" id="invoice_id'.$rs['id'].'" name="invoice_id'.$rs['id'].'" value="'.$rs['id'].'"></td>';
				$row_data.='<td class="border-td"><label>'.$amount.'</label><input type="hidden" dojoType="dijit.form.TextBox" id="amount'.$rs['id'].'" name="amount'.$rs['id'].'" value="'.$amount.'"></td>';
				$row_data.='<td class="border-td"><label>'.$balance.'</label><input type="hidden" dojoType="dijit.form.TextBox" id="balance_after'.$rs['id'].'" name="balance_after'.$rs['id'].'" value="'.$balance.'"></td>';
				$row_data.='</tr>';
				if (!empty($oldidentity)){
					$oldidentity= $oldidentity.",".$rs['id'];
				}else{ $oldidentity=$rs['id'];
				}
			}
			$result = array('table'=>$row_data,
					'total'=>$total,
					'total_balance'=>$total_balance,
					'identity'=>$identity,
					'oldidentity'=>$oldidentity);
			return $result;
		}catch (Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
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

