<?php
/**
 * The main class of the plugin.
 *
 * @package    BB_Group_Events
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Main
 */
class BB_Group_Events_Main {

	/**
	 * The instance of the class.
	 *
	 * @var BB_Group_Events_Main
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return BB_Group_Events_Main
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
			BB_Group_Events_Admin::get_instance();
			BB_Group_Events_Admin_MetaBox::get_instance();
		}

		BB_Group_Event_Manager::get_instance();
		BB_Group_Events_DB::get_instance();

		// Initialize frontend core.
		BB_Group_Events_FrontEnd::get_instance();
	}

	/**
	 * Include required files.
	 * @since 1.0.0
	 */
	public function includes() {
		// Load all functions.
		require bbgea_dir_path( 'includes/functions.php' );
		require bbgea_dir_path( 'includes/admin/class-bb-group-events-admin.php' );
		require bbgea_dir_path( 'includes/admin/class-bb-group-events-admin-metabox.php' );

		// Load common classes.
		require bbgea_dir_path( 'includes/common/class-bb-group-event-manager.php' );
		require bbgea_dir_path( 'includes/common/class-bb-group-events-db.php' );

		// Load admin and frontend classes.
		require bbgea_dir_path( 'includes/frontend/class-bb-group-events-frontend.php' );
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
		if ( plugin_basename( bbgea_dir_path( 'bb-group-events.php' ) ) === $file ) {
			$links['settings'] = sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=bp-settings&tab=bp-groups#bbgea_bb_settings' ),
				esc_html__( 'Settings', 'bb-group-events' )
			);
		}

		return $links;
	}
}
