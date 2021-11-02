<?php
/**
 * This module provides functions for improving the security of a WordPress installation
 * by hiding potentially sensitive data ("security through obscurity").
 *
 * @since 1.1
 */

/**
 * Remove the generator tag containing the Wordpress version from <head>.
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove links to the REST Api from <head> and HTTP headers.
 */
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);

/**
 * Remove the shortlink from <head> and HTTP headers.
 */
remove_action('wp_head', 'wp_shortlink_wp_head', 10);
remove_action( 'template_redirect', 'wp_shortlink_header', 11);

/**
 * Remove links to wlwmanifest.xml and xmlrpc.php?rsd from <head>.
 */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Remove dns-prefetch to s.w.org from <head>.
 */
remove_action('wp_head', 'wp_resource_hints', 2);

/**
 * Remove version information from script and stylesheet links (e.g. abc.js?ver=1.0.1).
 */
function yawsp_remove_css_js_version_info($src) {
	if(strpos($src, '?ver='))
		$src = remove_query_arg('ver', $src);
	return $src;
}
add_filter('style_loader_src', 'yawsp_remove_css_js_version_info', 9999);
add_filter('script_loader_src', 'yawsp_remove_css_js_version_info', 9999);

/**
 * Remove the generator version info from RSS feeds.
 */
add_filter('the_generator', '__return_empty_string');

?>
