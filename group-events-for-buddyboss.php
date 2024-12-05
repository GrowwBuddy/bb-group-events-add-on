<?php
/**
 * Plugin Name: Group Events for BuddyBoss
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

// Load Constants.
require_once plugin_dir_path( __FILE__ ) . 'constants.php';

// Register activation hook
register_activation_hook( __FILE__, array( 'Group_Events_For_BuddyBoss', 'activation_hook' ) );
// Register deactivation hook
register_deactivation_hook( __FILE__, array( 'Group_Events_For_BuddyBoss', 'deactivation_hook' ) );

if ( ! defined( 'GB_GEFBB_VERSION' ) ) {
	return;
}

/**
 * Main class of BuddyBoss Group Events
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Group_Events_For_BuddyBoss' ) ) {
	/**
	 * Class Group_Events_For_BuddyBoss
	 * @since 1.0.0
	 */
	class Group_Events_For_BuddyBoss {

		/**
		 * The instance of the class.
		 *
		 * @var Group_Events_For_BuddyBoss
		 */
		private static $instance;

		/**
		 * Return the plugin instance
		 *
		 * @since 1.0.0
		 * @return Group_Events_For_BuddyBoss
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->includes();
			$this->init();
			$this->load_textdomain();
		}

		/**
		 * Include required files
		 * @since 1.0.0
		 */
		public function includes() {
			require_once gb_dir_path( 'includes/class-group-events-for-buddyboss-main.php' );
		}

		/**
		 * Initialize the plugin
		 * @since 1.0.0
		 */
		public function init() {
			// Initialize plugin core
			Group_Events_For_BuddyBoss_Main::get_instance();

			/**
			 * Triggered when plugin is loaded
			 * @since 1.0.0
			 */
			do_action( 'gb_group_events_loaded' );
		}

		/**
		 * Load the plugin text domain
		 * @since 1.0.0
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'group-events-for-buddyboss', false, gb_dir_path( 'languages/' ) );
		}


		/**
		 * Activation hook
		 * @since 1.0.0
		 */
		public static function activation_hook() {
			Group_Events_For_BuddyBoss::get_instance()->on_database_update();
		}

		/**
		 * Deactivation hook
		 * @since 1.0.0
		 */
		public static function deactivation_hook() {
		}

		/**
		 * Fires on database update.
		 *
		 * @since 1.0.0
		 */
		public function on_database_update() {
			$this->create_tables();
		}

		/**
		 * Create tables.
		 *
		 * @since 1.0.0
		 */
		private function create_tables() {
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$charset_collate = $wpdb->get_charset_collate();
			$table_name      = $wpdb->prefix . 'gb_rsvp';

			$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			group_id BIGINT(20) UNSIGNED NOT NULL,
			event_id BIGINT(20) UNSIGNED NOT NULL,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			status VARCHAR(20) NOT NULL,
			comment TEXT DEFAULT NULL,
			create_date DATETIME DEFAULT CURRENT_TIMESTAMP,
			modif_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (ID),
    		KEY group_id (group_id),
            KEY event_id (event_id),
            KEY user_id (user_id)
		) $charset_collate;";

			dbDelta( $sql );
		}
	}
}

/************************* Load functions *************************/

if ( ! function_exists( 'gb_group_events_for_buddyboss' ) ) {
	/**
	 * Get the instance of the Group_Events_For_BuddyBoss class
	 *
	 * @since 1.0.0
	 * @return Group_Events_For_BuddyBoss
	 */
	function gb_group_events_for_buddyboss() {
		if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
			add_action( 'all_admin_notices', 'gb_platform_required_notice' );
		} elseif ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			add_action( 'all_admin_notices', 'gb_group_component_required_notice' );
			return Group_Events_For_BuddyBoss::get_instance();
		}
	}

	/**
	 * Init the plugin and load the plugin instance
	 *
	 * @since 1.0.0
	 */
	add_action( 'plugins_loaded', 'gb_group_events_for_buddyboss' );
}

/**
 * Group event dir path.
 * @since 1.0.0
 */
function gb_dir_path( $path = '' ) {
	return GB_GEFBB_PLUGIN_DIR_PATH . $path;
}

/**
 * Group event dir url.
 * @since 1.0.0
 */
function gb_dir_url( $path = '' ) {
	return GB_GEFBB_PLUGIN_URL_PATH . $path;
}

/**
 * Group event required notice.
 * @since 1.0.0
 */
function gb_platform_required_notice() {
	echo '<div class="error fade"><p>';
	echo sprintf(
		'<strong>%s</strong> %s <a href="https://buddyboss.com/platform/" target="_blank">%s</a> %s',
		esc_html__( 'BuddyBoss Group Events', 'group-events-for-buddyboss' ),
		esc_html__( 'requires the BuddyBoss Platform plugin to work. Please', 'group-events-for-buddyboss' ),
		esc_html__( 'install BuddyBoss Platform', 'group-events-for-buddyboss' ),
		esc_html__( 'first.', 'group-events-for-buddyboss' )
	);
	echo '</p></div>';
}

/**
 * Group event required notice.
 * @since 1.0.0
 */
function gb_group_component_required_notice() {
	if ( function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
		return;
	}

	echo '<div class="error fade"><p>';
	echo sprintf(
		'<strong>%s</strong> %s',
		esc_html__( 'BuddyBoss Group Events', 'group-events-for-buddyboss' ),
		esc_html__( 'requires Social Groups Component to work. Please activate Social Groups Component.', 'group-events-for-buddyboss' )
	);
	echo '</p></div>';
}