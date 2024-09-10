<?php

/**
 * Main class of Group Events for BuddyBoss
 */

if ( ! class_exists( 'Group_Events_For_BuddyBoss' ) ) {
	/**
	 * Class Group_Events_For_BuddyBoss
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
		 * @since 1.0
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
		 */
		public function __construct() {
			$this->includes();
			$this->init();
			$this->load_textdomain();
		}

		/**
		 * Include required files
		 */
		public function includes() {
			require_once GROUP_EVENTS_FOR_BUDDYBOSS_INCLUDES_DIR_PATH . 'class-main.php';
		}

		/**
		 * Initialize the plugin
		 */
		public function init() {
			// Initialize plugin core
			Group_Events_For_BuddyBoss\Main::get_instance();

			/**
			 * Triggered when plugin is loaded
			 */
			do_action( 'group_events_for_buddyboss_loaded' );
		}

		/**
		 * Load the plugin text domain
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'group-events-for-buddyboss', false, GROUP_EVENTS_FOR_BUDDYBOSS_LANGUAGES_DIR_PATH );
		}


		/**
		 * Activation hook
		 */
		public static function activation_hook() {
		}

		/**
		 * Deactivation hook
		 */
		public static function deactivation_hook() {
		}
	}
}
