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
		(SELECT dt.title FROM `mini_document_type` AS dt WHERE dt.id =d.`document_type` LIMIT 1) AS doc_type_title,d.`create_date`,d.`status`
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
    	return $db->fetchAll($sql);
    	}
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
    function getAllDocumentType(){
    	$sql="SELECT id,title AS name FROM `mini_document_type` WHERE title!=''";
    	return  $this->getAdapter()->fetchAll($sql);
    }
}

