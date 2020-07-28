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


define('YAWSP_PATH', plugin_dir_path(__FILE__)); 
define('YAWSP_LOG_DIRECTORY', YAWSP_PATH . "log/"); 

# Include the security module
require_once YAWSP_PATH . 'includes/security.php';

