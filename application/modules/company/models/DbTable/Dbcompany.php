<?php

class Company_Model_DbTable_Dbcompany extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_company';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllCompany($data){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	
    	$sql="
    	SELECT c.id,c.com_code,c.com_name,c.com_phone,c.register_date,
			(SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`depart_id` AND cd.language_id=$lang LIMIT 1) AS department_name,
			(SELECT province_kh_name FROM mini_province WHERE  province_id=c.province_id LIMIT 1) as province_name,c.product,c.status,
			(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = c.user_id LIMIT 1) AS user_name
    	 	FROM `mini_company` AS c WHERE com_name!='' ";
    	
		if(!empty($data['txt_search'])){
			$s_where = array();
			$s_search = addslashes(trim($data['txt_search']));
			$s_where[] = " com_code LIKE '%{$s_search}%'";
			$s_where[] = " com_name LIKE '%{$s_search}%'";
			$s_where[] = " com_phone LIKE '%{$s_search}%'";
			$s_where[] = " product  LIKE '%{$s_search}%'";
			$s_where[] = " note LIKE '%{$s_search}%'";
			$s_where[] = " address LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($data['department']>0){
    		$sql.=" AND c.`depart_id`=".$data['department'];
    	}
    	if ($data['province_id']>0){
    		$sql.=" AND c.`province_id`=".$data['province_id'];
    	}
		
    	return $db->fetchAll($sql);
    }
    function addCompany($data){
    	
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbg = new Application_Model_DbTable_DbGlobal();
    		$part= PUBLIC_PATH.'/companylogo/';
    		$name = $_FILES['photo']['name'];
    		$photo='';
    		if (!empty($name)){
    			$tem =explode(".", $name);
    			$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
    			$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
    		}
	    	$arr = array(
	    			'com_code'=>$data['company_code'],
	    			'com_name'=>$data['company_name'],
					'com_phone'=>$data["phone"],
	    			'address'=>$data["address"],
	    			'register_date'=>$data['date_register'],
	    			'depart_id'=>$data['department'],
	    			'product'=>$data['product_name'],
	    			'province_id'=>$data["province_id"],
	    			'note'=>$data["note"],
	    			'logo'=>$photo,
	    			'create_date'=>date("Y-m-d H:i:s"),
	    			'modify_date'=>date("Y-m-d H:i:s"),
	    			'user_id'=>$this->getUserId(),
	    			'status'=>1,
	    			
	    		);
	    	$identity = $data['identity'];
	    	$ids = explode(',', $identity);
	    	$index=1;
	    	foreach ($ids as $i){
	    		if (!empty($_FILES['photo'.$i]['name'])){
	    			$ss = 	explode(".", $_FILES['photo'.$i]['name']);
	    			$new_image_name = date("Y").date("m").date("d").time().$i."file.".end($ss);
	    			$image_name = $dbg->resizeImase($_FILES['photo'.$i], $part,$new_image_name);
	    			
	    			if ($index<=8){
	    				$arr['file'.$index]=$image_name;
	    			}
	    			$index++;
	    		}else{
	    			$image_name = "";
	    		}
	    	}
	    	$this->insert($arr);
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function editCompany($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$dbg = new Application_Model_DbTable_DbGlobal();
		$part= PUBLIC_PATH.'/companylogo/';
    		$name = $_FILES['photo']['name'];
    		$photo='';
    		if (!empty($name)){
    			$tem =explode(".", $name);
    			$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
    			$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
    		}else{
    			$photo = $data['old_photo'];
    		}
	
			$arr = array(
					'com_code'=>$data['company_code'],
					'com_name'=>$data['company_name'],
					'com_phone'=>$data["phone"],
					'address'=>$data["address"],
					'register_date'=>$data['date_register'],
					'depart_id'=>$data['department'],
					'product'=>$data['product_name'],
					'province_id'=>$data["province_id"],
					'note'=>$data["note"],
					'logo'=>$photo,
					'user_id'=>$this->getUserId(),
					'status'=>$data["status"],
					'modify_date'=>date("Y-m-d H:i:s"),
	
			);
			$identity = $data['identity'];
			$ids = explode(',', $identity);
			$index=1;
			foreach ($ids as $i){
				$files='';
				if (!empty($_FILES['photo'.$i]['name'])){
	    			$ss = 	explode(".", $_FILES['photo'.$i]['name']);
	    			$new_image_name = date("Y").date("m").date("d").time().$i."file.".end($ss);
	    			$image_name = $dbg->resizeImase($_FILES['photo'.$i], $part,$new_image_name);
	    			
	    			if ($index<=8){
	    				$arr['file'.$index]=$image_name;
	    			}
	    			$index++;
				}else{
					if (!empty($data['old_photo'.$i])){
						$files = $data['old_photo'.$i];
					}
				}
				if ($index<=8){
					$arr['file'.$index]=$files;
				}
				$index++;
			}
			while($index <= 8){
				$arr['file'.$index]="";
				$index++;
			}
			$where = "id = ".$data['id'];
			$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getCompanyById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `mini_company` WHERE id =".$id;
		return $db->fetchRow($sql);
	}
// 	function getDepartmentTitleByLang($cate_id,$lang){
// 		$db = $this->getAdapter();
// 		$sql="SELECT cd.id,cd.`title`,cd.description,cd.`language_id` FROM `mini_department_detail` AS cd WHERE cd.`department_id`=$cate_id AND cd.`language_id`=$lang";
// 		return $db->fetchRow($sql);
// 	}
// 	public function CheckTitleAlias($alias){
// 		$db =$this->getAdapter();
// 		$sql = "SELECT c.`id` FROM `mini_department` AS c WHERE c.`alias_category`= '$alias'";
// 		return $db->fetchRow($sql);
// 	}
// 	function deleteCategory($id){
// 		$db = $this->getAdapter();
// 		$db->beginTransaction();
// 		try{
// 			$arr = array(
// 					'status'=>-1,
// 			);
// 				$where = " id =".$id;
// 				$this->update($arr, $where);
// 			$db->commit();
// 		}catch(exception $e){
// 			Application_Form_FrmMessage::message("Application Error");
// 			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
// 			$db->rollBack();
// 		}
// 	}
}

