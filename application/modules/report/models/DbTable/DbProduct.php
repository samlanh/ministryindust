<?php

class Report_Model_DbTable_DbProduct extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_product';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    } 
    function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
	function getALLProducts($search = null){		
		try{	
			$land = $this->getCurrentLang();
			$db = $this->getAdapter();
 			$from_date =(empty($search['start_date']))? '1': "create_date >= '".$search['start_date']." 00:00:00'";
 			$to_date = (empty($search['end_date']))? '1': "create_date <= '".$search['end_date']." 23:59:59'";
			$sql = "
			SELECT p.`id`,p.`pro_code`,
				(SELECT pd.title FROM `mini_product_detail` AS  pd WHERE pd.lang_id =2 AND pd.pro_id = p.`id` LIMIT 1) AS pro_title,
				(SELECT pd.title FROM `mini_product_detail` AS  pd WHERE pd.lang_id =1 AND pd.pro_id = p.`id` LIMIT 1) AS pro_titleen,
				(SELECT cd.title FROM `mini_category_detail` AS  cd WHERE cd.languageId =$land AND cd.category_id = p.`category_id` LIMIT 1) AS cate_title,
				p.`status`
				FROM `mini_product` AS p
				 WHERE p.`status`>-1 ";
			$where='';
			if(empty($search['show_all'])){
 				$where = " AND  ".$from_date." AND ".$to_date;
				if(!empty($search['adv_search'])){
					$s_where = array();
					$s_search = addslashes(trim($search['adv_search']));
					$s_where[] = " p.`pro_code` LIKE '%{$s_search}%'";
					$s_where[] = " (SELECT pd.title FROM `mini_product_detail` AS  pd WHERE pd.lang_id =1 AND pd.pro_id = p.`id` LIMIT 1) LIKE '%{$s_search}%'";
					$s_where[] = " (SELECT pd.title FROM `mini_product_detail` AS  pd WHERE pd.lang_id =2 AND pd.pro_id = p.`id` LIMIT 1) LIKE '%{$s_search}%'";
					$where .=' AND ('.implode(' OR ',$s_where).')';
				}
				if($search['category']>0){
					$condiction = $this->getCliCate($search['category']);
					if (!empty($condiction)){
						$where.=" AND p.`category_id` IN ($condiction)";
					}else{$where.=" AND p.`category_id`=".$search['category'];
					}
					//$where.= " AND p.category_id = ".$search['category'];
				}
				if($search['status']>-1){
					$where.= " AND p.status = ".$search['status'];
				}
			}
			$order=" ORDER BY 
			(SELECT cat.`id` FROM `mini_category` AS cat WHERE cat.`id`=p.`category_id` LIMIT 1),
			(SELECT cat.`parent` FROM `mini_category` AS cat WHERE cat.`id`=p.`category_id` LIMIT 1),
			p.`id` DESC ";
			return $db->fetchAll($sql.$where.$order);
		}catch (Exception $e){
			echo $e->getMessage();
		}	
	}
	function getCliCate($id,$idetity=null){
		$where='';
		$db = $this->getAdapter();
		$sql=" SELECT c.`id` FROM `mini_category` AS c WHERE c.`parent` = $id AND c.`status`=1 ";
		$child = $db->fetchAll($sql);
		foreach ($child as $va) {
			if (empty($idetity)){
				$idetity=$id.",".$va['id'];
			}else{$idetity=$idetity.",".$va['id'];
			}
			$idetity = $this->getCliCate($va['id'],$idetity);
		}
		return $idetity;
	}
}

