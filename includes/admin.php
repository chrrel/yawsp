<?php
/**
 * This module provides the admin interface for the WordPress backend.
 *
 * @since 1.0
 */

/**
 * Register the plugin's settings page.
 */
function yawsp_register_options_page() {
	add_options_page('YAWSP', 'YAWSP', 'manage_options', 'yawsp', 'yawsp_options_page');
}
add_action('admin_menu', 'yawsp_register_options_page');

/**
 * Create the plugin's settings page content.
 * Read the README file and transform its Markdown content to HTML.
 */
function yawsp_options_page() {
	$readme = file_get_contents(YAWSP_PATH . 'README.md');

	$readme = preg_replace('/###(.*)/', '<h3>$1</h3>', $readme);
	$readme = preg_replace('/##(.*)/', '<h2>$1</h2>', $readme);
	$readme = preg_replace('/#(.*)/', '<h1>$1</h1>', $readme);
	$readme = preg_replace('/\*+(.*)?/i', '<ol><li>$1</li></ol>', $readme);
	$readme = preg_replace('/(\<\/ol\>\n(.*)\<ol\>*)+/', '', $readme);
	
	echo '<div class="wrap">' . $readme . '</div>';
}

/**
 * Add a link to the settings page from plugins screen.
 */
add_filter('plugin_action_links_' . YAWSP_BASENAME, 'yawsp_add_action_links');
function yawsp_add_action_links($links) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=yawsp' ) . '">' . __('Settings') .' </a>',
	);
	return array_merge($links, $mylinks);
}

?>
