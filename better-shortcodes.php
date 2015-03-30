<?php
/*
Plugin Name: Better Tinymce Shortcodes List
Plugin URI: http://www.betterweatherllc.com
Description: Creates a list of shortcodes in the tinymce toolbar
Author: Betterweather LLC
Version: 2.2
Author URI: http://www.betterweatherllc.com/
*/
/*  Copyright 2014-2015  Betterweather Inc.  (email : designed@betterweatherllc.com)

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
ob_start(); //used primarily for Backup function
/*
 * Validate Version of WordPress prior to activating
 */ 
	function btslb_versioncheck(){
		global $wp_version;
		
		if(!version_compare($wp_version, "4.1", ">=")){
			die('You must have at least Wordpress Version 4.1 or greater to use the Better Tinymce ShortCode List plugin!');	
		}
	}
	register_activation_hook(__FILE__, "btslb_versioncheck");
/*
 * Deactivate
 */
	function btslb_deactivate(){
		if( !current_user_can('activate_plugins') )
		  return;
	    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	    check_admin_referer( "deactivate-plugin_{$plugin}" );		
	}
	register_deactivation_hook( __FILE__, 'btslb_deactivate' );

/*
 * Uninstall
 */
 function btlsb_uninstall(){
		if( !current_user_can('activate_plugins') )
		return; 	
 }
register_uninstall_hook(plugins_url('/better-shortcodes/uninstall.php'), "btslb_uninstall");

/*
 * Place JS in the Admin Head
 */
 if( is_admin() && ( $pagenow=='post-new.php' OR $pagenow=='post.php' ) ){
	add_action('admin_head', 'better_shortcodes_in_js');
	add_action('admin_head', 'better_tinymce');
 }

/*
 * Pass Options and Variables to betterDrop.js
 */
	function better_shortcodes_in_js(){
		// set JS variables
		$listTitle = trim( esc_attr( get_option('btslb_listTitle') ) );
		if(!$listTitle){
			$listTitle = "ShortCode";
		}
		$shortcodes = esc_attr( get_option('btslb_shortcodes') );
		if($shortcodes==''){
			$shortcodes = '""';
		}
?>
<?php // set the scripts inline with variable values for futher processing ?>
	<script type="text/javascript">
		var listTitle = "<?php echo $listTitle; ?>";
		var btslb_shortcodes = <?php echo html_entity_decode($shortcodes); ?>;
	</script>
<?php

/*
 * Make Drop Down List Work
 */
	function better_tinymce() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
   		return;
    }		
    // verify the post type
    if(!in_array($typenow, array( 'post', 'page' ))){
        return;
		}
		// check if WYSIWYG is enabled
		if ( get_user_option('rich_editing') == 'true' ) {		
			add_filter('mce_external_plugins', 'better_tinymce_plugin');
			add_filter('mce_buttons', 'better_tinymce_button');//TODO: mce_buttons_2 - mce_buttons_4 - if you need it on another row
		}
	}
	 
	function better_tinymce_plugin($plugin_array) {
		$plugin_array['shortcodedrop'] = plugins_url( '/js/betterDrop.js',__FILE__ ); 
		return $plugin_array;
	}
	 
	function better_tinymce_button($buttons) {
		array_push($buttons, 'shortcodedrop');
		return $buttons;
	}
}//end better_shortcodes_in_js

/*
 * Admin Section - 
 * functions and setup for administration of ShortCodes
 */
 
/*
 * Setup Options for transient storage
 */
	function btslb_init(){
		register_setting('btslb_options','btslb_listTitle');
		register_setting('btslb_options','btslb_shortcodes');
	} 
	add_action('admin_init','btslb_init');	

/*
 * add custom JS & CSS files to Admin Pages
 */
 	function load_btslb_admin_scripts() {
		wp_register_style ('btlsbadminstyle',plugins_url('/better-shortcodes/css/jquery-ui-1.10.4.shortcode.css'),false,'1.0');
		wp_register_script('btslbadminscript', plugins_url('/better-shortcodes/js/betterDropAdmin.js'), array('jquery'),'1.0', true); 
		wp_register_script('jquery-ui-shortcode', plugins_url('/better-shortcodes/js/jquery-ui-1.10.4.shortcode.min.js'), array('jquery'),'1.0', true);
		wp_enqueue_style  ('btlsbadminstyle');
		wp_enqueue_script ('jquery-ui-shortcode'); 
		wp_enqueue_script ('btslbadminscript');
	}//End load_btslb_admin_scripts

	function load_btslb_admin_restore() {
		wp_register_style ('btlsbadminstylerestore',plugins_url('/better-shortcodes/css/restore.css'),false,'1.0');
		wp_register_script('validate','http://jqueryvalidation.org/files/dist/jquery.validate.min.js',array('jquery'),'1.0',true);
		wp_register_script('validate_addl','http://jqueryvalidation.org/files/dist/additional-methods.min.js',array('jquery'),'1.0',true);
		wp_register_script('btslbrestorescript', plugins_url('/better-shortcodes/js/restore.js'), array('jquery'),'1.0', true);
		wp_enqueue_style  ('btlsbadminstylerestore');
		wp_enqueue_script ('validate');
		wp_enqueue_script ('validate_addl');
		wp_enqueue_script ('btslbrestorescript');
	}//End load_btslb_admin_restore
	
