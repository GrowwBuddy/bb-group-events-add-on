<?php
/**
 * The plugin constants.
 *
 * @package    BB_Group_Events
 * @subpackage Constants
 */

/**
 * Internal constants, not to be overridden
 */
if ( ! defined( 'BB_GROUP_EVENTS_VERSION' ) ) {
	define( 'BB_GROUP_EVENTS_VERSION', '1.0.0' );
}

if ( ! defined( 'BB_GROUP_EVENTS_PLUGIN_DIR_PATH' ) ) {
	define( 'BB_GROUP_EVENTS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BB_GROUP_EVENTS_PLUGIN_URL_PATH' ) ) {
	define( 'BB_GROUP_EVENTS_PLUGIN_URL_PATH', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'BB_GROUP_EVENTS_INCLUDES_DIR_PATH' ) ) {
	define( 'BB_GROUP_EVENTS_INCLUDES_DIR_PATH', BB_GROUP_EVENTS_PLUGIN_DIR_PATH . 'includes/' );
}

if ( ! defined( 'BB_GROUP_EVENTS_LANGUAGES_DIR_PATH' ) ) {
	define( 'BB_GROUP_EVENTS_LANGUAGES_DIR_PATH', BB_GROUP_EVENTS_PLUGIN_DIR_PATH . 'languages/' );
}
