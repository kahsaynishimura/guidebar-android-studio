<?php
/* 	News Press's Search Form
	Copyright: 2014, D5 Creation, www.d5creation.com
	Based on the Simplest D5 Framework for WordPress
	Since NewsPress 1.0
*/
?>


<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="s" class="assistive-text"><?php '' ?></label>
		<input type="text" class="field" name="s" id="s" placeholder="<?php echo 'Search...'; ?>" />
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php echo 'Search...'; ?>" />
	</form>