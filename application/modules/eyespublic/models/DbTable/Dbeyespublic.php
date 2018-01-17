<?php

class Eyespublic_Model_DbTable_Dbeyespublic extends Zend_Db_Table_Abstract
{
    protected $_name = 'mini_eyespublic';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	$lang = $session_lang->lang_id;
    	if(empty($lang)){
    		$session_lang->lang_id=2;
    		return 2;
    	}else{return $lang;
    	}
    }
    public function getAllDocument($data){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	$sql="SELECT d.`id`,d.`title`,d.file_size,d.description,d.key_word,
		d.`create_date`,d.`status`,
		(SELECT u.first_name FROM `rms_users` AS u WHERE u.id = d.`user_id` LIMIT 1) AS user_name
		 FROM `mini_eyespublic` AS d WHERE d.`status`>-1 AND title!='' ";    	 
    	if(!empty($data['txt_search'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($data['txt_search']));
	    	$s_where[] = " title LIKE '%{$s_search}%'";
	    	$s_where[] = " description LIKE '%{$s_search}%'";
	    	$s_where[] = " title LIKE '%{$s_search}%'";$sql.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$sql.=" ORDER BY d.id DESC";
    	return $db->fetchAll($sql);
    }
    function getDocumentById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT df.* FROM `mini_eyespublic` AS df WHERE df.`id`=$id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function addDocument($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbg = new Application_Model_DbTable_DbGlobal();
//     		$part= PUBLIC_PATH.'/file/image_feature/';
//     		if (!file_exists($part)) {
//     			mkdir($part, 0777, true);
//     		}
//     		$photoname = str_replace(" ", "_", $data['document_name']);
//     		$name = $_FILES['photo']['name'];
//     		$size = $_FILES['photo']['size'];
//     		$photo='';
//     		if (!empty($name)){
//     			$tem =explode(".", $name);
// 					$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
// 					$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
    				
//     		}    		
    		
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
	    			'file_name'=>$document,
	    			'file_size'=>$filesize,
	    			'create_date'=>date("Y-m-d"),
	    			'user_id'=>$this->getUserId(),
	    			'status'=>1,
	    		);
	    	$this->insert($arr);
	    	$db->commit();
    	}catch(exception $e){
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
// 					'document_type'=>$data["document_type"],
					'file_name'=>$document,
					'file_size'=>$filesize,
					'modify_date'=>$data['date_register'],
					'user_id'=>$this->getUserId(),
					'status'=>1,
			);
			$where=" id=".$data['id'];
			$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
//     function getAllDocumentType(){
//     	$sql="SELECT id,title AS name FROM `mini_document_type` WHERE title!=''";
//     	return  $this->getAdapter()->fetchAll($sql);
//     }
}

