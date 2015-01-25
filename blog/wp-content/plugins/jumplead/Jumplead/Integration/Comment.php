<?php

/**
 * Create Contacts from Commenters
 */

class JumpleadIntegrationComment extends JumpleadIntegration {

    function __construct($data)
    {
        parent::__construct($data);

        // Hooks
        if (get_option('jumplead_capture_comments', false)) {
            add_action('wp_insert_comment', array($this, 'capture'), 10, 2);
        }
    }

    /**
     * Captures data from comment submission and saves to cookie
     *
     * @return void
     */
    function capture($id, $data) {
        if (isset($data->comment_author) && isset($data->comment_author_email)) {

            $data = array(
                'name'  => $data->comment_author,
                'email' => $data->comment_author_email
            );

            JumpleadIntegration::saveCookies($data);
        }
    }
}
