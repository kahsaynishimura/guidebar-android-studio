<?php
		include_once 'common.php';
		include_once 'const.php';
		
		//show the UI of popup manage
		function sub_squeezers_popup_manage_cb()
		{
			//get the id from  the db, display in a list. radio button to preview. Delete option
			?>
			<div id="popup_manage_container">
			<?php echo sq_bgt_activation_notice();?>
				<!-- First row, where user selects the popup -->
				<div id="popup_manage_first_row" class="popup_manage_row" >
					<div id="popup_manage_left" style="float:left; width: 40%;">
					<h4>Your popups</h4>
					<?php popup_manage_show_pop();?>					
					</div>
					<div id="popup_manage_right" style="width: 58%;">
					
					</div>
					<div style="clear: both;"></div>
				</div>
				
			
			
			<div id="popup_manage_second_row" class="popup_manage_row">
			<h4>Position to appear</h4>
			<ul>
				<li><input type="radio" name="display_pos"  id="pop_top_left"/> Top Left</li>
				<li><input type="radio" name="display_pos"  id="pop_top_right"/> Top Right</li>
				<li><input type="radio" name="display_pos"  id="pop_bottom_left"/> Bottom Left</li>
				<li><input type="radio" name="display_pos"  id="pop_bottom_right"/> Bottom Right</li>
				<li><input type="radio" name="display_pos"  id="pop_center"/> Center</li>
				
			</ul>
			</div>
			
			<div id="popup_manage_3rd_row" class="popup_manage_row">
			<h4>How to appear</h4>
				<ul>
					<li><input type="radio" name="how_appear" id="pop_on_exit"/> On Exit Intention</li>
					<li><input type="radio" name="how_appear" id="pop_timer" /> Timer</li>
				</ul>
				<div id="timer_div" style="display:none;">Set your time (in seconds)<br />
				<input type="text" id="pop_timer_time" />
				</div>
				
			</div>
			
			<div id="popup_manage_4th_row" class="popup_manage_row">
			<h4>Background Color</h4>
			<ul>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_white"/> White</li>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_black"/> Black</li>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_transparent_black"/> Transparent Black</li>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_transparent"/> Transparent</li>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_pattern"/> Pattern</li>
				<li><input type="radio" name="pop_bg_color" id="pop_bg_image"/> Use image</li>
			</ul>	
			<input type="text" id="pop_bg_custom_image" value="enter your image URL here" style="width: 300px; display: none;" />
			</div>
			
			<div id="popup_manage_5th_row" class="popup_manage_row">
				<h4>Where to appear</h4>
				<ul>
					<li><input type="radio" name="display_where" id="pop_display_all"/> Every Page/Post</li>
					<li><input type="radio" name="display_where" id="pop_display_particular"/> On particular posts</li>
					<li><input type="radio" name="display_where" id="pop_display_home"/> On homepage ONLY</li>
				</ul>			
			</div>
			
			<div id="popup_manage_6th_row" class="popup_manage_row">
				<h4>Cover all background?</h4>
				<ul>
					<li><input type="radio" name="pop_cover_bg" id="pop_cover_yes"/> Yes</li>
					<li><input type="radio" name="pop_cover_bg" id="pop_cover_no"/> No</li>
				</ul>
			</div>
			
			<div id="popup_manage_7h_row" class="popup_manage_row">
				<h4>Activate this option? (this will deactivate current active option)</h4>
				<ul>
					<li><input type="radio" name="pop_active" id="pop_active_yes"/> Yes</li>
					<li><input type="radio" name="pop_active" id="pop_active_no"/> No</li>
				</ul>
			</div>
			
			<div id="popup_manage_8th_row" class="popup_manage_row">
				<h4>Display frequency (once or repeatedly)</h4>
				<ul>
					<li><input type="radio" name="pop_display_freq" id="once"/> Once</li>
					<li><input type="radio" name="pop_display_freq" id="all_time"/> All the time (your users may get angry)</li>
					
				</ul>
			</div>
	
			<div id="popup_manage_9th_row" class="popup_manage_row">
				<input type="button" id="save_manage_popup_option" class="button button-primary" value="Save Option" />
			</div>
			
			<div id="popup_manage_10th_row" class="popup_manage_row">
			<h4>Current Options</h4>
				<?php 
				//display current popup options
				global $wpdb;
				$table = BGT_POPUP_OPTION_TABLE; // $wpdb->get_blog_prefix().'sq_popup_option';
				
				//get all the options
				$pop_options = $wpdb->get_results("SELECT * FROM $table", 'ARRAY_A');
				//display the options
				?>
				<table id="pop_listing_options" style="border: 1px solid;">
				<tr>
					<th>Preview</th>
					<th>Appear Position</th>
					<th>Appear Behavior</th>
					<th>Background Color</th>
					<th>Where To Display</th>
					<th>Cover All Background?</th>
					<th>Delay</th>
					<th>Image URL</th>
					<th>Status</th>
					<th>Once or All</th>
					<th>Delete</th>
					<th>Save</th>
					<th>Get Shortcode</th>
					
				</tr>
				<?php for ($i=0; $i < count($pop_options); $i++)
				{?>
					<div>
						<tr>
							<td>
								<a class="listing_view_popup" href="#pop_theme_preview" rel="lightcase" op_id="<?php echo $pop_options[$i]['id']; ?>" pop_id="<?php echo $pop_options[$i]['popup_id']; ?>">View</a>
							</td>
							<td>
								<select name="listing_appear_position">
									<option value="pop_top_left">Top Left</option>
									<option value="pop_top_right">Top Right</option>
									<option value="pop_bottom_left">Bottom Left</option>
									<option value="pop_bottom_right">Bottom Right</option>
									<option value="pop_center">Center</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['appear_position'];?></span>
							</td>
							
							<td>
								<select name="listing_appear_behavior">
									<option value="pop_on_exit">On Exit Behavior</option>
									<option value="pop_timer">Timer</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['appear_behavior'];?></span>
							</td>
							
							<td>
								<select name="listing_background_color">
									<option value="pop_bg_white">White</option>
									<option value="pop_bg_black">Black</option>
									<option value="pop_bg_transparent_black">Transparent Black</option>
									<option value="pop_bg_transparent">Transparent</option>
									<option value="pop_bg_pattern">Pattern</option>
									<option value="pop_bg_image">Image BG</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['background_color'];?></span>
							</td>
							
							<td>
								<select name="listing_display_area">
									<option value="pop_display_all">All Post/Page</option>
									<option value="pop_display_particular">On Particular Post</option>
									<option value="pop_display_home">On homepage ONLY</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['display_area'];?></span>
							</td>
							
							<td>
								<select name="listing_background_cover">
									<option value="pop_cover_yes">Yes</option>
									<option value="pop_cover_no">No</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['background_cover'];?></span>
							</td>
							
							<td>
								<input type="text" class="pop_listing_delay" value="<?php echo $pop_options[$i]['delay'];?>" />
							</td>
							<td>
								<input type="text" class="pop_listing_custom_image" <?php if (stripos($pop_options[$i]['background_color'], "*") == false) { echo  "value='Not Available'";} else  { $ar = explode("*", $pop_options[$i]['background_color']); echo 'value="'.$ar[0].'" img_type="'.$ar[1].'"';  } ?> />
							</td>
							<td>
								<select name="listing_status">
									<option value="pop_active_yes">Active</option>
									<option value="pop_active_no">Not Active</option>

								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['status'];?></span>
							</td>
							
							<td>
								<select name="listing_frequency">
									<option value="once">Once</option>
									<option value="all_time">All Time</option>
								</select>
								<span class="pop_selected_option" style="display: none;"><?php echo $pop_options[$i]['frequency'];?></span>
							</td>
							
							<td><a href="" class="button button-primary pop_delete_btn">Delete</a></td>
							<td><a href="" class="button button-primary pop_save_btn">Save</a></td>
							<td><a href="#pop_shortcode" class="button button-primary pop_getcode_btn" rel="lightcase" option_id="<?php echo $pop_options[$i]['id'];?>">Get Code</a></td>
						</tr>
					</div>
				<?php }
				
				?>
				</table>
				<!-- PREVIEW THE THEME/VIEW THE SHORTCODE -->
				<div id="pop_shortcode" style="display: none;"></div>
				<div id="pop_theme_preview" style="display: none;"></div>
				
			</div>
			<br /><br />
			</div>
		<?php include_once 'common.txt'; }



		//function to display available popup in the db
		function popup_manage_show_pop()
		{
			//get the available popup in the db
			global $wpdb;
			
			//get the table
			$popup_table = BGT_POPUP_CODE_TABLE; //$wpdb->get_blog_prefix().'sq_popup_code';
			
			//get the id
			$array_id = $wpdb->get_results("SELECT popup_id, name FROM $popup_table", 'ARRAY_A');
			echo "<ul>";
			for ($i=0; $i<count($array_id); $i++)
			{
				//set a generic name
				if (trim($array_id[$i]['name']) == "")
				{
					$array_id[$i]['name'] = "You didn't set a name for this";
				}
				$name = explode("*x*x*", $array_id[$i]['name']);
				$name = $name[0];
				?>
				<li><input type="radio" name="popup_listing" class="popup_manage_listing" pop_id="<?php echo $array_id[$i]['popup_id']?>"> <?php echo $name; ?>  <a href="" class="pop_manage_delete_pop" style="margin-left: 30px;">Delete</a> <a href="#pop_theme_preview" rel="lightcase" class="pop_manage_preview_pop" pop_id="<?php echo $array_id[$i]['popup_id']?>" style="margin-left: 30px;">View</a></li>
			<?php }
			echo "</ul>";
			
		}
		
		//function to show the preview
		
		add_action('wp_ajax_popup_manage_show_preview', 'popup_manage_show_preview_cb');
		
		function popup_manage_show_preview_cb()
		{
			$popup_id = $_POST['popup_id'];
			
			//get data from db
			global $wpdb;
			
			//get the table
			$popup_table = BGT_POPUP_CODE_TABLE; // $wpdb->get_blog_prefix().'sq_popup_code';
			
			$popup = $wpdb->get_results("SELECT * FROM $popup_table WHERE popup_id = '$popup_id'", 'ARRAY_A');
			
			$return_data = array();
			$return_data['code'] = $popup[0]['code'];
			$return_data['css_url'] = $popup[0]['css_url'];
			
			echo "123dddsacxz".json_encode($return_data)."123dddsacxz";
			die();
		}
		
		
		//save the popup option
		add_action('wp_ajax_popup_save_option', 'popup_save_option_cb');
		
		function popup_save_option_cb()
		{

			global $wpdb;
			
			$popup_id = $_POST['selected_popup'];
			$appear_position = $_POST['appear_position'];
			$appear_behavior = $_POST['appear_behavior'];
			$bg_color = $_POST['bg_color'];
			$bg_cover = $_POST['bg_cover'];
			$appear_where = $_POST['appear_where'];
			$active = $_POST['active'];
			$delay = $_POST['delay'];
			$frequency = $_POST['frequency'];

			
		
			$data = array(
				'popup_id' => $popup_id,
				'appear_position' => $appear_position,
				'appear_behavior' => $appear_behavior,
				'background_color' => $bg_color,
				'display_area' => $appear_where,
				'background_cover' => $bg_cover,
				'status' => $active,
				'delay' => $delay,
				'frequency' => $frequency
			);
			
			try {

				//if $active == yes, disable all other options
				if ($active == 'pop_active_yes')
				{
					$table = BGT_POPUP_OPTION_TABLE; //$wpdb->get_blog_prefix().'sq_popup_option';
					$update_data = array('status' => 'pop_active_no');
					$update_where = array('status' => 'pop_active_yes');
					$wpdb->update($table, $update_data, $update_where);
				}
				
				//insert the new data into the db
				$table = BGT_POPUP_OPTION_TABLE; //$wpdb->get_blog_prefix().'sq_popup_option';

				
				$wpdb->insert($table, $data);
				echo "Done";
			} catch (Exception $e)
			{
				echo "something wrong!!!";
			}
			
			die();

		}
		
		
		//save/update the popup listing option popup_save_listing_option
		add_action('wp_ajax_popup_save_listing_option', 'popup_save_listing_option_cb');
		
		function popup_save_listing_option_cb()
		{
			global $wpdb;
				
			$op_id = $_POST['op_id'];
			$appear_position = $_POST['appear_position'];
			$appear_behavior = $_POST['appear_behavior'];
			$background_color = $_POST['background_color'];
			$background_cover = $_POST['background_cover'];
			$display_area = $_POST['display_area'];
			$active = $_POST['active'];
			$delay = $_POST['delay'];
			$frequency = $_POST['frequency'];
			
				
			
			$data = array(
					'appear_position' => $appear_position,
					'appear_behavior' => $appear_behavior,
					'background_color' => $background_color,
					'display_area' => $display_area,
					'background_cover' => $background_cover,
					'status' => $active,
					'delay' => $delay,
					'frequency' => $frequency,
					'status' => $active
			);
				
			try {
			
				//if $active == yes, disable all other options
				if ($active == 'pop_active_yes')
				{
					$table = BGT_POPUP_OPTION_TABLE; //$wpdb->get_blog_prefix().'sq_popup_option';
					$update_data = array('status' => 'pop_active_no');
					$update_where = array('status' => 'pop_active_yes');
					$wpdb->update($table, $update_data, $update_where);
				}
			
				//insert the new data into the db
				$table = BGT_POPUP_OPTION_TABLE; // $wpdb->get_blog_prefix().'sq_popup_option';
			
			
				$wpdb->update($table, $data, array('id' => $op_id));
				echo "Done";
			} catch (Exception $e)
			{
				echo "something wrong!!!";
			}
				
			die();			
		}