<?php

class Document_Model_DbTable_Dbdocument extends Zend_Db_Table_Abstract
{
    protected $_name = 'mini_documentfile';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getAllDocument($data){
    	$db=$this->getAdapter();
    	 
    	$sql="
    	SELECT d.`id`,d.`title`,
		(SELECT dt.title FROM `mini_document_type` AS dt WHERE dt.id =d.`document_type` LIMIT 1) AS doc_type_title,d.`modify_date`,d.`status`,
		(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = d.`user_id` LIMIT 1) AS user_name
		 FROM `mini_documentfile` AS d WHERE d.`status`>-1 AND title!='' ";
    	 
    	if(!empty($data['txt_search'])){
    	$s_where = array();
    	$s_search = addslashes(trim($data['txt_search']));
    	$s_where[] = " title LIKE '%{$s_search}%'";
    	$s_where[] = " (SELECT dt.title FROM `mini_document_type` AS dt WHERE dt.id =d.`document_type` LIMIT 1) LIKE '%{$s_search}%'";
    	$sql.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if ($data['document_type']>0){
    	$sql.=" AND d.`document_type`=".$data['document_type'];
    	}
    	$sql.=" ORDER BY d.id DESC";
    	return $db->fetchAll($sql);
    }
    function getDocumentById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT df.* FROM `mini_documentfile` AS df WHERE df.`id`=$id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function addDocument($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbg = new Application_Model_DbTable_DbGlobal();
    		
    		$part= PUBLIC_PATH.'/file/image_feature/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
    		$photoname = str_replace(" ", "_", $data['document_name']);
    		$name = $_FILES['photo']['name'];
    		$size = $_FILES['photo']['size'];
    		$photo='';
    		if (!empty($name)){
    			$tem =explode(".", $name);
					$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
					$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
    				
    		}
    		
    		$document='';
    		$partfile= PUBLIC_PATH.'/file/';
    		$filename = $_FILES['document']['name'];
    		$filesize = $_FILES['document']['size'];
    		if (!empty($filename)){
    			$temfile =explode(".", $filename);
    			$file_name = date("Y").date("m").date("d").time().".".end($temfile);
    			$tmp = $_FILES['document']['tmp_name'];
    		
    			if(move_uploaded_file($tmp, $partfile.$file_name)){
    				$document = $file_name;
    			}
    			else
    				$string = "Document Upload failed";
    		}
    		
	    	$arr = array(
	    			'title'=>$data['document_name'],
	    			'key_word'=>$data['key_word'],
					'document_type'=>$data["document_type"],
	    			'image'=>$photo,
	    			'file_name'=>$document,
	    			'file_size'=>$filesize,
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
	function editDocument($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$dbg = new Application_Model_DbTable_DbGlobal();
			$part= PUBLIC_PATH.'/file/image_feature/';
    		if (!file_exists($part)) {
    			mkdir($part, 0777, true);
    		}
			$photoname = str_replace(" ", "_", $data['document_name']);
			$name = $_FILES['photo']['name'];
			$size = $_FILES['photo']['size'];
			$photo='';
			if (!empty($name)){
				$tem =explode(".", $name);
				$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
				$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
		
			}else{
				$photo = $data['old_photo'];
			}
			$document='';
			$partfile= PUBLIC_PATH.'/file/';
			$filename = $_FILES['document']['name'];
			$filesize = $_FILES['document']['size'];
			if (!empty($filename)){
				$temfile =explode(".", $filename);
				$file_name = date("Y").date("m").date("d").time().".".end($temfile);
				$tmp = $_FILES['document']['tmp_name'];
				
				if(move_uploaded_file($tmp, $partfile.$file_name)){
					$document = $file_name;
				}
				else
					$string = "Document Upload failed";
			}else{
				$document = $data['old_document'];
			}
			
			$arr = array(
					'title'=>$data['document_name'],
					'key_word'=>$data['key_word'],
					'document_type'=>$data["document_type"],
					'image'=>$photo,
					'file_name'=>$document,
					'file_size'=>$filesize,
// 					'create_date'=>date("Y-m-d"),
					'modify_date'=>$data['date_register'],
					'user_id'=>$this->getUserId(),
					'status'=>1,
			);
			$where=" id=".$data['id'];
			$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
// 			echo $e->getMessage();exit();
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
    function getAllDocumentType(){
    	$sql="SELECT id,title AS name FROM `mini_document_type` WHERE title!=''";
    	return  $this->getAdapter()->fetchAll($sql);
    }
}

