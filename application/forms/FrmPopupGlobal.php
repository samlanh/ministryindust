<?php

class Application_Form_FrmPopupGlobal extends Zend_Dojo_Form
{
	public function init()
	{
		
	}
	public function frmPopupClient(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frmco = new Group_Form_FrmClient();
		$frm = $frmco->FrmAddClient();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
					<div data-dojo-type="dijit.Dialog"  id="frm_client" >
					<form id="form_client" name="form_client" />';
				$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
							<tr>
							       <td>Is Group</td>
									<td>'.$frm->getElement('is_group').'</td>
									<td>Client N</td>
									<td>'.$frm->getElement('client_no').'</td>
								</tr>
								<tr>
									<td>Name Khmer</td>
									<td>'.$frm->getElement('name_kh').'</td>
									<td>Name Eng</td>
									<td>'.$frm->getElement('name_en').'</td>
								</tr>
								<tr>
									<td>Sex</td>
									<td>'.$frm->getElement('sex').'</td>
									<td>Status</td>
									<td>'.$frm->getElement('situ_status').'</td>
								</tr>
								<tr>
									<td>Province</td>
									<td>'.$frm->getElement('province').'</td>
									<td>District</td>
									<td>'.$frm->getElement('district').'</td>
								</tr>
								<tr>
									<td>Commune</td>
									<td>'.$frm->getElement('commune').'</td>
									<td>'.$tr->translate("Village").'</td>
									<td>'.$frm->getElement('village').'</td>
								</tr>
								<tr>
									<td>Street</td>
									<td>'.$frm->getElement('street').'</td>
									<td>'.$tr->translate("House N.").'</td>
									<td>'.$frm->getElement('house').'</td>
									
								</tr>
								<tr>
									<td>ID Type</td>
									<td>'.$frm->getElement('id_type').'</td>
									<td>'.$tr->translate("ID Card").'</td>
									<td>'.$frm->getElement('id_no').'</td>
								</tr>
								<tr>
									<td>'.$tr->translate("Phone").'</td>
									<td>'.$frm->getElement('phone').'</td>
									<td>'.$tr->translate("Spouse Name").'</td>
									<td>'.$frm->getElement('spouse').'</td>
								</tr>
								<tr>
									<td>'.$tr->translate("Status").'</td>
									<td>'.$frm->getElement('status').'</td>
									<td>'.$tr->translate("Note").'</td>
									<td>'.$frm->getElement('desc').'</td>
								</tr>
								<tr>
									<td colspan="4" align="center">
									<input type="button" value="Save" label="Save" dojoType="dijit.form.Button" 
										 iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewClient();"/>
									</td>
								</tr>
							</table>';	
							
		$str.='	</form>	</div>
				</div>';
		return $str;
	}
	
