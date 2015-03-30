<?php
/*
Plugin Name: Better Tinymce Shortcodes List
Plugin URI: http://www.betterweatherinc.com
Description: Create a shortcodes list for shortcodes in the tinymce toolbar
Author: Betterweather Inc.
Version: 1.0
Author URI: http://www.betterweatherinc.com/
*/
/*  Copyright 2013  Betterweather Inc.  (email : designed@betterweatherinc.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * Validate Version of WordPress prior to activating
 */ 
	function btslb_versioncheck(){
		global $wp_version;
		
		if(!version_compare($wp_version, "3.6", ">=")){
			die('You must have at least Wordpress Version 3.6 or greater to use the Better Tinymce ShortCode List plugin!');	
		}
	}
	register_activation_hook(__FILE__, "btslb_versioncheck");
/*
 * Deactivate
 */
	function btslb_deactivate(){
		if(!current_user_can('activate_plugins'))
		  return;
	    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	    check_admin_referer( "deactivate-plugin_{$plugin}" );		
		}
	register_deactivation_hook( __FILE__, 'btslb_deactivate' );
/*
 * Uninstall
 */
 function btlsb_uninstall(){
		if(!current_user_can('activate_plugins'))
		return; 	
 }
	register_uninstall_hook(plugins_url('/better-shortcodes/uninstall.php'), "btslb_uninstall");
/*
 * Place JS in the Admin Head
 */	
	add_action('admin_head', 'better_shortcodes_in_js');
	add_action('admin_head', 'better_tinymce');

/*
 * Pass Options and Variables to betterDrop.js
 */
	function better_shortcodes_in_js(){
	/*
	 * set JS variables
	 */
		$listTitle = trim(esc_attr(get_option('btslb_listTitle')));
		if(!$listTitle){
			$listTitle = "Shortcode";
		}
		$shortcodes = esc_attr(get_option('btslb_shortcodes'));
		if($shortcodes==''){
			$shortcodes = '""';
		}
?>
	<script type="text/javascript">
	var listTitle = "<?php echo $listTitle; ?>";
	var btslb_shortcodes = <?php echo html_entity_decode($shortcodes); ?>;
	</script>
<?php
}

/*
 * Make Drop Down List Work
 */
	function better_tinymce() {
		add_filter('mce_external_plugins', 'better_tinymce_plugin');
		add_filter('mce_buttons', 'better_tinymce_button');
	}
	 
	function better_tinymce_plugin($plugin_array) {
		$plugin_array['shortcodedrop'] = plugins_url( '/js/betterDrop.js',__FILE__ ); 
		return $plugin_array;
	}
	 
	function better_tinymce_button($buttons) {
		array_push($buttons, 'shortcodedrop');
		return $buttons;
	}

/*
 * Admin Section
 */
 
/*
 * Setup Options for transient storage
 */
	function btslb_init(){
		register_setting('btslb_options','btslb_listTitle');//todo - add sanitization function ", 'functionName'"
		register_setting('btslb_options','btslb_shortcodes');
	} 
	add_action('admin_init','btslb_init');	

/*
 * add custom JS script to Plugin Admin Page
 */
 	function load_btslb_admin_scripts() {
		wp_register_style('btlsbadminstyle',plugins_url('/better-shortcodes/css/jquery-ui-1.10.4.shortcode.css'),false,'1.0');
		wp_register_script('btslbadminscript', plugins_url('/better-shortcodes/js/betterDropAdmin.js'), array('jquery'),'1.0', true); 
		wp_register_script('jquery-ui-shortcode', plugins_url('/better-shortcodes/js/jquery-ui-1.10.4.shortcode.min.js'), array('jquery'),'1.0', true);
		wp_enqueue_style('btlsbadminstyle');
		wp_enqueue_script('jquery-ui-shortcode'); 
		wp_enqueue_script('btslbadminscript');
	}
	
/*
 * Display the Options form for Shortcodes
 */
	function btslb_option_page(){
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Better Tinymce ShortCode List Options</h2>	
			<p>Here you can set or edit the fields needed for the plugin.</p>
			<form action="options.php" method="post" id="btslb-options-form">
			<?php settings_fields('btslb_options'); ?>
				<table class="form-table">
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label for="btslb_listTitle">The Title for the Short Code List: </label></th>				
						<td>
							<input type="text" id="btslb_listTitle" name="btslb_listTitle" class="medium-text" value="<?php echo esc_attr(get_option('btslb_listTitle')); ?>" />
							<p class="description">(Default is "Shortcode")</p>
						</td>
					</tr>
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label></label></th>				
						<td>
							<a href="" id="addCode">Add Another Short Code</a>						
						</td>
					</tr>
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label for="tmp_btslb_freindly">Short Code(s): </label></th>				
						<td>
							<input type="text" id="tmp_btslb_friendly" name="tmp_btslb_friendly" class="medium-text friendly btslb_n btslb_n_1 btslb-filter" placeholder="Friendly Name"/>
							<input type="text" id="tmp_btslb_shortcodes" name="btslb_shortcodes" class="regular-text shortcode btslb_c btslb_c_1 btslb-filter nospace" placeholder="Short Code"/>							
						</td>
					</tr>
				</table>
				<input type="hidden" id="btslb_shortcodes" name="btslb_shortcodes" value="<?php echo esc_attr(get_option('btslb_shortcodes')); ?>" /> 		
				<p class="submit"><input type="submit" id="btslb-submit" name="submit" class="button-primary" value="Save Settings" /></p>			
			</form>
		</div>		
		<?php
	}
	
	/*
	 * Setup Admin menu item
	 */
	function btslb_plugin_menu(){
		$btlsb_page = add_options_page('Better Tinymce Shortcodes Settings','Shortcodes','manage_options','btslb-plugin','btslb_option_page');
		add_action('load-'.$btlsb_page, 'load_btslb_admin_scripts');
		
	}
	
	/*
	 * Make Admin Menu Item
	 */
	add_action('admin_menu','btslb_plugin_menu');
	
?>