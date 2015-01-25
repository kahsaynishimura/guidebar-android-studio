	<?php $EgoiMailListBuilder = get_option('EgoiMailListBuilderObject'); ?>
	<div class='wrap'>
	<div id="icon-egoi-mail-list-builder-widget-settings" class="icon32"></div>
	<h2>Widget Settings</h2>
	<?php require('donate.php'); ?>
	<?php if($EgoiMailListBuilder->isAuthed())	{
		if(isset($_POST['egoi_mail_list_builder_widget_settings_save'])) {
			$EgoiMailListBuilder->FIRST_NAME = $_POST['egoi_mail_list_builder_widget_settings_fname'];
			$EgoiMailListBuilder->LAST_NAME = $_POST['egoi_mail_list_builder_widget_settings_lname'];
			$EgoiMailListBuilder->EMAIL = $_POST['egoi_mail_list_builder_widget_settings_email'];
			$EgoiMailListBuilder->MOBILE = $_POST['egoi_mail_list_builder_widget_settings_mobile'];
			$EgoiMailListBuilder->LANGUAGE = $_POST['egoi_mail_list_builder_widget_settings_language'];
			$EgoiMailListBuilder->LANGUAGE_T_EN = $_POST['egoi_mail_list_builder_widget_settings_language_en'];
			$EgoiMailListBuilder->LANGUAGE_T_FR = $_POST['egoi_mail_list_builder_widget_settings_language_fr'];
			$EgoiMailListBuilder->LANGUAGE_T_DE = $_POST['egoi_mail_list_builder_widget_settings_language_de'];
			$EgoiMailListBuilder->LANGUAGE_T_PT_PT = $_POST['egoi_mail_list_builder_widget_settings_language_pt_pt'];
			$EgoiMailListBuilder->LANGUAGE_T_PT_BR = $_POST['egoi_mail_list_builder_widget_settings_language_pt_br'];
			$EgoiMailListBuilder->LANGUAGE_T_ES = $_POST['egoi_mail_list_builder_widget_settings_language_es'];
			$EgoiMailListBuilder->BIRTH_DATE = $_POST['egoi_mail_list_builder_widget_settings_bdate'];
			$EgoiMailListBuilder->SUBSCRIBE = $_POST['egoi_mail_list_builder_widget_settings_subscribe'];

			$EgoiMailListBuilder->FIRST_NAME_F = (isset($_POST['egoi_mail_list_builder_widget_settings_fname_f'])) ? true : false;
			$EgoiMailListBuilder->LAST_NAME_F = (isset($_POST['egoi_mail_list_builder_widget_settings_lname_f'])) ? true : false;
			$EgoiMailListBuilder->EMAIL_F = (isset($_POST['egoi_mail_list_builder_widget_settings_email_f'])) ? true : false;
			$EgoiMailListBuilder->MOBILE_F = (isset($_POST['egoi_mail_list_builder_widget_settings_mobile_f'])) ? true : false;
			$EgoiMailListBuilder->LANGUAGE_F = (isset($_POST['egoi_mail_list_builder_widget_settings_language_f'])) ? true : false;
			$EgoiMailListBuilder->BIRTH_DATE_F = (isset($_POST['egoi_mail_list_builder_widget_settings_bdate_f'])) ? true : false;

			$EgoiMailListBuilder->FIRST_NAME_E = $_POST['egoi_mail_list_builder_widget_settings_fname_e'];;
			$EgoiMailListBuilder->LAST_NAME_E = $_POST['egoi_mail_list_builder_widget_settings_lname_e'];;
			$EgoiMailListBuilder->EMAIL_E = $_POST['egoi_mail_list_builder_widget_settings_email_e'];;
			$EgoiMailListBuilder->MOBILE_E = $_POST['egoi_mail_list_builder_widget_settings_mobile_e'];;
			$EgoiMailListBuilder->LANGUAGE_E = $_POST['egoi_mail_list_builder_widget_settings_language_e'];;
			$EgoiMailListBuilder->BIRTH_DATE_E = $_POST['egoi_mail_list_builder_widget_settings_bdate_e'];;
			$EgoiMailListBuilder->LIST_E = $_POST['egoi_mail_list_builder_widget_settings_list_e'];;
			$EgoiMailListBuilder->SUCCESS_E = $_POST['egoi_mail_list_builder_widget_settings_success_e'];;

			update_option('EgoiMailListBuilderObject',$EgoiMailListBuilder);
		}
		egoi_mail_list_builder_admin_notices();
	?>
	<h3>Subscription form texts</h3>
	<form name='egoi_mail_list_builder_widget_settings_form' method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>'>
		<table class="form-table">
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_fname">First Name</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_fname' value='<?php echo $EgoiMailListBuilder->FIRST_NAME; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_fname_f' <?php if($EgoiMailListBuilder->FIRST_NAME_F) echo "checked";?>/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_fname_e' value='<?php echo $EgoiMailListBuilder->FIRST_NAME_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_lname">Last Name</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_lname' value='<?php echo $EgoiMailListBuilder->LAST_NAME; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_lname_f' <?php if($EgoiMailListBuilder->LAST_NAME_F) echo "checked";?>/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_lname_e' value='<?php echo $EgoiMailListBuilder->LAST_NAME_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_email">Email</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_email' value='<?php echo $EgoiMailListBuilder->EMAIL; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_email_f' checked disabled/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_email_e' value='<?php echo $EgoiMailListBuilder->EMAIL_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_mobile">Mobile</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_mobile' value='<?php echo $EgoiMailListBuilder->MOBILE; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_mobile_f' <?php if($EgoiMailListBuilder->MOBILE_F) echo "checked";?>/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_mobile_e' value='<?php echo $EgoiMailListBuilder->MOBILE_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language">Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language' value='<?php echo $EgoiMailListBuilder->LANGUAGE; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_language_f' <?php if($EgoiMailListBuilder->LANGUAGE_F) echo "checked";?>/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_e' value='<?php echo $EgoiMailListBuilder->LANGUAGE_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_en">English Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_en' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_EN; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_fr">French Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_fr' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_FR; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_de">German Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_de' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_DE; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_pt_pt">Portuguese Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_pt_pt' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_PT_PT; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_pt_br">Portuguese (Brasil) Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_pt_br' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_PT_BR; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_language_es">Spanish Language</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_language_es' value='<?php echo $EgoiMailListBuilder->LANGUAGE_T_ES; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_bdate">Birth Date</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_bdate' value='<?php echo $EgoiMailListBuilder->BIRTH_DATE; ?>'/>
				<input type='checkbox' size='60' name='egoi_mail_list_builder_widget_settings_bdate_f' <?php if($EgoiMailListBuilder->BIRTH_DATE_F) echo "checked";?>/>*
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_bdate_e' value='<?php echo $EgoiMailListBuilder->BIRTH_DATE_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_subscribe">Subscribe Button</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_subscribe' value='<?php echo $EgoiMailListBuilder->SUBSCRIBE; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_list_e">Invalid List Message</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_list_e' value='<?php echo $EgoiMailListBuilder->LIST_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_widget_settings_success_e">Success Message</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_widget_settings_success_e' value='<?php echo $EgoiMailListBuilder->SUCCESS_E; ?>'/>
			</td>
		</tr>
		<tr>
			<th>
				<em>*User Must Fill</em>
			</th>
		</tr>
		<tr>
			<th>
				<input type="submit" class='button-primary' name="egoi_mail_list_builder_widget_settings_save" id="egoi_mail_list_builder_widget_settings_save" value="Save" />	
			</th>
		</tr>
		</table>
	</form>
	<?php } ?>