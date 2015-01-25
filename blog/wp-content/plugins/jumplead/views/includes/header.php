<div class="wrap jumplead">
    <header id="jumplead_header">
        <div class="links">
<?php
if (!get_option('jumplead_tracker_id', null)) {
?>
            <a href="http://jumplead.com/join-us" target="_blank">Create Free Account</a>
<?php
}
?>
            <a href="http://app.jumplead.com" target="_blank">Login</a>
        </div>

        <h2>
            <img id="jumplead_logo" src="<?php echo Jumplead::$path; ?>/assets/robot-white.png" />
            <?php echo isset($h2) ? $h2 : 'Jumplead'; ?>
        </h2>
    </header>
<?php
include(JUMPLEAD_PATH_VIEW . 'includes/message.php');
?>