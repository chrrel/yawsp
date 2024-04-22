<?php
/**
 * This module provides functions for improving the security of a WordPress installation.
 *
 * @since 1.0
 */

/**
 * Logging function.
 * 
 * Log $data together with the current date to the file given by $file_path.
 */
function yawsp_logger($data, $file_path) {
	$logfile = fopen($file_path, 'a') or die('Unable to open file');
	$date = date('Y-m-d H:i:s');
	$row = "$date - $data \n";
	fwrite($logfile, $row);
	fclose($logfile);
}

/**
 * Disable the REST API endpoint wp-json/wp/v2/users to prevent the leakage of usernames.
 */
function yawsp_disable_users_endpoint_in_rest_api($endpoints) {
	if(isset($endpoints['/wp/v2/users'])) {
		unset($endpoints['/wp/v2/users']);
	}
	if(isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
		unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
	}
	return $endpoints;
}
add_filter('rest_endpoints', 'yawsp_disable_users_endpoint_in_rest_api');

/**
 * Prevent user enumeration via the author archive URL.
 * 
 * Disable author archvies completely so that e.g. /?author=1 does not yield a username.
 * Details: https://wp-mix.com/wordpress-disable-author-archives/
 */
function yawsp_disable_author_archives() {
	if (is_author()) {
		global $wp_query;
		$wp_query->set_404();
		status_header(404);
	} else {
		redirect_canonical();
	}
}
remove_filter('template_redirect', 'redirect_canonical');
add_action('template_redirect', 'yawsp_disable_author_archives');

/**
 * Prevent the actual author display name from being displayed in the frontend.
 * This removes the name e.g. from news posts and the RSS feed and replaces it
 * with the website's title.
 */
function yawsp_replace_author_display_name($display_name) {
	if(is_admin()) {
		return $display_name;
	} else {
		return get_bloginfo();
	}
}
add_filter('the_author', 'yawsp_replace_author_display_name');

/**
 * Disable the user sitemap /wp-sitemap-users-1.xml to prevent the leakage of usernames.
 */
function yawsp_disable_user_sitemap($provider, $name) {
	if ('users' === $name) {
		return false;
	}
	return $provider;
}
add_filter('wp_sitemaps_add_provider', 'yawsp_disable_user_sitemap', 10, 2);

/**
 * Modify the login error message to prevent user enumeration based on different error messages 
 * for existing/non-existing user accounts.
 */
function yawsp_disable_login_errors($errors) {
	if (str_contains($errors, 'Too many login attempts')) {
		return $errors;
	}
	return sprintf(__('<strong>Error:</strong> The password you entered for the username %s is incorrect.'), '') 
	.' <a href="' . wp_lostpassword_url() . '">' . __( 'Lost your password?' ) . '</a>';
}
add_filter('login_errors', 'yawsp_disable_login_errors', 30, 1);

/**
 * Disable custom CSS classes for author comments containing user names.
 * Example: comment-author-[USERNAME]
 */
function yawsp_disable_comment_author_class( $classes ) {
	foreach($classes as $key => $class ) {
		if(strstr($class, 'comment-author-')) {
			unset($classes[$key]);
		}
	}
	return $classes;
}
add_filter('comment_class', 'yawsp_disable_comment_author_class');

/**
 * Create an anti-spam honeypot.
 * 
 * Use the "website" field in a comment as honeypot. It is set to display:none via css, 
 * so that "normal" users do not enter text there.
 */
function yawsp_create_spam_comment_honeypot(array $data){
	if(empty($data['comment_author_url'])) {
		return $data;
	} else {
		yawsp_logger($data['comment_post_ID'], YAWSP_LOG_DIRECTORY . 'comment-spam.log');
		$message = 'Um Spam zu verhindern, darf das Feld "Website" nicht ausgefüllt werden. Mit einem Klick auf 
		"Zurück" gelangen Sie auf die vorherige Seite. Dort können Sie Ihren Kommentar (ohne Angabe einer Website)
		erneut absenden.<br><br><a onclick="history.go(-1)" style="cursor:pointer;"><strong>Zurück</strong></a>';
		$website_title = 'Fehler';
		$args = array('response' => 200);
		wp_die($message, $website_title, $args);
		exit(0);
	}
} 
add_filter('preprocess_comment','yawsp_create_spam_comment_honeypot'); 

/**
 * Log failed login attempts to the WordPress backend.
 */
function yawsp_login_failed_logger($username) {
	yawsp_logger("$username - authentication failure for ".admin_url(), YAWSP_LOG_DIRECTORY . 'logins_failed.log');
}
add_action('wp_login_failed', 'yawsp_login_failed_logger');

/**
 * Log successful logins to the WordPress backend.
 */
function yawsp_login_successful_logger($username) {
	yawsp_logger("$username - login", YAWSP_LOG_DIRECTORY . 'logins_successfull.log');
}
add_action('wp_login', 'yawsp_login_successful_logger');

/**
 * Disallow file edits from WordPress.
 * 
 * Prevent users from editing source code files using the built-in file editor.
 */
define('DISALLOW_FILE_EDIT', true);

/**
 * Add HTTP security headers.
 */
function yawsp_enable_http_security_headers($headers) {
	$headers['X-Frame-Options'] = 'deny';
	$headers['X-XSS-Protection'] = '1; mode=block';
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
	if (!empty($_SERVER['HTTPS'])) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
	}
	return $headers;
}
add_filter('wp_headers', 'yawsp_enable_http_security_headers');

/**
 * Add escaping for the_title() and the_content().
 */
# Make the function the_tile() use esc_html() to encode output
add_filter('the_title', 'esc_html');

/**
 * Disable XML-RPC.
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Limit login attempts for a user within a specified time frame.
 *
 * Count failed login attempts for each user by using transients and return an error message when
 * the maximum number is reached.
 * 
 * Inspired by: https://phppot.com/wordpress/how-to-limit-login-attempts-in-wordpress/
 *
 */
function yawsp_authenticate_limit_login_attempts($user, $username, $password) {
	$username_hash = hash('sha256', $username);
	$transient_name = 'login_attempts_' . $username_hash;
	$max_attempts = 3;

	$attempts = get_transient($transient_name) ?: 0;

	if ($attempts < $max_attempts) {
		# Reset the attempt count for successful logins, increment for failed ones
		if ($user instanceof WP_User) {
			$attempts = 0;
		} else {
			$attempts++;
		}
		set_transient($transient_name, $attempts, 600);
		return $user;
	} else {
		$expiration_time = get_option('_transient_timeout_' . $transient_name);
		$seconds_left = max(0, $expiration_time - time());
		$message = "<strong>Error:</strong> Too many login attempts. Please wait $seconds_left seconds before trying again.";
		return new WP_Error('yawsp_too_many_login_attempts', $message);
	}
}
add_filter('authenticate', 'yawsp_authenticate_limit_login_attempts', 50, 3);

?>
