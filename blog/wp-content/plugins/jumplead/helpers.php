<?php

/**
 * Function to print out consitant HTML comments
 *
 * @param string $text Message to add to comment.
 * @return string The HTML comment.
 */
function jumplead_comment($text) {
    return '<!-- Jumplead: ' . $text . '; Wordpress Plugin v' . JUMPLEAD_VERSION . ' -->' . PHP_EOL;
}


/**
 * Function to validate a Jumplead Tracker ID
 * If no tracker ID id give, it'll load the option
 *
 * @param string|null $tracker_id The tracker ID to valid
 * @return boolean True if tracker seems valid, false otherwise
 */
function jumplead_is_tracker_id_valid($tracker_id = null) {
    $tracker_id = trim($tracker_id === null ? get_option('jumplead_tracker_id', '') : $tracker_id);

    if ($tracker_id && is_string($tracker_id) && strlen($tracker_id) > 10) {
        return true;
    }

    return false;
}


