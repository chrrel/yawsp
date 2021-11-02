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
 */
function yawsp_options_page() {
?>
	<div class="wrap">
		<h2>YAWSP - Yet Another Wordpress Security Plugin</h2>
		<p>YAWSP is a minimal plugin for enhancing security and privacy of WordPress websites.</p>
		<h3>Features</h3>
		This plugin provides the following features.
		<h4>Security Hardenings</h4>
		<ol>
			<li>Disable the REST API endpoint wp-json/wp/v2/users to prevent the leakage of usernames</li>
			<li>Disable author archvies completely so that e.g. /?author=1 does not yield a username.</li>
			<li>Replace the author display name with the website tile (e.g. in RSS feeds).</li>
			<li>Create an anti-spam honeypot: Set the "website" field for comments as an invisible field that must not be filled.</li>
			<li>Log failed and successfull logins to the WordPress backend to log files.</li>
			<li>Prevent users from editing source code files using the built-in file editor.</li>
			<li>Add HTTP Security Headers.</li>
			<li>Add output escaping to the_title and the_content to prevent XSS</li>
		</ol>
		<h4>Privacy Improvements</h4>
		<ol>
			<li>Gravatar (used to show avatars for comments) is disabled. Instead, local images are shown.</li>
			<li>Remove cookie consent field in comments so that no cookies are to be saved.</li>
			<li>Replace a comment's IP address with "127.0.0.1" when the comment is approved or classified as spam.</li>
			<li>Replace embedded YouTube videos with youtube-nocookie.com embedds.</li>
		</ol>
		<h4>Security through Obscurity</h4>
		<ol>
			<li>Remove Wordpress version information from ...</li>
			<ul>
				<li>... generator tag in HTML head..</li>
				<li>... generator info in RSS feeds.</li>
				<li>... script and stylesheet links (e.g. `abc.js?ver=1.0.1`).</li>
			</ul>
			<li>Remove links to the REST Api from HTML head and HTTP headers.</li>
			<li>Remove shortlink from HTML head and HTTP headers</li>
			<li>Remove links to `wlwmanifest.xml` and `xmlrpc.php?rsd` from HTML head.</li>
		</ol>
		<h3>Details</h3>
		<p>The source code for this project is available on <a href="https://github.com/chrrel/yawsp">GitHub</a>.</p>
		<p>This plugin is licensed under the <a href="https://www.gnu.org/licenses/gpl-3.0">GNU General Public License v3.0</a>.</p>
	</div>
<?php
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
