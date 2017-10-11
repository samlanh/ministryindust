<?php

class Document_Model_DbTable_Dbdocument extends Zend_Db_Table_Abstract
{
    protected $_name = 'mini_documentfile';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
//     static function getCurrentLang(){
//     	$session_lang=new Zend_Session_Namespace('lang');
//     	return $session_lang->lang_id;
//     }
//     public function getAllCompany($data){
//     	$db=$this->getAdapter();
//     	$lang = $this->getCurrentLang();
    	
//     	$sql="
//     	SELECT c.*,
// 			(SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`depart_id` AND cd.language_id=$lang LIMIT 1) AS department_name,
// 			(SELECT province_kh_name FROM mini_province WHERE  province_id=c.province_id LIMIT 1) as province_name
//     	 	FROM `mini_company` AS c WHERE com_name!='' ";
    	
// 		if(!empty($data['txt_search'])){
// 			$s_where = array();
// 			$s_search = addslashes(trim($data['txt_search']));
// 			$s_where[] = " com_code LIKE '%{$s_search}%'";
// 			$s_where[] = " com_name LIKE '%{$s_search}%'";
// 			$s_where[] = " com_phone LIKE '%{$s_search}%'";
// 			$s_where[] = " product  LIKE '%{$s_search}%'";
// 			$s_where[] = " note LIKE '%{$s_search}%'";
// 			$s_where[] = " address LIKE '%{$s_search}%'";
// 			$sql.=' AND ('.implode(' OR ',$s_where).')';
// 		}
//     	if ($data['department']>0){
//     		$sql.=" AND c.`depart_id`=".$data['department'];
//     	}
//     	if ($data['province_id']>0){
//     		$sql.=" AND c.`province_id`=".$data['province_id'];
//     	}
		
//     	return $db->fetchAll($sql);
//     }
    function addDocument($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$photoname = str_replace(" ", "_", $data['document_name']) . '.jpg';
    		$upload = new Zend_File_Transfer();
    		$upload->addFilter('Rename',
    				array('target' => PUBLIC_PATH . '/companylogo/'. $photoname, 'overwrite' => true) ,'photo');
    		$receive = $upload->receive();
    		
    		if($receive)
    		{
    			$data['photo'] = $photoname;
    		}
    		else{
    			$data['photo']="";
    		}
    		
    		$files = $upload->getFileInfo();
    		$file_size=0;
    		if(!empty($files['document']['size'])){
    			$file_size=$files['document']['size'];
    		}
	    	$arr = array(
	    			'title'=>$data['document_name'],
	    			'key_word'=>$data['key_word'],
					'document_type'=>$data["document_type"],
	    			'image'=>$data["photo"],
	    			'file_name'=>$data['photo'],
	    			'file_size'=>$file_size,
	    			'create_date'=>date("Y-m-d"),
	    			'modify_date'=>$data['date_register'],
	    			'user_id'=>$this->getUserId(),
	    			'status'=>1,
	    		);
	    	$this->insert($arr);
	    	$db->commit();
    	}catch(exception $e){
    		echo $e->getMessage();exit();
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
// 	function getCompanyById($id){
// 		$db= $this->getAdapter();
// 		$sql="SELECT * FROM `mini_company` WHERE id =".$id;
// 		return $db->fetchRow($sql);
// 	}
/* for document type*/
    function getAllDocumentType(){
    	$sql="SELECT id,title AS name FROM `mini_document_type` WHERE title!=''";
    	return  $this->getAdapter()->fetchAll($sql);
    }
}

