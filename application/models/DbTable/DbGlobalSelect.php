<?php

class Application_Model_DbTable_DbGlobalSelect extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		$lang = $session_user->user_id;
		
		return $lang;
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		if(!empty($session_lang->lang_id)){
			return $session_lang->lang_id;
		}else{
			return 2;
		}
	}

	
	public function getWebsiteSetting($label){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_website_setting` AS ws WHERE ws.`label`='$label' AND ws.`status`=1 limit 1";
		return $db->fetchRow($sql);
	}
	
	public function getCateIdByAlias($cate_alias){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT cat.*,(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = cat.`id` AND cd.languageId=$lang LIMIT 1 ) AS title FROM `mini_category` AS cat WHERE cat.`alias_category`='$cate_alias' LIMIT 1";
		return $db->fetchRow($sql);
	}
	

	function getCliCate($id,$idetity=null){
		$where='';
		$db = $this->getAdapter();
		$sql=" SELECT c.`id` FROM `mini_category` AS c WHERE c.`parent` = $id AND c.`status`=1 ";
		$child = $db->fetchAll($sql);
		foreach ($child as $va) {
			if (empty($idetity)){$idetity=$id.",".$va['id'];}else{$idetity=$idetity.",".$va['id'];}
			$idetity = $this->getCliCate($va['id'],$idetity);
		}
		return $idetity;
	}
	
	function getMenuItems(){ //  for menu front
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=$lang_id LIMIT 1) AS title
		FROM `mini_menu` AS m WHERE m.`status`=1 AND m.parent=0 AND m.`menu_manager_id`=1 ";
		return $db->fetchAll($sql);
	}
	function getMenuItemsByAlias($alias){ //  for Controler page action index
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,(SELECT vd.title FROM `mini_menu_detail` AS vd WHERE vd.menu_id=m.`id` AND vd.languageId=$lang_id LIMIT 1) AS title_menu FROM `mini_menu` AS m WHERE m.`alias_menu`='$alias' limit 1";
		return $db->fetchRow($sql);
	}
	function getMenuContactByMenID($id){ //  for Controler page action index 'case page contacts'
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_menu_contact` AS mc WHERE mc.`menu_id`=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getAticleByID($id){ //  for Controler page action index 'case page single article'
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT atc.*,atcd.`title`,atcd.`description`
			FROM `mini_article` AS atc,`mini_article_detail` AS atcd 
			WHERE atcd.`articleId` = atc.`id` AND atcd.`language_id`=$lang_id AND atc.`id`=$id LIMIT 1";
		return $db->fetchRow($sql);
	}
	function getArcticleByCate($cateId){ //  for Controler page action 'case page category blog'
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT ad.title FROM `mini_article_detail` AS ad WHERE ad.articleId = a.`id` AND ad.language_id=$lang_id LIMIT 1) AS title,
		(SELECT ad.description FROM `mini_article_detail` AS ad WHERE ad.articleId = a.`id` AND ad.language_id=$lang_id LIMIT 1) AS description
		FROM `mini_article` AS a WHERE a.`status`=1 AND a.`category_id`=$cateId ";
		return $db->fetchAll($sql);
	}
	function getCategoryForHomepageArticle($listcate){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT c.*,
			(SELECT cd.title FROM `mini_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId =$lang LIMIT 1) AS title
			 FROM `mini_category` AS c WHERE c.id IN ($listcate)";
		 return $db->fetchAll($sql);
	}
	function getArticleByCateId($cate_id){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT arc.*,
		(SELECT arcd.title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS title,
		(SELECT arcd.description FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description,
		(SELECT arcd.description_forweb FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description_forweb,
		(SELECT arcd.short_descript FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS short_descript
		 FROM `mini_article` AS arc WHERE arc.`status`=1  ";
		$where='';
		if (!empty($cate_id)){// for get product by category
			$condiction = $this->getCliCate($cate_id);
			if (!empty($condiction)){ $where=" AND arc.`category_id` IN ($condiction)";}else{$where.=" AND arc.`category_id`=$cate_id";}
		}
		$limitarticle =	$this->getWebsiteSetting('items_homepage');	

		$order = ' ORDER BY arc.`id` DESC LIMIT '.$limitarticle['value'];
		return $db->fetchAll($sql.$where.$order);
	}
	function getCategoryByAlias($alias){
		
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql ="SELECT ca.*,
		(SELECT cad.title FROM `mini_category_detail` AS cad WHERE cad.category_id =ca.`id` AND cad.languageId=$lang LIMIT 1 ) AS title,
		(SELECT cad.sub_title FROM `mini_category_detail` AS cad WHERE cad.category_id =ca.`id` AND cad.languageId=$lang LIMIT 1 ) AS sub_title
		 FROM `mini_category` AS ca WHERE ca.`alias_category`='$alias' LIMIT 1";
		return $db->fetchRow($sql );
	}
	function getCategoryByID($id){
	
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql ="SELECT ca.*,
		(SELECT cad.title FROM `mini_category_detail` AS cad WHERE cad.category_id =ca.`id` AND cad.languageId=$lang LIMIT 1 ) AS title,
		(SELECT cad.sub_title FROM `mini_category_detail` AS cad WHERE cad.category_id =ca.`id` AND cad.languageId=$lang LIMIT 1 ) AS sub_title
		FROM `mini_category` AS ca WHERE ca.`id`='$id' LIMIT 1";
		return $db->fetchRow($sql );
	} 
	function countarticle($cate_id=null){
		$db = $this->getAdapter();
		$sql="select count(arc.id) FROM `mini_article` AS arc WHERE arc.`status`=1";
		if(!empty($cate_id)){
			$condiction = $this->getCliCate($cate_id);
			if (!empty($condiction)){ $sql.=" AND arc.`category_id` IN ($condiction)";}else{$sql.=" AND arc.`category_id`=$cate_id";}
		}
		return $db->fetchOne($sql);
	}
	function getArcticleByCateForlistAll($cate_id){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT arc.*,
		(SELECT arcd.title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS title,
		(SELECT arcd.sub_title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS sub_title,
		(SELECT arcd.description FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description,
		(SELECT arcd.short_descript FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS short_descript,
		(SELECT arcd.description_forweb FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description_forweb
		 FROM `mini_article` AS arc WHERE arc.`status`=1   ";
		$where='';
		if (!empty($cate_id)){// for get article by category
			$condiction = $this->getCliCate($cate_id);
			if (!empty($condiction)){ $where=" AND arc.`category_id` IN ($condiction)";}else{$where.=" AND arc.`category_id`=$cate_id";}
		}
		$order = ' ORDER BY arc.`id` DESC ';
	
		$row =  $db->fetchAll($sql.$where.$order);
		return $row;
		
	}
	function getArcticleByCateLastesArticle($cate_id,$article=null){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT arc.*,
		(SELECT arcd.title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS title,
		(SELECT arcd.sub_title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS sub_title,
		(SELECT arcd.description FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description,
		(SELECT arcd.short_descript FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS short_descript,
		(SELECT arcd.description_forweb FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description_forweb
		FROM `mini_article` AS arc WHERE arc.`status`=1   ";
		$where='';
		if (!empty($cate_id)){// for get article by category
			$condiction = $this->getCliCate($cate_id);
			if (!empty($condiction)){
			$where.=" AND arc.`category_id` IN ($condiction)";
			}else{$where.=" AND arc.`category_id`=$cate_id";
			}
		}
		if (!empty($article)){
			$where.=" AND arc.`id` != $article";
		}
		$order = ' ORDER BY arc.`id` DESC LIMIT 5';
	
		$row =  $db->fetchAll($sql.$where.$order);
		return $row;
	
	}
	function getAticleByListId($listarticleid){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql ="SELECT arc.*,
		(SELECT arcd.title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS title,
		(SELECT arcd.sub_title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS sub_title,
		(SELECT arcd.description FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description,
		(SELECT arcd.description_forweb FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description_forweb,
		(SELECT arcd.short_descript FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS short_descript
		 FROM `mini_article` AS arc WHERE arc.`status`=1 AND arc.id IN ($listarticleid) ";
		 return $db->fetchAll($sql);
	}
	function getAticleDetailByAlias($alias){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql ="SELECT arc.*,
		(SELECT arcd.title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS title,
		(SELECT arcd.sub_title FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS sub_title,
		(SELECT arcd.description FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS description,
		(SELECT arcd.short_descript FROM `mini_article_detail` AS arcd WHERE arcd.articleId = arc.`id` AND arcd.language_id=$lang LIMIT 1) AS short_descript,
	  	(SELECT cat.title FROM `mini_category_detail` AS cat WHERE cat.category_id = arc.`category_id` AND cat.languageId=$lang LIMIT 1) AS cate_title
		 FROM `mini_article` AS arc WHERE arc.`status`=1 AND arc.`alias_article`='$alias' LIMIT 1";
		 return $db->fetchRow($sql);
	}
	function getMenuRight(){ // for home page and article detail
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT m.*,
		(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id=m.`id`  AND md.languageId=$lang LIMIT 1) AS title
		 FROM `mini_menu` AS m WHERE m.`status`=1 AND m.`parent`=0 AND m.`menu_manager_id`=2";
	  return $db->fetchAll($sql);
	}
	
	function checkSubMenuRight($parent){
		$db = $this->getAdapter();
		$sql="SELECT m.`id` FROM `mini_menu` AS m WHERE m.`status`=1 AND menu_manager_id=2 AND m.`parent`=".$parent;
		return $db->fetchRow($sql);
	}
	function loop_arrayMenuRight($array = array(),$base_url,$parent_id=0){
		$padding=0;
		$image='';
		$mainmneu=0;
		if(!empty($array[$parent_id])){
			echo '<ul class="sidebar-submenu">';
			foreach ($array[$parent_id] as  $rs){
				$rr = $this->checkSubMenuRight($rs['id']);
				if(!empty($rr)){
					
					echo '<li>
							<a href="'.$base_url.'/page/index?param='.$rs['alias_menu'].'.html">'.$rs['title'].'<i class="fa fa-angle-left pull-right"></i></a>';
					$this->getSubMenuRight($base_url, $rs['id']);
				}else{
					echo '<li>
							<a  href="'.$base_url.'/page/index?param='.$rs['alias_menu'].'.html">'.$rs['title'].'</a>';
				}
				$this->loop_arrayMenuRight($array,$base_url,$rs['id']);
				echo '</li>';
			}
	
			echo '</ul>';
		}
	}
	function getSubMenuRight($base_url,$main_menu_id){
		$db = $this->getAdapter();
		$language = $this->getCurrentLang();
		$sql="SELECT *,
		(SELECT md.title FROM `mini_menu_detail` AS md WHERE md.menu_id = m.`id` AND md.languageId=1 LIMIT 1) AS title
		FROM `mini_menu` AS m WHERE m.`status`=1 AND m.menu_manager_id=2 AND m.parent=$main_menu_id";
		$stmt = $db->fetchAll($sql);
		$result = $db->fetchAll($sql);
		$array = array();
		foreach ($stmt as $row){
			$array[$row['parent']][] =$row;
		}
		$this->loop_arrayMenuRight($array,$base_url,$main_menu_id);
	}
	
	public function getAllFaq(){
		$db=$this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT
		act.*,
		(SELECT ad.title FROM `mini_faq_detail` AS ad WHERE ad.faq_id = act.`id` AND language_id=$lang LIMIT 1) AS title,
		(SELECT ad.description FROM `mini_faq_detail` AS ad WHERE ad.faq_id = act.`id` AND language_id=$lang LIMIT 1) AS description
		FROM `mini_faq` AS act
		WHERE act.`status` = 1";
		$where='';
		$order = "  ORDER BY act.`id` DESC";
		return $db->fetchAll($sql.$where.$order);
	}
	
	
	public function getAllCompanySearch($data){
		$db=$this->getAdapter();
		$lang = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$sql="
		SELECT c.*,
		(SELECT cd.title FROM `mini_department_detail` AS cd WHERE cd.department_id = c.`depart_id` AND cd.language_id=$lang LIMIT 1) AS department_name,
		(SELECT ".$province_field[$lang]." FROM mini_province WHERE  province_id=c.province_id LIMIT 1) as province_name
		FROM `mini_company` AS c WHERE com_name!='' ";
		 
		if(!empty($data['zip_code'])){
			$s_where = array();
			$s_search = addslashes(trim($data['zip_code']));
			$s_where[] = " com_code LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
		if ($data['oranization']>0){
			$sql.=" AND c.`depart_id`=".$data['oranization'];
		}
		if ($data['province']>0){
			$sql.=" AND c.`province_id`=".$data['province'];
		}
		if(!empty($data['product'])){
			$s_where = array();
			$s_search = addslashes(trim($data['product']));
			$s_where[] = " product LIKE '%{$s_search}%'";
			$sql.=' AND ('.implode(' OR ',$s_where).')';
		}
		return $db->fetchAll($sql);
	}
	function getCompanyDetailById($id){
		$db = $this->getAdapter();
		$lang_id = $this->getCurrentLang();
		$province_field = array(
				"1"=>"province_en_name",
				"2"=>"province_kh_name"
		);
		$sql="SELECT c.*,
		(SELECT mp.".$province_field[$lang_id]." FROM mini_province AS mp WHERE mp.province_id = c.province_id LIMIT 1) AS province_name
		 FROM `mini_company` AS c WHERE c.status=1 AND c.id = $id LIMIT 1";
		return $db->fetchRow($sql);
	}
	public function getAllDepartment(){
		$db = $this->getAdapter();
		$lang = $this->getCurrentLang();
		$sql="SELECT dp.*,
		(SELECT dp_d.title  FROM `mini_department_detail` AS dp_d WHERE dp_d.language_id=$lang AND dp_d.department_id = dp.id) AS title
		 FROM `mini_department` AS dp WHERE dp.depart_parentid = 0 AND dp.status=1";
		return $db->fetchAll($sql);
	}
	
	function getCompanyByDept($dep_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `mini_company` AS c WHERE c.status=1 ";
		
		if (!empty($dep_id)){// for get product by category
			$condiction = $this->getListDeptById($dep_id);
			if (!empty($condiction)){
				$sql.=" AND c.depart_id IN ($condiction)";
			}else{$sql.=" AND c.depart_id=$dep_id";
			}
		}
		$limit=15;
		$sql.=" ORDER BY c.id ASC LIMIT $limit";
		return $db->fetchAll($sql);
	}
	
	function getListDeptById($id,$idetity=null){
		$where='';
		$db = $this->getAdapter();
		$sql=" SELECT c.`id` FROM `mini_department` AS c WHERE c.`depart_parentid` = $id AND c.`status`=1 ";
		$child = $db->fetchAll($sql);
		foreach ($child as $va) {
			if (empty($idetity)){
				$idetity=$id.",".$va['id'];
			}else{$idetity=$idetity.",".$va['id'];
			}
			$idetity = $this->getListDeptById($va['id'],$idetity);
		}
		return $idetity;
	}
}
?>