<?php
/**
 * Plugin Name: Group Events for BuddyBoss
 * Plugin URI: https://example.com
 * Description: This plugin is used to manage the events for BuddyBoss groups.
 * Version: 1.0.0
 * Author: GrowwBuddy
 * Author URI: https://growwbuddy.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: group-events-for-buddyboss
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constants
require_once plugin_dir_path( __FILE__ ) . 'constants.php';
require_once plugin_dir_path( __FILE__ ) . 'class-group-events-for-buddyboss.php';

// Register activation hook
register_activation_hook( __FILE__, array( 'Group_Events_For_BuddyBoss', 'activation_hook' ) );
// Register deactivation hook
register_deactivation_hook( __FILE__, array( 'Group_Events_For_BuddyBoss', 'deactivation_hook' ) );

if ( ! defined( 'GROUP_EVENTS_FOR_BUDDYBOSS_VERSION' ) ) {
	return;
}


if ( ! function_exists( 'group_events_for_buddyboss' ) ) {
	/**
	 * Get the instance of the Book_Manager class
	 *
	 * @since 1.0
	 * @return Group_Events_For_BuddyBoss
	 */
	function group_events_for_buddyboss() {
		return Group_Events_For_BuddyBoss::get_instance();
	}

	/**
	 * Init the plugin and load the plugin instance
	 *
	 * @since 1.0
	 */
	add_action( 'plugins_loaded', 'group_events_for_buddyboss' );
}
