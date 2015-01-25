<?php
/**
 * Jumplead
 */

class Jumplead
{
    /**
     * Plugin's name
     */
    static $plugin = 'jumplead/jumplead.php';

    /**
     * Path to Jumplead Plugin's folder
     */
    static $path = '';

    /**
     * DB table name for field mapping
     */
    static $tableFieldMapping = '';

    /**
     * Data to be used for triggering automations
     */
    static $data = array();


    /**
     * Boot up Jumplead Plugin
     * Sets up static variables, hooks and styles
     */
    static function boot()
    {
        global $wpdb;

        // Static Variables
        self::$path = plugins_url('/', self::$plugin);
        self::$tableFieldMapping = $wpdb->prefix . 'jumplead_mapping';

        // Install
        register_activation_hook(self::$plugin, 'Jumplead::activate');
        register_deactivation_hook(self::$plugin, 'Jumplead::deactivate');

        // Admin
        add_action('admin_menu', 'Jumplead::adminMenu');

        // Styles
        add_action('admin_enqueue_scripts', 'Jumplead::styles');

        // Filters
        add_action('load-jumplead_page_jumplead_integrations', 'Jumplead::filterHasTrackerId');
    }

    /**
     * Add Jumple to Admin menu, along with subpages
     *
     * @return void
     */
    static function adminMenu()
    {
        $icon = plugins_url('jumplead/assets/jumplead-icon.png');

        // Main Menu
    	add_menu_page('Jumplead', 'Jumplead', 'edit_pages', 'jumplead', 'Jumplead::showPageJumplead', $icon);

        // Subpages
    	add_submenu_page('jumplead', 'Integrations', 'Integrations', 'edit_pages', 'jumplead_integrations', 'Jumplead::showPageIntegrations');
    	add_submenu_page('jumplead', 'Settings', 'Settings', 'edit_pages', 'jumplead_settings', 'Jumplead::showPageSettings');
    }

    /**
     * Add Jumplead admin styles
     *
     * @return void
     */
    static function styles()
    {
        wp_enqueue_style('jumplead_styles', self::$path . 'c/jumplead.css', array('dashicons'), JUMPLEAD_VERSION);
    }


    /**
     * Check for tracker ID, redirect to settings if not set
     *
     * @return void
     */
    static function filterHasTrackerId()
    {

        if (!jumplead_is_tracker_id_valid()) {
            wp_redirect(admin_url('admin.php?page=jumplead_settings'));
            exit;
        }
    }

    // Page Controllers

    /**
     * Jumplead Landing Page
     *
     * @return void
     */
    static function showPageJumplead()
    {
        $h2 = 'Jumplead';
	    include(JUMPLEAD_PATH_VIEW . 'jumplead.php');
    }

    /**
     * Jumplead Settings Page
     *
     * @return void
     */
    static function showPageSettings()
    {
        $h2 = 'Jumplead Settings';

        $errors = array();
        $info = array();

        // Update?
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Comments
            $capture_comments = isset($_POST['capture_comments']) ? (bool) $_POST['capture_comments'] : false;
            update_option('jumplead_capture_comments', $capture_comments);

            // Tracker
            if (isset($_POST['tracker_id']) && jumplead_is_tracker_id_valid($_POST['tracker_id']) ) {
        	    update_option('jumplead_tracker_id', trim($_POST['tracker_id']));
        	    $info[] = 'Settings Saved!';
        	} else {
        	    $errors[] = 'Tracker ID is not valid.';
        	}
        }

        // Settings for view
        $tracker_id         = get_option('jumplead_tracker_id', null);
        $capture_comments   = get_option('jumplead_capture_comments', false);
        $tracker_id_valid   = jumplead_is_tracker_id_valid($tracker_id);

        // Prompt for Tracker ID
        if (empty($errors) && empty($info) && !$tracker_id_valid) {
            $info[] = 'Please enter your Jumplead Tracker ID to use Jumplead.';
        }

