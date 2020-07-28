<?php
/*
Plugin Name: YAWSP - Yet Another Wordpress Security Plugin
Description: A minimal plugin for enhancing security and privacy.
Version: 0.1
Author: chrrel
Author URI: http://github.com/chrrel
Text Domain: health-check
Domain Path: /languages
*/

// Define plugin constants
define('YAWSP_PATH', plugin_dir_path(__FILE__)); 
define('YAWSP_LOG_DIRECTORY', YAWSP_PATH . "log/"); 

// Include the security and privacy modules
require_once YAWSP_PATH . 'includes/security.php';
require_once YAWSP_PATH . 'includes/privacy.php';

require_once YAWSP_PATH . 'includes/admin.php';


 define( 'WP_DEBUG', true );

#####
# Datei-Editoren deaktivieren
#define( 'DISALLOW_FILE_EDIT', true );