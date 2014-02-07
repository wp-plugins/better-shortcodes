<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$option_name   = 'btslb_listTitle';
$option_name_2 = 'btslb_shortcodes';

// For Single site
if ( !is_multisite() ) 
{
    delete_option( $option_name );
		delete_option( $option_name_2 );
} 
// For Multisite
else 
{
    // For regular options.
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
        delete_option( $option_name );
				delete_option( $option_name_2 );  
    }
    switch_to_blog( $original_blog_id );

    // For site options.
    delete_site_option( $option_name );
    delete_site_option( $option_name_2 );  
}
?>