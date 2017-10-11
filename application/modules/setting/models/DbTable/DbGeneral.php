<?php

class Setting_Model_DbTable_DbGeneral extends Zend_Db_Table_Abstract
{

    protected $_name = 'mini_website_setting';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function updateWebsitesetting($data){
		try{
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$part= PUBLIC_PATH.'/images/lang/';
			$name = $_FILES['photo']['name'];
			$size = $_FILES['photo']['size'];
			$photo='';
			if (!empty($name)){
					$tem =explode(".", $name);
					$image_name = time()."logo.".end($tem);
					$tmp = $_FILES['photo']['tmp_name'];
					if(move_uploaded_file($tmp, $part.$image_name)){
						$photo = $image_name;
					}
					else
						$string = "Image Upload failed";
					
					$arr = array(
							'value'=>$photo,
					);
					$where=" label= 'logo'";
					$this->update($arr, $where);
			}
			
			$arr = array(
					'value'=>$data['items_per_page'],
					);
			$where=" label= 'items_per_page'";
			$this->update($arr, $where);
			
// 			$arr = array(
// 					'value'=>$data['items_homepage'],
// 			);
// 			$where=" label= 'items_homepage'";
// 			$this->update($arr, $where);
			$arr = array(
					'value'=>$data['category'],
			);
			$where=" label= 'homecategorycontent'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['address'],
			);
			$where=" label= 'address'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['email'],
			);
			$where=" label= 'email'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['phone'],
			);
			$where=" label= 'tel'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['facebook'],
			);
			$where=" label= 'facebook'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['youtube'],
			);
			$where=" label= 'youtube'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['twitter'],
			);
			$where=" label= 'twitter'";
			$this->update($arr, $where);
			
			$arr = array(
					'value'=>$data['googleplus'],
			);
			$where=" label= 'googleplus'";
			$this->update($arr, $where);
			
// 			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
// 			$part= PUBLIC_PATH.'/images/headerbackground/';
// 			$name = $_FILES['backgroundheader']['name'];
// 			$size = $_FILES['backgroundheader']['size'];
// 			$photo='';
// 			if (!empty($name)){
// 					$tem =explode(".", $name);
// 					$image_name = time()."headerbg.".end($tem);
// 					$tmp = $_FILES['backgroundheader']['tmp_name'];
// 					if(move_uploaded_file($tmp, $part.$image_name)){
// 						$photo = $image_name;
// 					}
// 					else
// 						$string = "Image Upload failed";
					
// 					$arr = array(
// 							'value'=>$photo,
// 					);
// 					$where=" label= 'headebackground'";
// 					$this->update($arr, $where);
// 			}
			
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	
}

