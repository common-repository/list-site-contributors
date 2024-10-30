<?php
/*
Uninstall procedure (Removes the plugin cleanly)
*/


// Checks if it is accessed from Wordpress Admin
if ( ! function_exists( 'add_action' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
	
}


// Make sure that we are uninstalling
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}


// Delete options from DB
delete_option( 'lsctrib' );
delete_option( 'lsctribmax' ); // 01-10-2013
delete_option( 'lsctriball' ); // 01-24-2013
delete_option( 'lsctribsupp' ); // 01-31-2013
delete_option( 'lsctribexc' ); // 02-15-2014
delete_option( 'lsctriblb' );  // 03-21-2015
delete_option( 'lsctribimg' );  // 12-03-2015

// Bye! See you soon!

?>
