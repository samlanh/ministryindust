<?php

class Application_Model_DbTable_DbVdGlobal extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public function getLaguage(){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_language` AS l WHERE l.`status`=1";
		return $db->fetchAll($sql);
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		$lang = $session_lang->lang_id;
		if(empty($lang)){
			$session_lang->lang_id=2;
			return 2;
		}else{return $lang;}
	}
	public function getMenuManager(){
		$db = $this->getAdapter();
		$sql="SELECT mg.`id`,mg.`title` AS `name` FROM `mini_menu_manager` AS mg WHERE mg.`status`=1";
		return $db->fetchAll($sql);
	}
	public function getMenuItems($parent = 0, $spacing = '', $cate_tree_array = ''){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT m.`id`,
			(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=1 LIMIT 1) AS name,m.`parent`
			 FROM `mini_menu` AS m WHERE m.`status`=1 AND m.`parent` = $parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getMenuItems($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function  getAllArticle(){
		$db = $this->getAdapter();
		$language = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT ad.title FROM `mini_article_detail` AS ad WHERE ad.articleId = c.`id` AND ad.language_id =$language LIMIT 1) AS title
		 FROM `mini_article` AS c WHERE c.`status`=1 ORDER BY c.id DESC";
		return $db->fetchAll($sql);
	}
	function getMenuType(){
		$db=$this->getAdapter();
		$sql="SELECT mt.`id`,mt.`title` AS name FROM `mini_menu_type` AS mt WHERE mt.`status`=1";
		return $db->fetchAll($sql);
	}
	
	
	public function getCategory($parent = 0, $spacing = '', $cate_tree_array = ''){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=1 LIMIT 1) AS name,
		c.`parent` FROM `mini_category` AS c WHERE c.`status`=1 AND c.`parent`=$parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
				foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
				}
		}
		return $cate_tree_array;
	} 
	public function getAllDepartment($parent = 0, $spacing = '', $cate_tree_array = ''){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT c.`id`,c.alias,
		(SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`id` AND cd.language_id=1 LIMIT 1) AS name,
		c.`depart_parentid` FROM `mini_department` AS c WHERE c.`status`=1 AND c.`depart_parentid`=$parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name'], "alias" => $row['alias']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function getAllParentAbout($parent = 0, $spacing = '', $cate_tree_array = ''){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$sql="SELECT a.`id`,a.alias,
		(SELECT ad.title FROM `mini_aboutministry_detail` AS ad WHERE ad.ministy_id = a.`id` AND ad.language_id=1 LIMIT 1) AS name,
		a.`parent_id` FROM `mini_aboutministry` AS a WHERE a.`status`=1 AND a.`parent_id`=$parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name'], "alias" => $row['alias']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function getCategoryForMenu($parent = 0, $spacing = '', $cate_tree_array = ''){ // for backend menu item category for menu
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$language = $this->getCurrentLang();
	
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=$language LIMIT 1) AS name,
		c.`parent` FROM `mini_category` AS c WHERE c.`status`=1 AND c.cate_type=2 AND c.`parent`=$parent ORDER BY id DESC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function getCategoryForProduct($parent = 0, $spacing = '', $cate_tree_array = ''){ // for backend menu item category for menu
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array))
			$cate_tree_array = array();
		$language = $this->getCurrentLang();
	
		$sql="SELECT c.`id`,
		(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=$language LIMIT 1) AS name,
		c.`parent` FROM `mini_category` AS c WHERE c.`status`=1 AND c.cate_type=1 AND c.`parent`=$parent ORDER BY id ASC";
		$query = $db->fetchAll($sql);
		$stmt = $db->query($sql);
		$rowCount = count($query);
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array);
			}
		}
		return $cate_tree_array;
	}
	public function checkEmailClient($email){ //check email has been use or not
		$db = $this->getAdapter();
		$sql='SELECT p.id FROM `mini_client` AS p WHERE p.`status`=1 AND p.`email`='."'$email'";
		$row= $db->fetchRow($sql);
		if (!empty($row)){
			return 1;
		}
		else{
			return 0;
		}
	}
	function getAllClient(){
		$db=$this->getAdapter();
		$sql="SELECT c.`id`,c.`user_name` AS name FROM `mini_client` AS c WHERE c.`status`=1";
		return $db->fetchAll($sql);
	}
	public function getRecieptNo(){
		$this->_name='mini_reciept';
		$db = $this->getAdapter();
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = "RC";
		for($i = $acc_no;$i<4;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
	public function getCustomerCode(){
		$this->_name='mini_client';
		$db = $this->getAdapter();
		$sql=" SELECT id FROM $this->_name ORDER BY id DESC LIMIT 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre = "CUS";
		for($i = $acc_no;$i<4;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}

	function checkSub($parent){
		$db = $this->getAdapter();
		$sql="SELECT m.`id` FROM `mini_category` AS m WHERE m.`status`=1 AND m.`parent`=".$parent;
		return $db->fetchRow($sql);
	}
	function loop_array($array = array(),$base_url,$parent_id=0,$count=0){
		$padding=0;
		$image='';
		$count = $count+1;
		$mainmneu=0;
		$string='';
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		if(!empty($array[$parent_id])){
			if ($parent_id>0){
				echo '<ul class="cat-tree groupmenu-drop">';
			}else{
				if ($mainmneu!=1){
					echo '<ul class="groupmenu">';
					$mainmneu=1;
					$string= '<li class="item level0  level-top cat-tree seemore " ><a class="menu-link" id="loadmore" href="javascript:void(0)"><span>'.$tr->translate("SHOW_MORE_CATEGORY").'</span></a>
					</li>';
					$string.= '<li class="item level0  level-top cat-tree seemore" ><a style="display:none" class="menu-link" id="hiddedlomore" href="javascript:void(0)"> <span>'.$tr->translate("HIDE_CATEGORY").'</span></a>
					</li>';
				}else{
					echo '<ul class="level3 groupmenu-drop">';
				}
			}
// 			if ($count==1){
// 				echo '<li>';
// 				echo '<a href="'.$base_url.'/index/all-items">View All Items</a></li>';
// 			}
			$seemore=0;
			foreach ($array[$parent_id] as $key=> $rs){
				$class='';
				if ($key>7){
					$class="noclass";
					$seemore=1;
				}
				$rr = $this->checkSub($rs['id']);
				if(!empty($rr)){
					if ($this->isParent($rs['id'])==0){
						echo '<li class="item level0  level-top parent cat-tree '.$class.'" >
						<a class="menu-link" href="'.$base_url.'/index/products/category/'.$rs['alias_category'].'.html"><i class="menu-icon fa fa-chevron-right"></i> <span><span>'.$rs['title'].'</span></span></a>
						';
					}else{
					echo '<li  class="item level2 nav-1-1 parent "><a class="menu-link" href="'.$base_url.'/index/products/category/'.$rs['alias_category'].'.html" ><span>'.$rs['title'].'</span></a>
						';
					}
				}else{
					if ($this->isParent($rs['id'])==0){
						echo '<li class="item level0  level-top  cat-tree '.$class.'" >
						<a class="menu-link" href="'.$base_url.'/index/products/category/'.$rs['alias_category'].'.html"><i class="menu-icon fa fa-chevron-right"></i> <span><span>'.$rs['title'].'</span></span></a>
						';
					}else{
					echo '<li  class="item level2 nav-1-1"><a class="menu-link" href="'.$base_url.'/index/products/category/'.$rs['alias_category'].'.html" ><span>'.$rs['title'].'</span></a>
						';
					}
				}
				$this->loop_array($array,$base_url,$rs['id'],$count);
				echo '</li>';
			}
			if ($seemore==1){
			echo $string;
			}
			echo '</ul>';
		}
	}
		function isParent($id){
			$db = $this->getAdapter();
			$sql="SELECT m.`parent` FROM `mini_category` AS m WHERE m.`status`=1 AND m.`id`=".$id;
			return $db->fetchOne($sql);
		}
	function display_menu($base_url){
		$db = $this->getAdapter();
		$language = $this->getCurrentLang();
		$sql="SELECT c.*,
			(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId =$language LIMIT 1) AS title
			 FROM `mini_category` AS c WHERE c.`status`=1 AND c.`cate_type` =1";
		$stmt = $db->fetchAll($sql);
		$result = $db->fetchAll($sql);
		$array = array();
		foreach ($stmt as $row){
			$array[$row['parent']][] =$row;
		}
		$this->loop_array($array,$base_url);
	}
	
	function getSubMaiMenu($base_url,$main_menu_id){
		$db = $this->getAdapter();
		$language = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=$language LIMIT 1) AS title
		FROM `mini_menu` AS m WHERE m.`status`=1 AND m.parent=$main_menu_id";
		$stmt = $db->fetchAll($sql);
		$result = $db->fetchAll($sql);
		$array = array();
		foreach ($stmt as $row){
			$array[$row['parent']][] =$row;
		}
		$this->loop_arrayMenu($array,$base_url,$main_menu_id);
	}
	function checkSubMenu($parent){
		$db = $this->getAdapter();
		$sql="SELECT m.`id` FROM `mini_menu` AS m WHERE m.`status`=1 AND m.`parent`=".$parent;
		return $db->fetchRow($sql);
	}
	function loop_arrayMenu($array = array(),$base_url,$parent_id=0){
		$padding=0;
		$image='';
		$mainmneu=0;
		if(!empty($array[$parent_id])){
			echo '<ul>';
			foreach ($array[$parent_id] as  $rs){
				$rr = $this->checkSubMenu($rs['id']);
				if(!empty($rr)){
					
					echo '<li>
							<a href="'.$base_url.'/page/index?param='.$rs['alias_menu'].'.html">'.$rs['title'].'<i class="icon-arrow-down"></i></a>';
					$this->getSubMaiMenu($base_url, $rs['id']);
				}else{
					echo '<li>
							<a href="'.$base_url.'/page/index?param='.$rs['alias_menu'].'.html">'.$rs['title'].'</a>';
				}
				$this->loop_arrayMenu($array,$base_url,$rs['id']);
				echo '</li>';
			}
	
			echo '</ul>';
		}
	}

}
?>