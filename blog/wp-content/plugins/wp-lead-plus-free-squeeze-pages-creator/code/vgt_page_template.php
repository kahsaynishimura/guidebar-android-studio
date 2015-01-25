<?php
/**
 * Template Name: WP Lead Plus Template
 *
 * Description: Unique template of WP Lead Plus to display its squeeze page
 */
remove_filter( 'the_content', 'wpautop' );

//get current post id
$id = get_the_ID();

function vgt_write_custom_js($id, $position)
{
    if (get_post_meta($id, "vgt_custom_js_code_position", true) == $position)
    {
        echo vgt_de_serialize_data(get_post_meta($id, "vgt_custom_js_code", true));
    }
}

?>

<html>

<head>
    <?php vgt_write_custom_js($id, "after_head_open"); ?>

    <meta charset="utf-8" />
    <title><?php echo the_title(); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo get_option('vgt_wpl_plugin_url') . '/css/front.css'; ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo get_option('vgt_wpl_plugin_url') . '/css/button-styles.css'; ?>" />
    <style> <?php echo vgt_de_serialize_data(get_post_meta($id, "vgt_css_content", true)); ?></style>

    <style> <?php echo vgt_de_serialize_data(get_post_meta($id, "vgt_custom_css_code", true)); ?></style>

    <?php vgt_write_custom_js($id, "before_head_close"); ?>
    <script src="<?php echo get_option('vgt_wpl_plugin_url') . '/js/jq.js' ?>"></script>
    <!-- include tracking js -->
    <script src="<?php echo get_option('vgt_wpl_plugin_url') . '/js/backs.js' ?>"></script>
    <script src="<?php echo get_option('vgt_wpl_plugin_url') . '/js/front.js' ?>"></script>

    <?php echo vgt_de_serialize_data(get_post_meta($id, "vgt_custom_tracking_code", true)); ?>
    <?php vgt_write_custom_js($id, "before_head_close"); ?>
</head>


<?php
$background_type = get_post_meta($id, "vgt_outer_background_type", true);
$background_value = get_post_meta($id, "vgt_outer_background", true);
if ( $background_type == "image_pattern")
{
    echo "<body style='background: url(".$background_value.");'";
} else if ($background_type == "image")
{
    echo "<body>";
    echo "<script>jQuery(document).ready(function(){ jQuery('body').backstretch('".$background_value."'); });</script>";
} else if ($background_type == "color")
{
    echo "<body style='background: ".$background_value.";'";
} else
{
    echo "<body>";
}
    ?>
<!-- custom span used for tracking -->
<?php
echo '<span item_type="squeeze" for_item="'.get_post_meta($id, 'vgt_page_outer_id', true).'" ab_id="0" option_id="0" style="display: none;" item_id="'.$id.'"></span>';

?>

<?php vgt_write_custom_js($id, "after_body_open"); ?>

<?php while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
<?php endwhile; // end of the loop. ?>




<span style="display: none;" class="vgt_ajax_url"><?php echo get_option("vgt_custom_ajax_url"); ?></span>
<?php vgt_write_custom_js($id, "before_body_close"); ?>
<script>
    jQuery("*").removeAttr("contenteditable");

</script>
</body>

</html>