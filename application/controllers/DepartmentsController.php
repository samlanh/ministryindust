<?php

class DepartmentsController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    }

    public function indexAction()
    {
		$param = $this->getRequest()->getParam('dept');
// 		if (!empty($param["param"])){
// 	    	$temp = explode(".", $param["param"]);
// 	    	$db = new Application_Model_DbTable_DbGlobalSelect();
// 	    	$menudetail = $db->getMenuItemsByAlias($temp[0]);
// 	    	if (empty($menudetail)){
// 	    		$this->_redirect("/index");
// 	    	}
// 	    	$this->view->menu_info = $menudetail;
			
// 		}else if(!empty($param["article"])){
// 			$temp = explode(".", $param["article"]);
// 	    	$db = new Application_Model_DbTable_DbGlobalSelect();
// 			$row = $db->getAticleDetailByAlias($temp[0]);
// 			if (empty($row)){
// 	    		$this->_redirect("/index");
// 	    	}
// 			$this->view->article_de = $row;
// 		}else if(!empty($param["category"])){
// 			$temp = explode(".", $param["category"]);
// 	    	$db = new Application_Model_DbTable_DbGlobalSelect();
// 			$cate = $db->getCategoryByAlias($temp[0]);
// 			$this->view->cate = $cate;
// 			$this->view->article = $db->getArcticleByCateForlistAll($cate['id']);
// 			$this->view->countarticel = $db->countarticle($cate['id']);	
// 		}else{
// 			$this->_redirect("/index");
// 		}
// 		$this->view->menuright= $db->getMenuRight();
		$this->view->param = $param;
        
		$db = new Department_Model_DbTable_Dbdepartment();
		$rs = $db->getDepartmentFirstRecord($param);
		$this->view->rsfirst = $rs;
		
        $db = new Application_Model_DbTable_DbGlobalSelect();
        $this->view->rs_article = $db->getArcticleByDepartmentid($rs['dept_id']);//អត្តបទរបស់ News
        $this->view->rs_doc = $db->getDocumentByDeptid($rs['dept_id']);
        
        $bannerlist = $db->getWebsiteSetting("banner");
        $this->view->banner = $bannerlist;//explode(",", $bannerlist['value']);
        		
    	$db = new Application_Model_DbTable_DbVdGlobal();
    	$this->view->rsdepartment = $db->getAllDepartment();
    }  
}
