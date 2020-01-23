<?php

/**
 *
 * @package alexa_meta_boxes
 */


// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//uninstall functions
include_once plugin_dir_path( __FILE__ ) . "uninstall-functions.php";


deleteOptionsAndMetaInfo();