/*
 * Display the Options form for Shortcodes
 */
	function btslb_option_page(){
		?>
		<?php
		if ( $_GET['settings-updated'] == true) { ?>
		    <div id="message" class="updated">
		        <p>
		        Shortcodes Have Been Updated.
		        </p>
		    </div>
		<?php } ?>
		<div class="wrap">
			<h2>Better Tinymce ShortCode List Options</h2>	
			<p> </p>
			<div class="examples">
				<a href="javascript:void(0)" id="ex" class="button-secondary">Help</a>
				<ul>
					<li>1. <strong>Title for Shortcode:</strong> Any title you want to give the list.</li>
					<li>2. <strong>Add Shortcode</strong> Click this button to add another Shortcode to the list.</li>
					<li>3. <strong>Friendly Name:</strong> This text will appear in the drop down list and should describe the shortcode.</li>
					<li>4. <strong>Shortcode:</strong> Create the shortcode as needed
						<ul>
							<li><strong>one_half</strong> &mdash; will produce: <strong>[one_half][/one_half]</strong></li>
							<li><p class="description">Note: you should <u><strong>NOT</strong></u> use brackets - [ ]</p></li>
						</ul>
					</li>
					<li>5. <strong>Do Not Auto Close:</strong> Check this box if you want to create a shortcode that does NOT require a closing tag
						<ul>
							<li>Checked: <strong>gallery id="1" size="medium"</strong> &mdash; will produce: <strong>[gallery id="1" size="medium"]</strong></li>
							<li>Unchecked: <strong>gallery id="1" size="medium"</strong> &mdash; will produce: <strong>[gallery id="1" size="medium"][/gallery]</strong></li>
						</ul>
					</li>
					<li>6. Remove any unwanted Shortcode with the "Remove" button.</li>
					<li>7. Drag to reorder any of the Shortcodes with the exception of the first Shortcode (default)</li>
				</ul>
			</div>
			<form method="post" id="btslb-options-form" action="options.php">
			<?php settings_fields('btslb_options'); ?>
				<table class="form-table">
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label for="btslb_listTitle">ShortCode List Title: </label></th>				
						<td>
							<input type="text" id="btslb_listTitle" name="btslb_listTitle" class="medium-text" placeholder="ShortCode" value="<?php echo esc_attr(get_option('btslb_listTitle')); ?>" />
							<p class="description">(the default value is "ShortCode")</p>
						</td>
					</tr>
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label></label></th>				
						<td>
							<!--just spacing out-->					
						</td>
					</tr>
					<tr class="even ui-state-disabled" valign="top">
						<th scope="row"><label for="tmp_btslb_freindly">Shortcode(s): </label></th>				
						<td>
							<input type="text" id="tmp_btslb_friendly" name="tmp_btslb_friendly" class="medium-text friendly btslb_n btslb_n_1 btslb-filter" placeholder="Friendly Name"/>
							<input type="text" id="tmp_btslb_shortcodes" name="btslb_shortcodes" class="regular-text shortcode btslb_c btslb_c_1 btslb-filter nospace" placeholder="Shortcode"/>
							<label for="tmp_btslb_do_not_close" class="btslb_checkbox"><input id="tmp_btslb_do_not_close" name="tmp_btslb_do_not_close" type="checkbox" value="1" class="btslb_d btslb_d_1">Do Not Auto Close</label>							
						</td>
					</tr>
				</table>
				<input type="hidden" id="btslb_shortcodes" name="btslb_shortcodes" value="<?php echo esc_attr(get_option('btslb_shortcodes')); ?>" /> 		
				<p class="submit">
					<input type="submit" id="btslb-submit" name="submit" class="button-primary" value="Save Settings" />
					<a href="javascript:void(0)" id="addCode" class="button-primary" style="margin-left:5px;">Add Another Shortcode</a>	
				</p>			
			</form>
		</div>		
		<?php
	}//End btslb_option_page
	
	/*
	 * Setup Admin menu item
	 */
	function btslb_plugin_menu(){
		$btlsb_page 		= add_menu_page('Better Tinymce Shortcodes Settings','Shortcodes','manage_options','btslb-plugin','btslb_option_page');
		$btlsb_bak_page = add_submenu_page('btslb-plugin','Backup','Backup','activate_plugins','shortcode-backup-option','shortcode_backup_option_page');
		$btlsb_res_page = add_submenu_page('btslb-plugin','Restore','Restore','activate_plugins','shortcode-restore-option','shortcode_restore_option_page');


		function shortcode_backup_option_page() {
			// Content Export Feature
		    if ( isset($_POST['backup-shortcode']) ) {
		    	if( check_admin_referer('btslb-backup') ){
		    		?>
			    	<h2>Your download should start soon.</h2>
			    	<?php 
			      // generate the backup file
						$date = date("m-d-Y");
						$json_name = "better-shortcodes-".$date;
						// Get Shotcode List title and Shortcodes
						$options = array('btslb_listTitle' => get_option('btslb_listTitle'), 'btslb_shortcodes' => get_option('btslb_shortcodes'));
						$json_file = json_encode($options);
						// send file to the browser 
						ob_clean();					
							header("Content-Type: text/json; charset=" . get_option( 'blog_charset'));
							header("Content-Disposition: attachment; filename=$json_name.json");
							echo $json_file;
						exit();
					}
		    }
		    else {
		    ?>
	        <div class="wrap">
            <h2>Better Tinymce ShortCode Backup</h2>
            <p>When you click the <strong>Backup Shortcodes</strong> button, the <br /> 
            	process will generate a file for you to save on your computer.</p>
            <p>This backup file contains the Shortcodes configuration for this site.</p>
            <p>After backing up, you can either use the file to restore these <br /> 
            	shortcodes on this site again or another WordPress site that uses <br />
            	the Shortcodes Plugin.</p>
            <form method="post" action="">
              <p class="submit">
                <?php wp_nonce_field('btslb-backup'); ?>
                <input class="button-primary" type="submit" name="export" value="Backup Shortcodes" />
                <input type="hidden" name="backup-shortcode" value="true" />
              </p>
            </form>
	        </div>
	      <?php
		    }
		}//End shortcode_backup_option_page

		function shortcode_restore_option_page() {
	    ?>
	    <div class="wrap">
	    <h2>Better Tinymce ShortCode Restore</h2> 
        <?php
        	$restored = false;
					if ( isset($_FILES['restore']) && check_admin_referer('shortcode-restore') ) {
            if ( $_FILES['restore']['error'] > 0) {
                wp_die("There was an error importing your backup file.");
            }
            else {
              $file_name = $_FILES['restore']['name']; // Get the name of file
              $file_ext  = strtolower(end(explode(".", $file_name))); // Get extension of file
              $file_size = $_FILES['restore']['size']; // Get size of file
              /* Ensure uploaded file is JSON file type and the size not over 500000 bytes
               * You can modify the size you want
               */
              if ( ($file_ext == "json") && ($file_size < 500000) ) {
                  $encode_options = file_get_contents($_FILES['restore']['tmp_name']);
                  $options = json_decode($encode_options, true);
                  foreach ( $options as $key => $value ) {
                      update_option($key, $value);
                  }
                  echo "<div class='updated'><p>All ShortCodes were restored successfully.</p></div>";
                  $restored = true;
              }
              else {//shouldn't really ever happen, but just in case
                  echo "<div class='error'><p>File size too big.</p></div>";
              }
            }
          }
          else {

        ?>
        <ol>
	        <li>Press the <strong>Browse to Select File</strong> button to find and upload<br /> 
	        	the Better Tinymce Shortcodes .json backup file.</li>
	        <li class="step-two">Press the <strong>Restore</strong> button to restore Shortcodes from <br />
	        	the backup file.</li>
      	</ol>
        <form method="post" enctype="multipart/form-data" id="restore-form">
          <p class="submit">
            <?php wp_nonce_field('shortcode-restore'); ?>
            <label id="fileInputLbl" class="file-input button-secondary">
            	Browse to Select File
            	<input type="file" name="restore" id="restore" class="restore-file" />
          	</label>
            <input class="button-primary" type="submit" name="submit" value="Restore" disabled/>
            <p class="description">The file&rsquo;s extension should be <strong>.json</strong><br />
            	<span class="step-two">If you've chosen the wrong file, just click the file name to select another.</span></p>
          </p>
    	<?php } ?>
	    </div>
	    <?php

		}//End shortcode_restore_option_page

		add_action('load-'.$btlsb_page, 'load_btslb_admin_scripts');
		add_action('load-'.$btlsb_res_page, 'load_btslb_admin_restore');

	}//end btslb_plugin_menu
	
	/*
	 * Make Admin Menu Item
	 */
	add_action('admin_menu','btslb_plugin_menu');
	
