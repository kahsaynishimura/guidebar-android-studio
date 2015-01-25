<?php
/*
Plugin Name: Jumplead Marketing Software
Plugin URI: http://wordpress.org/extend/plugins/jumplead/
Description: Full Inbound Marketing Automation for WordPress. Visitor ID, Chat, Conversion Forms, email Autoresponders and Broadcasts, Contact CRM and Analytics.
Version: 3.0.8
Author: Jumplead
Author URI: http://jumplead.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
*/

/*  Copyright 2013-2014 Mooloop (hello@mooloop.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Define Constants for Jumplead Plugin
 */
define('JUMPLEAD_VERSION', '3.0.8');
define('JUMPLEAD_PATH', dirname(__FILE__) . '/');
define('JUMPLEAD_PATH_SRC', JUMPLEAD_PATH . 'Jumplead/');
define('JUMPLEAD_PATH_VIEW', JUMPLEAD_PATH . 'views/');

/**
 * Load required files
 */
require_once(JUMPLEAD_PATH . 'helpers.php');
require_once(JUMPLEAD_PATH_SRC . '/Jumplead.php');
require_once(JUMPLEAD_PATH . 'Jumplead/Integration.php');

/**
 * Boot Jumplead and the Integrations
 */
Jumplead::boot();
JumpleadIntegration::boot();

/**
 * Load the frontend functions
 */
require_once(JUMPLEAD_PATH . '/frontend.php');