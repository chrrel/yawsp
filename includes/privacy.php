<?php
/* ##################
 Disable Gravatar 
##################
*/

// Prevent calls to gravatar.com by showing one of 15 local random avatars instead. The avatar is chosen based on the hash value of the comment's email address.
function yawsp_replace_gravatar_with_own_images($url, $id_or_email, $args) {
	if ($id_or_email == intval($id_or_email)) {
		$comment = get_comment($id_or_email);
		$email = $comment->comment_author_email;
	}
	else {
		$email = $id_or_email;
    }

	$base_url = plugin_dir_url( __DIR__ ) .'/images/avatars/avatar';
	$images = array ("01.png", "02.png", "03.png", "04.png", "05.png", "06.png", "07.png", "08.png", "09.png", "10.png", "11.png", "12.png", "13.png", "14.png", "15.png");   

	$hash = crc32($email);
	$index = $hash % count($images);

	return $base_url . $images[$index]; 
}
add_filter('get_avatar_url', 'yawsp_replace_gravatar_with_own_images', 10, 3);


/* ##################
 Remove Cookie Consent Field in Comments 
##################
*/
function yawsp_remove_comment_cookie_field($fields) {	
	unset($fields['cookies']);		
	return $fields;
}
add_filter('comment_form_default_fields', 'yawsp_remove_comment_cookie_field');


/* ##################
 Replace embedded YouTube videos with youtube-nocookie.com embedds
##################
*/
function yawsp_replace_youtube_nocookie_oembed( $html ) {
	return str_replace('youtube.com', 'youtube-nocookie.com', $html);
}
add_filter('embed_oembed_html', 'yawsp_replace_youtube_nocookie_oembed');

?>
