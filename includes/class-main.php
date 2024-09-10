<?php
/**
 * The main class of the plugin.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Main
 */

namespace Group_Events_For_BuddyBoss;

// Exit if accessed directly.
use Group_Events_Extension;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Main
 */
class Main {

	/**
	 * The instance of the class.
	 *
	 * @var Main
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0
	 * @return Main
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
		$this->setup_hooks();

		if ( is_admin() ) {
			// Initialize admin core.
			\Group_Events_For_BuddyBoss\Admin::get_instance();
		}

		// Initialize frontend core.
		\Group_Events_For_BuddyBoss\FrontEnd::get_instance();
	}

	/**
	 * Include required files
	 */
	public function includes() {
		// Admin classes includes.
		include_once GROUP_EVENTS_FOR_BUDDYBOSS_INCLUDES_DIR_PATH . 'admin/class-admin.php';
		include_once GROUP_EVENTS_FOR_BUDDYBOSS_INCLUDES_DIR_PATH . 'admin/class-group-events-extension.php';
		// Frontend classes includes.
		include_once GROUP_EVENTS_FOR_BUDDYBOSS_INCLUDES_DIR_PATH . 'frontend/class-frontend.php';
	}

	/**
	 * Initialize the plugin
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'main_init' ) );
		add_action( 'bp_init', array( $this, 'setup_components' ), 7 );
	}

	/**
	 * Load the init
	 */
	public function main_init() {
		$this->register_post_type();
	}

	/**
	 * Register post type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Group Events', 'Post Type General Name', 'group-events-for-buddyboss' ),
			'singular_name'         => _x( 'Group Event', 'Post Type Singular Name', 'group-events-for-buddyboss' ),
			'menu_name'             => __( 'Group Events', 'group-events-for-buddyboss' ),
			'name_admin_bar'        => __( 'Group Event', 'group-events-for-buddyboss' ),
			'archives'              => __( 'Group Event Archives', 'group-events-for-buddyboss' ),
			'attributes'            => __( 'Group Event Attributes', 'group-events-for-buddyboss' ),
			'parent_item_colon'     => __( 'Parent Group Event:', 'group-events-for-buddyboss' ),
			'all_items'             => __( 'All Group Events', 'group-events-for-buddyboss' ),
			'add_new'               => __( 'Add New', 'book-manager' ),
			'add_new_item'          => __( 'Add New Group Event', 'group-events-for-buddyboss' ),
			'new_item'              => __( 'New Group Event', 'group-events-for-buddyboss' ),
			'edit_item'             => __( 'Edit Group Event', 'group-events-for-buddyboss' ),
			'update_item'           => __( 'Update Group Event', 'group-events-for-buddyboss' ),
			'not_found'             => __( 'No found', 'group-events-for-buddyboss' ),
			'not_found_in_trash'    => __( 'No found in Trash', 'group-events-for-buddyboss' ),
			'view_item'             => __( 'View Group Event', 'group-events-for-buddyboss' ),
			'view_items'            => __( 'View Group Events', 'group-events-for-buddyboss' ),
			'search_items'          => __( 'Search Group Event', 'group-events-for-buddyboss' ),
			'items_list'            => __( 'Group Events list', 'group-events-for-buddyboss' ),
			'items_list_navigation' => __( 'Group Events list navigation', 'group-events-for-buddyboss' ),
			'filter_items_list'     => __( 'Filter Group Events list', 'group-events-for-buddyboss' ),
		);
		$args   = array(
			'label'           => __( 'Group Event', 'group-events-for-buddyboss' ),
			'description'     => __( 'Events for BuddyBoss groups', 'group-events-for-buddyboss' ),
			'labels'          => $labels,
			'hierarchical'    => false,
			'public'          => true,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_position'   => 5,
			'has_archive'     => true,
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'events' ),
			'supports'        => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ),
			'show_in_rest'    => false,
		);
		register_post_type( 'buddyboss_event', $args );
	}

	public function setup_components() {
		// Register the group extension only if groups are active
		if ( bp_is_active( 'groups' ) ) {
			$this->register_group_extension();
		}

	}

	/**
	 * Register the group extension
	 */
	public function register_group_extension() {
		bp_register_group_extension( 'Group_Events_Extension' );
	}

}
