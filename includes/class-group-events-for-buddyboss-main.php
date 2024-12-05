<?php
/**
 * The main class of the plugin.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Main
 */
class Group_Events_For_BuddyBoss_Main {

	/**
	 * The instance of the class.
	 *
	 * @var Group_Events_For_BuddyBoss_Main
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return Group_Events_For_BuddyBoss_Main
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
		$this->setup_hooks();

		if ( is_admin() ) {
			// Initialize admin core.
			Group_Events_For_BuddyBoss_Admin::get_instance();
			Group_Events_For_BuddyBoss_Admin_MetaBox::get_instance();
		}

		Group_Events_For_BuddyBoss_Manager::get_instance();
		Group_Events_For_BuddyBoss_DB::get_instance();

		// Initialize frontend core.
		Group_Events_For_BuddyBoss_FrontEnd::get_instance();
	}

	/**
	 * Include required files.
	 * @since 1.0.0
	 */
	public function includes() {
		// Load all functions.
		require gb_dir_path( 'includes/functions.php' );
		require gb_dir_path( 'includes/admin/class-group-events-for-buddyboss-admin.php' );
		require gb_dir_path( 'includes/admin/class-group-events-for-buddyboss-admin-metabox.php' );

		// Load common classes.
		require gb_dir_path( 'includes/common/class-group-events-for-buddyboss-manager.php' );
		require gb_dir_path( 'includes/common/class-group-events-for-buddyboss-db.php' );

		// Load admin and frontend classes.
		require gb_dir_path( 'includes/frontend/class-group-events-for-buddyboss-frontend.php' );
	}

	/**
	 * Initialize the plugin.
	 * @since 1.0.0
	 */
	public function setup_hooks() {
		add_filter( 'plugin_action_links', array( $this, 'actions_links' ), 10, 2 );
	}

	/**
	 * Add action links to the plugin
	 *
	 * @param array  $links The links array.
	 * @param string $file  The plugin file.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function actions_links( $links, $file ) {
		if ( plugin_basename( gb_dir_path( 'group-events-for-buddyboss.php' ) ) === $file ) {
			$links['settings'] = sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=bp-settings&tab=bp-groups#gb_gefbb_settings' ),
				esc_html__( 'Settings', 'group-events-for-buddyboss' )
			);
		}

		return $links;
	}
}