        // View
	    include(JUMPLEAD_PATH_VIEW . 'settings.php');
    }

    /**
     * Jumplead Integrations Pages
     * - Acts as a route for Jumplead Integration pages.
     * - $_GET['subpage'] defines what page to load
     *
     * @return void
     */
    static function showPageIntegrations()
    {
        $page = isset($_GET['subpage']) ? $_GET['subpage'] : null;

        switch ($page) {
            case 'mapping':
                self::showPageIntegrationsMapping();
                break;
            default:
                self::showPageIntegrationsIndex();
        }
    }

    /**
     * Jumplead Integrations Page
     * - Lists forms from active integrated plugins.
     * - Handles unlinking of forms.
     *
     * @return void
     */
    static function showPageIntegrationsIndex()
    {
        $h2 = 'Jumplead Integrations';

        // Bulk Actions
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Top Bulkaction dropdown
            $bulkaction = isset($_POST['bulkaction']) ? $_POST['bulkaction'] : null;
            if (!$bulkaction) {
                // Bottom Bulkaction dropdown
                $bulkaction = isset($_POST['bulkaction2']) ? $_POST['bulkaction2'] : null;
            }
            $forms      = isset($_POST['forms']) ? $_POST['forms'] : null;

            if ($bulkaction) {
                switch ($bulkaction) {
                    case 'unlink':
                        JumpleadIntegration::unlinkMappings($forms);
                }

            }
        }

        // Show list
        $activeIntegrations     = JumpleadIntegration::getActive();
        $mappings               = JumpleadIntegration::getAllMappings();

        // Set up array of mappings for easy use
        $mappingsLookup = array();

        foreach ($mappings as $mapping) {
            $key = (string) $mapping->integration_id . '_' . (string) $mapping->form_id;
            $mappingsLookup[$key] = $mapping;
        }

        // Count the forms
        $formCount = 0;
        $formsLookup = array();
        foreach ($activeIntegrations as $integration) {
            $forms = $integration->listForms();
            $formsLookup[$integration->id] = $forms;
            $formCount += count($forms);
        }

	    include(JUMPLEAD_PATH_VIEW . 'integrations.php');
    }

    /**
     * Jumplead Integrations Mapping Page (Subpage of Jumplead Integrations)
     * - Sets up mappings
     * - Sets automation
     *
     * @return void
     */
    static function showPageIntegrationsMapping()
    {
        $h2 = 'Jumplead Integrations Mapping';

        $errors = array();
        $info = array();

        $integrationId  = sanitize_key($_GET['integration']);
        $formId         = (int) $_GET['form_id'];

        $integration = JumpleadIntegration::getById($integrationId);

        // We found integration
        if ($integration) {
            $form = $integration->getForm($formId);

            // We found form
            if ($form) {
                $mapping = $integration->getMapping($formId);

                if (!$mapping) {
                    $mapping = new stdClass();
                    $mapping->automation_id = '';
                }

                // Update mapping?
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Get automation_id
                    $mapping->automation_id = $_POST['automation_id'];

                    // Loop the fields
                    foreach (JumpleadIntegration::$fields as $field) {
                        $id = $field['id'];
                        $mapping->$id = isset($_POST[$id]) ? $_POST[$id] : null;

                        if ($field['required']) {
                            if (!$mapping->$id || strlen($mapping->$id) < 1) {
                                $errors[] = $field['name'] . ' is required.';
                            }
                        }

                        // Sub Fields
                        if (isset($field['sub'])) {
                            foreach ($field['sub'] as $subField) {
                                $id = $subField['id'];
                                $mapping->$id = isset($_POST[$id]) ? $_POST[$id] : null;
                            }
                        }
                    }

                    // No Errors, then save
                    if (empty($errors)) {
                        $response = $integration->saveMapping($formId, $mapping);

                        if ($response === false) {
                            $errors[] = 'Could not save integration.';
                        } else {
                            $info[] = 'Integration saved!';
                        }
                    }
                }

    	        include(JUMPLEAD_PATH_VIEW . 'integrations_mapping.php');
    	    }
        }
    }

    // Plugin Management

    /**
     * Plugin activation hook.
     *
     * @return void
     */
    static function activate()
    {
        self::handleMultiSite('activate');
    }

    /**
     * Plugin deactivation hook.
     *
     * @return void
     */
    static function deactivate()
    {
        self::handleMultiSite('deactivate');
    }

    /**
     * Installation script
     * - Creates DB tables
     * - Sets Jumplead verion in DB
     *
     * @return void
     */
    static function install()
    {
        if (JUMPLEAD_VERSION != get_site_option('jumplead_version')) {
    	    global $wpdb;

        	$charset_collate = '';

        	if (!empty($wpdb->charset)) {
        	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        	}

        	if (!empty( $wpdb->collate)) {
        	  $charset_collate .= " COLLATE {$wpdb->collate}";
        	}

            // Mappings
            $sql = "CREATE TABLE " . self::$tableFieldMapping . " (
                `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `integration_id` varchar(100) NOT NULL,
                `form_id` int NOT NULL,
                `automation_id` varchar(100),
                `name` varchar(255),
                `name_last` varchar(255),
                `email` varchar(255),
                `company` varchar(255)
            ) $charset_collate;";

        	dbDelta($sql);

            update_option('jumplead_version', JUMPLEAD_VERSION);
        }
    }

    /**
     * Installation script
     * - Drops DB tables
     * - Unsets Jumplead options
     *
     * @return void
     */
    static function uninstall()
    {
        global $wpdb;

        delete_option('jumplead_version');
        delete_option('jumplead_tracker_id');
        delete_option('jumplead_capture_comments');

        $wpdb->query('DROP TABLE IF EXISTS ' . self::$tableFieldMapping);
    }

    /**
     * Runs an aution on single and multi-site installs
     *
     * @return void
     */
    static function handleMultiSite($action)
    {
        // Active
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        if (function_exists('is_multisite') && is_multisite()) {
            // Is it networkwide?
            if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {

                $current = $wpdb->blogid;

                // Get all blog ids
                $blogIds = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                foreach ($blogIds as $blogId) {
                    switch_to_blog($blogId);

                    if ($action == 'activate') {
                        self::install();
                    } else if ($action == 'deactivate') {
                        self::uninstall();
                    }
                }

                // Switch back to current WordPress
                switch_to_blog($current);
                return;
            }
        }

        // Current WordPress only
        if ($action == 'activate') {
            self::install();
        } else if ($action == 'deactivate') {
            self::uninstall();
        }
    }
}
