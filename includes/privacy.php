<?php
/**
 * This module provides functions for protecting the privacy of users and GDPR compliance.
 *
 * @since 1.0
 */

/**
 * Disable Gravatar.
 * 
 * Prevent calls to gravatar.com by showing one of 15 local random avatars instead. 
 * The avatar is chosen based on the hash value of the commenter's email address.
 */
function yawsp_replace_gravatar_with_own_images($url, $id_or_email, $args) {
	if(is_numeric($id_or_email)) {
		$id = (int) $id_or_email;
		# by user_id
		if(!empty(get_user_by('id', $id)->user_email)) {
			$email = get_user_by('id', $id)->user_email;
		}
		# by comment_ID
		else {
			$email = get_comment($id)->comment_author_email;
		}
	}
	# by WP_User object
	else if(is_a($id_or_email, 'WP_User')) {
		$email = $id_or_email->user_email;
	}
	# by WP_Comment object
	else if(is_a($id_or_email, 'WP_Comment')) {
		$email = $id_or_email->comment_author_email;
	}
	# by WP_POST object
	else if(is_a($id_or_email, 'WP_POST')) {
		$id = $id_or_email->post_author;
		$email = get_user_by('id', $id)->user_email;
	}
	# by gravatar md5 hash
	else if(strlen($id_or_email) == 32 && ctype_xdigit($id_or_email)) {
		$email = $id_or_email;
	}
	# by user email
	else if(filter_var($id_or_email, FILTER_VALIDATE_EMAIL)) {
		$email = get_user_by('email', $id_or_email)->user_email;
	}
	else {
		$email = "usernotfound@example.org";
	}

	$base_url = plugin_dir_url( __DIR__ ) .'images/avatars/avatar';
	$images = array ('01.png', '02.png', '03.png', '04.png', '05.png', '06.png', '07.png', '08.png', '09.png', '10.png', '11.png', '12.png', '13.png', '14.png', '15.png');

	$hash = crc32($email);
	$index = $hash % count($images);

	return $base_url . $images[$index]; 
}
add_filter('get_avatar_url', 'yawsp_replace_gravatar_with_own_images', 10, 3);


/**
 * Remove cookie consent field in comments.
 */
function yawsp_remove_comment_cookie_field($fields) {	
	unset($fields['cookies']);		
	return $fields;
}
add_filter('comment_form_default_fields', 'yawsp_remove_comment_cookie_field');

/**
 * Remove IPs and e-mail addresses from comments.
 * 
 * Replace a comment's IP address with 127.0.0.1 and the e-mail address with comment-<ID>@example.com
 * when the comment is approved or classified as spam.
 */
function yawsp_remove_ip_and_email_from_comment_on_approval($new_status, $old_status, $comment) {
	if(($old_status != $new_status) && ($new_status == 'approved' || $new_status == 'spam')) {
		$modifiedComment = array();
		$modifiedComment['comment_ID'] = $comment->comment_ID;
		$modifiedComment['comment_author_IP'] = '127.0.0.1';
		$modifiedComment['comment_author_email'] = 'comment-'. $comment->comment_ID .'@example.com';
		wp_update_comment($modifiedComment);
	}
}
add_action('transition_comment_status', 'yawsp_remove_ip_and_email_from_comment_on_approval', 10, 3);

/**
 * Replace embedded YouTube videos with youtube-nocookie.com embedds.
 */
function yawsp_replace_youtube_nocookie_oembed($html) {
	return str_replace('youtube.com', 'youtube-nocookie.com', $html);
}
add_filter('embed_oembed_html', 'yawsp_replace_youtube_nocookie_oembed');

?>
