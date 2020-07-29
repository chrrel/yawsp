<?php
/**
 * Plugin Name:       YAWSP - Yet Another Wordpress Security Plugin
 * Plugin URI:        http://github.com/chrrel/yawsp
 * Description:       A minimal plugin for enhancing security and privacy.
 * Version:           1.0
 * Requires at least: 5.4.2
 * Author:            chrrel
 * Author URI:        http://github.com/chrrel
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0
 * Text Domain:       yawsp
 * Domain Path:       /languages
 */

/* Prevent direct access to plugin files */
defined('ABSPATH') or die('Stop here.');

/* Define plugin constants */
define('YAWSP_PATH', plugin_dir_path(__FILE__)); 
define('YAWSP_LOG_DIRECTORY', YAWSP_PATH . "log/"); 

/* Load all features from the corresponding modules */
require_once YAWSP_PATH . 'includes/security.php';
require_once YAWSP_PATH . 'includes/privacy.php';
require_once YAWSP_PATH . 'includes/admin.php';
