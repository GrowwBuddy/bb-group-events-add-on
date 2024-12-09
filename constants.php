<?php
/**
 * The plugin constants.
 *
 * @package    GB_GEFBB
 * @subpackage Constants
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
} // Exit if accessed directly

/**
 * Internal constants, not to be overridden
 */
if ( ! defined( 'GB_GEFBB_VERSION' ) ) {
	define( 'GB_GEFBB_VERSION', '1.0.0' );
}

if ( ! defined( 'GB_GEFBB_PLUGIN_DIR_PATH' ) ) {
	define( 'GB_GEFBB_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'GB_GEFBB_PLUGIN_URL_PATH' ) ) {
	define( 'GB_GEFBB_PLUGIN_URL_PATH', plugin_dir_url( __FILE__ ) );
}
