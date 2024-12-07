<?php
/**
 * The main class of the plugin.
 *
 * @package    GB_GEFBB
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Main
 */
class GB_GEFBB_Main {

	/**
	 * The instance of the class.
	 *
	 * @var GB_GEFBB_Main
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return GB_GEFBB_Main
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
			GB_GEFBB_Admin::get_instance();
			GB_GEFBB_Admin_MetaBox::get_instance();
		}

		GB_GEFBB_Manager::get_instance();
		GB_GEFBB_DB::get_instance();

		// Initialize frontend core.
		GB_GEFBB_FrontEnd::get_instance();
	}

	/**
	 * Include required files.
	 * @since 1.0.0
	 */
	public function includes() {
		// Load all functions.
		require gb_gefbb_dir_path( 'includes/functions.php' );
		require gb_gefbb_dir_path( 'includes/admin/class-group-events-for-buddyboss-admin.php' );
		require gb_gefbb_dir_path( 'includes/admin/class-group-events-for-buddyboss-admin-metabox.php' );

		// Load common classes.
		require gb_gefbb_dir_path( 'includes/common/class-group-events-for-buddyboss-manager.php' );
		require gb_gefbb_dir_path( 'includes/common/class-group-events-for-buddyboss-db.php' );

		// Load admin and frontend classes.
		require gb_gefbb_dir_path( 'includes/frontend/class-group-events-for-buddyboss-frontend.php' );
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
		if ( plugin_basename( gb_gefbb_dir_path( 'group-events-for-buddyboss.php' ) ) === $file ) {
			$links['settings'] = sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=bp-settings&tab=bp-groups#gb_gefbb_settings' ),
				esc_html__( 'Settings', 'group-events-for-buddyboss' )
			);
		}

		return $links;
	}
}
