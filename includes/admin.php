<?php
// Create settings page
function yawsp_register_options_page() {
	add_options_page('YAWSP', 'YAWSP', 'manage_options', 'yawsp', 'yawsp_options_page');
}
add_action('admin_menu', 'yawsp_register_options_page');

function yawsp_options_page() {
?>
	<div class="wrap">
	<h2>YAWSP - Yet Another Wordpress Security Plugin</h2>
	<p>YAWSP is a minimal plugin for enhancing security and privacy of WordPress websites.</p>
	<form method="post" action="options.php">
	<?php settings_fields( 'myplugin_options_group' ); ?>
	<h3>This is my option</h3>
	<p>Some text here.</p>
	<table>
	<tr valign="top">
	<th scope="row"><label for="myplugin_option_name">Label</label></th>
	<td><input type="text" id="myplugin_option_name" name="myplugin_option_name" value="<?php echo get_option('myplugin_option_name'); ?>" /></td>
	</tr>
	</table>
	<?php submit_button(); ?>
	</form>
	</div>
<?php
}

// Link to settings page from plugins screen
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'yawsp_add_action_links' );
function yawsp_add_action_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=yawsp' ) . '">' . __( 'Settings' ) .' </a>',
    );
    return array_merge( $links, $mylinks );
}

function yawsp_register_settings() {
	add_option( 'myplugin_option_name', 'This is my option value.');
	register_setting( 'myplugin_options_group', 'myplugin_option_name', 'myplugin_callback' );
 }
 add_action( 'admin_init', 'yawsp_register_settings' );
?>
