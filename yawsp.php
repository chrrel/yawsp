<?php
/**
 * Plugin Name:       YAWSP - Yet Another Wordpress Security Plugin
 * Plugin URI:        https://github.com/chrrel/yawsp
 * Description:       A minimal plugin for enhancing security and privacy.
 * Version:           1.3
 * Update URI:        false
 * Requires at least: 5.8.1
 * Author:            chrrel
 * Author URI:        https://github.com/chrrel
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0
 * Text Domain:       yawsp
 * Domain Path:       /languages
 *
 * YAWSP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 as 
 * published by the Free Software Foundation.
 * 
 * YAWSP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with YAWSP. If not, see https://www.gnu.org/licenses/gpl-3.0.
 */

/* Prevent direct access to plugin files */
defined('ABSPATH') or die('Stop here.');

/* Define plugin constants */
define('YAWSP_PATH', plugin_dir_path(__FILE__));
define('YAWSP_BASENAME', plugin_basename(__FILE__)); 
define('YAWSP_LOG_DIRECTORY', YAWSP_PATH . 'log/'); 

/* Load all features from the corresponding modules */
require_once YAWSP_PATH . 'includes/security.php';
require_once YAWSP_PATH . 'includes/privacy.php';
require_once YAWSP_PATH . 'includes/obscurity.php';
require_once YAWSP_PATH . 'includes/admin.php';

?>
