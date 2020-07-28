<?php

function yawsp_logger($data, $file_path) {
	$logfile = fopen($file_path, "a") or die("Unable to open file!");
	$date = date('Y-m-d H:i:s');
	$row = "$date - $data \n";
	fwrite($logfile, $row);
	fclose($logfile);
}

/* ##################
 Prevent leakage of usernames 
##################
*/

/* Disable the REST API endpoints wp-json/wp/v2/users. */
function yawsp_disable_users_endpoint_in_rest_api( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
}
add_filter('rest_endpoints', 'yawsp_disable_users_endpoint_in_rest_api');

/* Prevent user enumeration via URL /?author=1 by disabling author archives completely (https://wp-mix.com/wordpress-disable-author-archives/) */
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


/* ##################
 Add spam honeypot 
##################
*/

// Use the "website" field in a comment as honeypot. It is set to display:none via css, so that "normal" users do not enter text there.*/
function yawsp_create_spam_comment_honeypot(array $data){
	if(empty($data['comment_author_url'])) {
		return $data;
	} else {
        yawsp_logger($data['comment_post_ID'], YAWSP_LOG_DIRECTORY . "comment-spam.log");
		$message = $admin_email . 'Um Spam zu verhindern, darf das Feld "Website" nicht ausgefüllt werden. Mit einem Klick auf "Zurück" gelangen Sie auf die vorherige Seite. Dort können Sie Ihren Kommentar (ohne Angabe einer Website) erneut absenden.<br><br><a onclick="history.go(-1)" style="cursor:pointer;"><strong>Zurück</strong></a>';
		$website_title = 'Fehler';
		$args = array('response' => 200);
		wp_die($message, $title, $args);
		exit(0);
	}
} 
add_filter('preprocess_comment','yawsp_create_spam_comment_honeypot'); 

/* ##################
 Remove IPs from comments 
##################
*/

// Replace a comment's IP address with "127.0.0.1" when the comment is approved or classified as spam.
function yawsp_remove_ip_from_comment_on_approval($new_status, $old_status, $comment) {
	if(($old_status != $new_status) && ($new_status == 'approved' || $new_status == 'spam')) {
		$modifiedComment = array();
		$modifiedComment['comment_ID'] = $comment->comment_ID;
		$modifiedComment['comment_author_IP'] = "127.0.0.1";
		wp_update_comment($modifiedComment);
	}
}
add_action('transition_comment_status', 'yawsp_remove_ip_from_comment_on_approval', 10, 3);


/* ##################
 Log successfull and failed logins 
##################
*/
function yawsp_login_failed_logger($username) {
	yawsp_logger("$username - authentication failure for ".admin_url(), YAWSP_LOG_DIRECTORY . "logins_failed.log");
}
add_action('wp_login_failed', 'yawsp_login_failed_logger');

function yawsp_login_successful_logger($username) {
	yawsp_logger("$username - login", YAWSP_LOG_DIRECTORY . "logins_successfull.log");
}
add_action('wp_login', 'yawsp_login_successful_logger');

?>
