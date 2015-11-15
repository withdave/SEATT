<?php
/*
Plugin Name: Simple Event Attendance (SEATT)
Version: 1.3.0
Plugin URI: http://www.3cc.org/scripts/wp-seatt-simple-event-attendance/
Author: Dave Channon
Author URI: http://www.3cc.org
Description: Simple attendance list, multiple lists can be added to any post or page and subscribed members can be edited.
*/
global $seatt_db_version;
$seatt_db_version = "1.1.2";
include('seatt_events_include.php');

function seatt_install() {
	global $wpdb;
	global $seatt_db_version;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   
	// Install tables
	$sql = "CREATE TABLE " . $wpdb->prefix . "seatt_events (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		event_name text NOT NULL,
		event_desc text NOT NULL,
		event_limit mediumint(9) NOT NULL,
		event_reserves mediumint(9) NOT NULL,
		event_start int NOT NULL,
		event_expire int NOT NULL,
		event_status mediumint(1) NOT NULL,
		UNIQUE KEY id (id)
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
		CREATE TABLE " . $wpdb->prefix . "seatt_attendees (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		event_id mediumint(9) NOT NULL,
		user_id int(9) DEFAULT NULL,
		user_comment text NOT NULL,
		UNIQUE KEY id (id)
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	dbDelta($sql);
 
   add_option("seatt_db_version", $seatt_db_version);
   
    $installed_ver = get_option( "seatt_db_version" );

   if ($installed_ver != $seatt_db_version) {

      $sql = "CREATE TABLE " . $wpdb->prefix . "seatt_events (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		event_name text NOT NULL,
		event_desc text NOT NULL,
		event_limit mediumint(9) NOT NULL,
		event_reserves mediumint(9) NOT NULL,
		event_start int NOT NULL,
		event_expire int NOT NULL,
		event_status mediumint(1) NOT NULL,
		UNIQUE KEY id (id)
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
		CREATE TABLE " . $wpdb->prefix . "seatt_attendees (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		event_id mediumint(9) NOT NULL,
		user_id int(9) DEFAULT NULL,
		user_comment text NOT NULL,
		UNIQUE KEY id (id)
		) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
      dbDelta($sql);

      update_option( "seatt_db_version", $seatt_db_version );
  }
}

function seatt_uninstall() {
	global $wpdb;
   
	// Remove tables
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "seatt_events");
	$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "seatt_attendees");
 	
	// Remove option
   delete_option("seatt_db_version");
}


register_activation_hook( __FILE__, 'seatt_install' );
register_uninstall_hook( __FILE__, 'seatt_uninstall' );

function seatt_update_db_check() {
    global $seatt_db_version;
    if (get_site_option('seatt_db_version') != $seatt_db_version) {
        seatt_install();
    }
}

add_action('seatt_loaded', 'seatt_update_db_check');

function seatt_admin() {  
	include('seatt_events_admin.php');
}

function seatt_admin_add() {   
	include('seatt_events_add.php');
}

function seatt_admin_edit() {   
	include('seatt_events_edit.php');
}

function seatt_admin_actions() {
	add_menu_page("SEATT Events", "SEATT Events", "level_3", "seatt_events", "seatt_admin", NULL );
	add_submenu_page( "seatt_events", "SEATT Events Add", "Add Event", "level_3", "seatt_events_add", "seatt_admin_add" );
	add_submenu_page( "seatt_events", "SEATT Events Edit", "Edit Event", "level_3", "seatt_events_edit", "seatt_admin_edit" );
}

add_action('admin_menu', 'seatt_admin_actions');

function seatt_func( $atts ) {
	extract( shortcode_atts( array(
		'event_id' => '1',
	), $atts ) );

	return seatt_form("{$event_id}");
}
add_shortcode( 'seatt-form', 'seatt_func' );

?>