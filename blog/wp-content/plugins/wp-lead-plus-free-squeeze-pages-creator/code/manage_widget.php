<?php
	include_once 'const.php';

	function vgt_ui_widget_manage_page()
	{ ?>
        <div id="vgt_widget_manager">
            <div id="vgt_manager_left">
                <h3 id="vgt_create_option" class="vgt-header"><small>Create an option</small></h3>
                <h3 id="vgt_edit_option" class="vgt-header"><small>Edit existing option</small></h3>

                <div id="vgt_created_options">
                    <label class="label label-info">Select an option to start editing</label>
                    <select class="form-control" id="vgt_list_of_options"></select>
                    <div class="vgt_clear"></div>
                </div>

                <div id="vgt_option_settings">
                    <label class="label label-info" id="vgt_option_label" title="a name which is easy to remember, can be anything">Step 1. Give this option a name</label>
                    <input type="text" id="vgt_option_title" class="form-control" />

                    <label class="label label-info" title="This widget will be displayed when you use this option">Step 2. Select a widget</label>
                    <select class="form-control" id="vgt_selected_widget_id"></select>

                    <label class="label label-info" title="Where do you want to show the widget">Step 3. Where to show the widget</label>
                    <select class="form-control" id="vgt_display_location">
                        <option value=""></option>
                        <option value="everywhere">On all pages/posts</option>
                        <option value="on_posts">On posts only</option>
                        <option value="on_pages">On pages only</option>
                        <option value="on_category">On specific categories</option>
                        <option value="on_shortcode">On specific page/post (use shortcode)</option>

                    </select>
                    <div class="vgt_hidden vgt_manage_extra_settings">
                        <label class="label label-success">Select categories</label>

                        <div id="on_category_value" >
                        </div>

                    </div>

                    <label class="label label-info" title="the position of the widget on your page">Step 4. Position in post/page</label>
                    <select class="form-control" id="vgt_widget_position_in_post">
                        <option value=""></option>
                        <option value="top">Top</option>
                        <option value="bottom">Bottom</option>
                        <option value="top_bottom">Top & Bottom</option>
                        <option value="random">Random</option>
                    </select>


                    <label class="label label-info" title="if you active this option, the currently activated widget will be disabled (replaced)">Step 5. Active now?</label>
                    <select class="form-control" id="vgt_active_widget">
                        <option value=""></option>
                        <option value="activated">Yes</option>
                        <option value="deactivated">No</option>
                    </select>

                    <button id="vgt_widget_save_option" class="btn btn-success">Save Option</button>
                    <button rel="lightcase" href="#vgt_shortcode" id="vgt_get_shortcode" class="btn btn-info"><span class="glyphicon glyphicon-share"></span></button>
                    <button id="vgt_delete_option" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>


                </div> <!-- vgt_option_settings -->

            </div> <!-- vgt_manager_left -->

        </div> <!-- vgt_widget_manager -->

        <div class="vgt_clear"></div>
     <?php  include_once "common.txt"; }