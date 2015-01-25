	<?php $EgoiMailListBuilder = get_option('EgoiMailListBuilderObject'); ?>
	<div class='wrap'>
	<div id="icon-egoi-mail-list-builder-lists" class="icon32"></div>
	<h2>Mailing Lists</h2>
	<?php require('donate.php'); ?>
	<h4>You're almost there! To add an E-goi form to your site, just go to the Wordpress "Appearance" menu. Then click "Widgets", drag the E-goi Mailing List Builder widget into "Main Sidebar" or one of the frontpage areas and save your changes. That's all there is to it!<br><br>Your E-goi account information follows below:</h4>
	<?php 
	if($EgoiMailListBuilder->isAuthed()) {
		unset($result);
		$result = $EgoiMailListBuilder->getLists();
		update_option('EgoiMailListBuilderObject',$EgoiMailListBuilder);
		for($x = 0;$x < count($result); $x++) {
			$postname = 'egoi_mail_list_builder_updatelist'.$result[$x]['listnum'];
			if(isset($_POST[$postname])) {
				$posttitle = 'egoi_mail_list_builder_title_text'.$result[$x]['listnum'];
				$postlang = 'egoi_mail_list_builder_list_lang'.$result[$x]['listnum'];
				$EgoiMailListBuilder->updateList($result[$x]['listnum'],$_POST[$posttitle],$_POST[$postlang]);
				unset($result);
				$result = $EgoiMailListBuilder->getLists();
			}
		}
		if(isset($_POST['egoi_mail_list_builder_createlist'])) {
			$EgoiMailListBuilder->createList($_POST['egoi_mail_list_builder_Elist_title'],$_POST['egoi_mail_list_builder_list_lang']);
			update_option('EgoiMailListBuilderObject',$EgoiMailListBuilder);
			unset($result);
			$result = $EgoiMailListBuilder->getLists();
		}
		if(count($result) != 0){
			array_multisort($result,SORT_ASC);
		}
		egoi_mail_list_builder_admin_notices();
	?>
		<form name='egoi_mail_list_builder_updatelist_form' method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>'>
		<?php
			$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
			$limit = 10;
			$offset = ( $pagenum - 1 ) * $limit;

			$total = count($result);
			$num_of_pages = ceil( $total / $limit );
			$page_links = paginate_links( array(
			    'base' => add_query_arg( 'pagenum', '%#%' ),
			    'format' => '',
			    'prev_text' => __( '&laquo;', 'aag' ),
			    'next_text' => __( '&raquo;', 'aag' ),
			    'total' => $num_of_pages,
			    'current' => $pagenum
			) );
		?>
		<div class= "tablenav">
			<div class='tablenav-pages' style="margin: 1em 0">
				<?php echo $page_links; ?>
			</div>
		</div>
		<table border='1' class="widefat">
		<thead>
			<tr>
				<th>List ID</th>
				<th>Title</th>
				<th>Internal Title</th>
				<th>Group</th>
				<th>Active Subscribers</th>
				<th>Total Subscribers</th>
				<th>Language</th>
				<th>Update</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>List ID</th>
				<th>Title</th>
				<th>Internal Title</th>
				<th>Group</th>
				<th>Active Subscribers</th>
				<th>Total Subscribers</th>
				<th>Language</th>
				<th>Update</th>
			</tr>
		</tfoot>
		<?php for($x = $offset;$x < $limit + $offset && isset($result[$x]); $x++) { ?>
			<tr>
				<td>
					<?php echo $result[$x]['listnum']; ?>
					<input type='hidden' name='egoi_mail_list_builder_listid' value='<?php echo $result[$x]['listnum']; ?>' />
				</td>
				<td>
					<input type='text' name='egoi_mail_list_builder_title_text<?php echo $result[$x]['listnum']; ?>' value='<?php echo $result[$x]['title']; ?>'/>
				</td>
				<td>
					<?php echo $result[$x]['title_ref']; ?>
				</td>
				<td>
					<?php echo $result[$x]['grupo']; ?>
				</td>
				<td>
					<?php echo $result[$x]['subs_activos']; ?>
				</td>
				<td>
					<?php echo $result[$x]['subs_total']; ?>
				</td>
				<td>
					<select name='egoi_mail_list_builder_list_lang<?php echo $result[$x]['listnum']; ?>'>
					<?php if(strcmp($result[$x]['idioma'],'en')==0)	{ ?>
						<option value='en' selected>English</option>
					<?php } else { ?>
						<option value='en'>English</option>
					<?php }	if(strcmp($result[$x]['idioma'],'fr')==0) { ?>
						<option value='fr' selected>French</option>
					<?php }	else { ?>
						<option value='fr'>French</option>
					<?php }	if(strcmp($result[$x]['idioma'],'de')==0) { ?>
						<option value='de' selected>German</option>
					<?php }	else { ?>
						<option value='de'>German</option>
					<?php }	if(strcmp($result[$x]['idioma'],'pt')==0) { ?>
						<option value='pt' selected>Portuguese</option>
					<?php } else { ?>
						<option value='pt'>Portuguese</option>
					<?php }	if(strcmp($result[$x]['idioma'],'br')==0) { ?>
						<option value='br' selected>Portuguese (Brasil)</option>
					<?php }	else { ?>
						<option value='br'>Portuguese (Brasil)</option>
					<?php }	if(strcmp($result[$x]['idioma'],'es')==0) { ?>
						<option value='es' selected>Spanish</option>
					<?php }	else { ?>
						<option value='es'>Spanish</option>
					<?php } ?>
					</select>
				</td>
				<td>
					<input type="submit" class='button-primary' name="egoi_mail_list_builder_updatelist<?php echo $result[$x]['listnum']; ?>" id="'egoi_mail_list_builder_updatelist<?php echo $result[$x]['listnum']; ?>" value="Update" />
				</td>
			</tr>
		<?php } ?>
		</table>
		<div class= "tablenav">
			<div class='tablenav-pages' style="margin: 1em 0">
				<?php echo $page_links; ?>
			</div>
		</div>
		</form>
		<h3>Create another list</h3>
		<form name='egoi_mail_list_builder_createlist_form' method='post' action='<?php echo $_SERVER['REQUEST_URI']; ?>'>
		<table class="form-table">
		<tr>
			<th>
				<label for="egoi_mail_list_builder_Elist_title">Name</label>
			</th>
			<td>
				<input type='text' size='60' name='egoi_mail_list_builder_Elist_title' />
			</td>
		</tr>
		<tr>
			<th>
				<label for="egoi_mail_list_builder_list_lang">Language</label>
			</th>
			<td>
				<select name='egoi_mail_list_builder_list_lang'>
				<option value='en'>English</option>
				<option value='fr'>French</option>
				<option value='de'>German</option>
				<option value='pt'>Portuguese</option>
				<option value='br'>Portuguese (Brasil)</option>
				<option value='es'>Spanish</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<input type='submit'class='button-primary' name='egoi_mail_list_builder_createlist' id='egoi_mail_list_builder_createlist' value='Add' />
			</th>
			<td>
			</td>
		</tr>
		</table>
		</form>
	<?php } ?>
	</div>