	public function frmPopupDistrict(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$frm = new Other_Form_FrmDistrict();
		$frm = $frm->FrmAddDistrict();
		Application_Model_Decorator::removeAllDecorator($frm);
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_district" >
				<form id="form_district" >';
		$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>District Name English</td>
						<td>'.$frm->getElement('pop_district_name').'</td>
					</tr>
								<tr>
						<td>Province Name English</td>
						<td>'.$frm->getElement('pop_district_namekh').'</td>
					</tr>
					<tr>
						<td>District Name Khmer</td>
						<td>'.$frm->getElement('province_names').'</td>
					</tr>
					
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="Save" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewDistrict();"/>
						</td>
				    </tr>
				</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	public function frmPopupCommune(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_commune" >
					<form id="form_commune" >';
			$str.='<table style="margin: 0 auto; width: 100%;" cellspacing="7">
					<tr>
						<td>Commune Name EN</td>
						<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" class="fullside" id="commune_nameen" name="commune_nameen" value="" type="text">'.'</td>
					</tr>
					<tr>
						<td>Commune KH</td>
						<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" class="fullside" id="commune_namekh" name="commune_namekh" value="" type="text">'.'</td>
					</tr>
					<tr>
						<td></td>
						<td>'.'<input dojoType="dijit.form.TextBox" required="true" class="fullside" id="district_nameen" name="district_nameen" value="" type="hidden">'.'</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
						<input type="button" value="Save" label="Save" dojoType="dijit.form.Button"
						iconClass="dijitEditorIcon dijitEditorIconSave" onclick="addNewCommune();"/>
						</td>
					</tr>
				</table>';
		$str.='</form></div>
			</div>';
		return $str;
	}
	public function frmPopupVillage(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_village" >
					<form id="form_village" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
		 <script type="dojo/method" event="onSubmit">			
			if(this.validate()) {
				return true;
			} else {
				return false;
			}
        </script>
		';
		$str.='<table style="margin: 0 auto; width: 95%;" cellspacing="10">
					    <tr>
							<td>'.$tr->translate("VILLAGE_KH").'</td>
							<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="village_namekh" name="village_namekh" value="" type="text">'.'</td>
						</tr>
						<tr>
							<td>'.$tr->translate("VILLAGE_NAME").'</td>
							<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="village_name" name="village_name" value="" type="text">'.'</td>
						</tr>
						<tr>
							<td>'. $tr->translate("DISPLAY_BY").'</td>
							<td>'.'<select name="display" id="display" dojoType="dijit.form.FilteringSelect" class="fullside">
									    <option value="1" label="Name KH">Name KH</option>
									    <option value="2" label="Name EN">Name EN</option>
									</select>'.'</td>
						</tr>
						<tr>
							<td>'.'<input dojoType="dijit.form.TextBox" class="fullside" id="province_name" name="province_name" value="" type="hidden">
								<input dojoType="dijit.form.TextBox" id="district_name" name="district_name" value="" type="hidden">
							'.'</td>
							<td>'.'<input dojoType="dijit.form.TextBox" id="commune_name" name="commune_name" value="" type="hidden">'.'</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
											<input type="reset" value="សំអាត" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
											<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVE').'" dojoType="dijit.form.Button" 
												iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addVillage();"  />
							</td>
						</tr>
					</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	public function frmPopupCustomer(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$str='<div class="dijitHidden">
				<div data-dojo-type="dijit.Dialog"  id="frm_client" title="'.$tr->translate("ADD_NEW_CUSTOMER").'" >
					<form id="form_client" dojoType="dijit.form.Form" method="post" enctype="application/x-www-form-urlencoded">
						<script type="dojo/method" event="onSubmit">
							if(this.validate()) {
								if(check==1){ alert("Email has been use on other account!");dijit.byId("email").focus(); return false;}
									return true;
								} else {
								return false;
								}
								</script>
							';
		$str.='<table style="margin: 0 auto; width: 95%;" cellspacing="10">
				<tr>
					<td>'.$tr->translate("CUSTOMER_NAME").'</td>
					<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="customer_name" name="customer_name" value="" type="text">'.'</td>
				</tr>
				<tr>
					<td>'.$tr->translate("EMAIL").'<span id="error" style=" color: #df0000;"></span></td>
					<td>'.'<input dojoType="dijit.form.ValidationTextBox" required="true" onChange="checkMail();" missingMessage="Invalid Module!" class="fullside" id="email" name="email" value="" type="text">'.'</td>
				</tr>
				<tr>
					<td>'. $tr->translate("PASSWORD").'</td>
					<td>'.'<input dojoType="dijit.form.TextBox" required="true" missingMessage="Invalid Module!" class="fullside" id="password" name="password" value="" type="password">'.'</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<input type="reset" value="សំអាត" label='.$tr->translate('CLEAR').' dojoType="dijit.form.Button" iconClass="dijitIconClear"/>
					<input type="button" value="save_close" name="save_close" label="'. $tr->translate('SAVE').'" dojoType="dijit.form.Button"
					iconClass="dijitEditorIcon dijitEditorIconSave" Onclick="addCustomer();"  />
					</td>
				</tr>
		</table>';
		$str.='</form></div>
		</div>';
		return $str;
	}
	
}

