<?php

class MenuManager_Model_DbTable_DbArticle extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_article';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    static function getCurrentLang(){
    	$session_lang=new Zend_Session_Namespace('lang');
    	return $session_lang->lang_id;
    }
    public function getAllArticle($search){
    	$db=$this->getAdapter();
    	$lang = $this->getCurrentLang();
    	$sql="SELECT
			act.`id`,
			(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = act.`category_id` AND languageId=$lang LIMIT 1) AS category,
			(SELECT ad.title FROM `mini_article_detail` AS ad WHERE ad.articleId = act.`id` AND language_id=$lang LIMIT 1) AS title,
			act.`status`
			 FROM `mini_article` AS act 
			 WHERE act.`status`>-1";
    	$where='';
    	if(!empty($search['adv_search'])){
			$s_where = array();
			$s_search = addslashes(trim($search['adv_search']));
			$s_where[] = " (SELECT ad.title FROM `mini_article_detail` AS ad WHERE ad.articleId = act.`id` AND language_id=2 LIMIT 1) LIKE '%{$s_search}%'";
			$s_where[] = " (SELECT ad.title FROM `mini_article_detail` AS ad WHERE ad.articleId = act.`id` AND language_id=1 LIMIT 1) LIKE '%{$s_search}%'";
			$where .=' AND ('.implode(' OR ',$s_where).')';
		}
    	if ($search['status_search']!=""){
    		$where.=" AND act.`status` =".$search['status_search'];
    	}
    	if ($search['category']>0){
    		$where.=" AND act.`category_id` =".$search['category'];
    	}
    	$order = "  ORDER BY act.`id` DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addArticle($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$dbg = new Application_Model_DbTable_DbGlobal();
			$part= PUBLIC_PATH.'/images/article/';
			$name = $_FILES['photo']['name'];
			$size = $_FILES['photo']['size'];
			$photo='';
			if (!empty($name)){
					$tem =explode(".", $name);
					$new_image_name = date("Y").date("m").date("d").time().".".end($tem);
					$photo = $dbg->resizeImase($_FILES['photo'], $part,$new_image_name);
					
			}else{
				$photo = "";
			}
			
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		if (!empty(trim($data['title_alias']))){
    			$alias = $data['title_alias'];
    		}else{
    			$alias = md5(date("Y-m-d H:i:s"));
    		}
	    	$arr = array(
	    			'category_id'=>$data['category'],
					'alias_article'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d H:i:s"),
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="mini_article";
	    	 if (!empty($data['id'])){
				 if (!empty($name)){
					 $arr['image_feature']= $photo;
				 }
	    		 	$where=" id=".$data['id'];
	    		 	$this->update($arr, $where);
	    		 	$article_id =$data['id'];
	    		 	
	    		 	if(!empty($lang)){
	    		 		$iddetail="";
	    		 		foreach($lang as $row){
	    		 			$title = str_replace(' ','',$row['title']);
	    		 			if (empty($iddetail)){
	    		 				$iddetail=$data['iddetail'.$title];
	    		 			}
	    		 			else{$iddetail=$iddetail.",".$data['iddetail'.$title];
	    		 			}
	    		 		}
	    		 		
	    		 		$this->_name="mini_article_detail";
	    		 		$where1=" articleId=".$data['id'];
	    		 		if (!empty($iddetail)){
	    		 			$where1.=" AND id NOT IN (".$iddetail.")";
	    		 		}
	    		 		$this->delete($where1);
	    		 		
		    		 	foreach($lang as $row){
		    		 		$title = str_replace(' ','',$row['title']);
		    		 		if (!empty($data['iddetail'.$title])){
		    		 			$arr_article = array(
		    		 					'articleId'=>$article_id,
		    		 					'title'=>$data['title'.$title],
		    		 					'description'=>$data['description'.$title],
		    		 					'language_id'=>$row['id'],
		    		 			);
		    		 			$this->_name="mini_article_detail";
		    		 			$wheredetail=" articleId=".$data['id']." AND id=".$data['iddetail'.$title];
			    				$this->update($arr_article,$wheredetail);
		    		 		}else{
			    		 		$arr_article = array(
			    		 				'articleId'=>$article_id,
			    		 				'title'=>$data['title'.$title],
			    		 				'description'=>$data['description'.$title],
			    		 				'language_id'=>$row['id'],
			    		 		);
			    		 		$this->_name="mini_article_detail";
			    		 		$this->insert($arr_article);
		    		 		}
		    		 	}
		    		 }
	    	}else{
	    			$this->_name="mini_article";
					$arr['image_feature']= $photo;
	    		 	$arr['create_date']= date("Y-m-d");
	    			$article_id = $this->insert($arr);
	    			
	    			if(!empty($lang)) foreach($lang as $row){
	    				$title = str_replace(' ','',$row['title']);
	    				$arr_article = array(
	    						'articleId'=>$article_id,
	    						'title'=>$data['title'.$title],
	    						'description'=>$data['description'.$title],
	    						'language_id'=>$row['id'],
	    				);
	    				$this->_name="mini_article_detail";
	    				$this->insert($arr_article);
	    			}
	    	}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getArticleById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM $this->_name WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getArticleTitleByLang($article_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT  acd.id,acd.`title`,acd.`sub_title`,acd.`language_id`,acd.description FROM `mini_article_detail` AS acd WHERE acd.`articleId`=$article_id AND acd.`language_id`=$lang";
		return $db->fetchRow($sql);
	}
	public function CheckTitleAlias($alias){
		$db =$this->getAdapter();
		$sql = "SELECT c.`id` FROM $this->_name AS c WHERE c.`alias_article`= '$alias'";
		return $db->fetchRow($sql);
	}
	function deleteArticle($id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr = array(
					'status'=>-1,
			);
				$where = " id =".$id;
				$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

