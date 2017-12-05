<?php

class Document_Model_DbTable_Dbdocumenttype extends Zend_Db_Table_Abstract
{
    protected $_name = 'mini_document_type';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getAllDocumentType($data){
    	$db=$this->getAdapter();
    	$sql="SELECT id,title,title_en,
    			(SELECT title FROM `mini_document_type` WHERE parent_id=dt.id LIMIT 1) AS parrent_name,
    			create_date,
    			(SELECT first_name FROM `rms_users` WHERE id=user_id) AS user_name,status 
    			FROM `mini_document_type` AS dt 
    	WHERE dt.`status`>-1 AND dt.title!='' ";
    	 
    	if(!empty($data['txt_search'])){
	    	$s_where = array();
	    	$s_search = addslashes(trim($data['txt_search']));
	    	$s_where[] = " dt.title LIKE '%{$s_search}%'";
	    	$s_where[] = " dt.title_en LIKE '%{$s_search}%'";
	    	$sql.=' AND ('.implode(' OR ',$s_where).')';
    	}
//     	if ($data['document_type']>0){
//     		$sql.=" AND d.`document_type`=".$data['document_type'];
//     	}
//     	$sql.=" ORDER BY d.id DESC";
    	return $db->fetchAll($sql);
    }
    function getDocumentById($id){
    	$db = $this->getAdapter();
    	$sql="SELECT df.* FROM `mini_document_type` AS df WHERE df.`id`=$id LIMIT 1";
    	return $db->fetchRow($sql);
    }
    function addDocumenttype($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
	    	$arr = array(
	    			'title'=>$data['document_typekh'],
	    			'title_en'=>$data['document_typeen'],
					'parent_id'=>$data["document_type"],
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
	
	function addDocumenttypeAjax($data){
		$db = $this->getAdapter();
			$arr = array(
					'title'=>$data['document_typekh'],
					'title_en'=>$data['document_typeen'],
					'parent_id'=>$data["document_types"],
					'create_date'=>date("Y-m-d"),
					'user_id'=>$this->getUserId(),
					'status'=>$data['block_status'],
			);
			$id=$this->insert($arr);
			return $id;
	}
	
	function editDocumenttype($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
				$arr = array(
						'title'=>$data['document_typekh'],
						'title_en'=>$data['document_typeen'],
						'parent_id'=>$data["document_type"],
						'create_date'=>date("Y-m-d"),
						'user_id'=>$this->getUserId(),
						'status'=>$data['status'],
				);
			
			$where=" id=".$data['id'];
			$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			echo $e->getMessage();exit();
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

