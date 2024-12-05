<?php
/**
 * The database class of the plugin.
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
class Group_Events_For_BuddyBoss_DB {

	/**
	 * The instance of the class.
	 *
	 * @since 1.0.0
	 * @var Group_Events_For_BuddyBoss_DB
	 */
	private static $instance;

	public $group_event_post_type = 'group-events';

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return Group_Events_For_BuddyBoss_DB
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
	}

	/**
	 * Include required files.
	 * @since 1.0.0
	 */
	public function includes() {
	}

	/**
	 * Initialize the plugin.
	 * @since 1.0.0
	 */
	public function setup_hooks() {
		if ( ! gb_disable_group_event() ) {
			return;
		}
		add_action( 'bp_init', array( $this, 'load_extension' ) );
		add_action( 'init', array( $this, 'main_init' ) );
		add_filter( 'bp_core_get_groups_admin_tabs', array( $this, 'add_group_events_tab' ) );
		add_action( 'all_admin_notices', array( $this, 'gb_groups_event_admin_group_type_listing_add_groups_tab' ) );
		add_filter( 'parent_file', array( $this, 'gb_group_event_set_platform_tab_submenu_active' ) );
		add_action( 'admin_head', array( $this, 'modify_editor_for_group_event' ) );
	}

	/**
	 * Load the init.
	 * @since 1.0.0
	 */
	public function main_init() {
		if ( function_exists( 'bp_is_active' ) && ! bp_is_active( 'groups' ) ) {
			return false;
		}
		$this->register_post_type();
	}

	/**
	 * Load the extension
	 * @since 1.0.0
	 */
	public function load_extension() {
		// Register the group extension only if groups are active
		if ( bp_is_active( 'groups' ) ) {
			require gb_dir_path( 'includes/common/class-group-events-for-buddyboss-extension.php' );
			bp_register_group_extension( 'Group_Events_For_BuddyBoss_Extension' );
		}
	}

	/**
	 * Register post type
	 * @since 1.0.0
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
			'add_new'               => __( 'Add New', 'group-events-for-buddyboss' ),
			'add_new_item'          => __( 'Add New', 'group-events-for-buddyboss' ),
			'new_item'              => __( 'New Group Event', 'group-events-for-buddyboss' ),
			'edit_item'             => __( 'Edit Group Event', 'group-events-for-buddyboss' ),
			'update_item'           => __( 'Update Group Event', 'group-events-for-buddyboss' ),
			'not_found'             => __( 'No found', 'group-events-for-buddyboss' ),
			'not_found_in_trash'    => __( 'No found in Trash', 'group-events-for-buddyboss' ),
			'view_item'             => __( 'View Group Event', 'group-events-for-buddyboss' ),
			'view_items'            => __( 'View Group Events', 'group-events-for-buddyboss' ),
			'search_items'          => __( 'Search', 'group-events-for-buddyboss' ),
			'items_list'            => __( 'Group Events list', 'group-events-for-buddyboss' ),
			'items_list_navigation' => __( 'Group Events list navigation', 'group-events-for-buddyboss' ),
			'filter_items_list'     => __( 'Filter Group Events list', 'group-events-for-buddyboss' ),
		);
		$args   = array(
			'label'              => __( 'Group Event', 'group-events-for-buddyboss' ),
			'description'        => __( 'Events for BuddyBoss groups', 'group-events-for-buddyboss' ),
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'group-event' ),
			'show_in_admin_bar'  => true,
			'show_in_menu'       => false,
			'map_meta_cap'       => true,
			'show_in_rest'       => false,
			'show_ui'            => bp_current_user_can( 'bp_moderate' ),
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);
		register_post_type( $this->group_event_post_type, $args );
	}

	/**
	 * Add Group Events tab in BuddyBoss > Groups
	 * @since 1.0.0
	 */
	public function add_group_events_tab( $tabs ) {
		$cpt_type = gb_groups_event_get_post_type();
		if ( is_network_admin() && bp_is_network_activated() ) {
			$group_url = get_admin_url( bp_get_root_blog_id(), 'edit.php?post_type=' . $cpt_type );
		} else {
			$group_url = bp_get_admin_url( add_query_arg( array( 'post_type' => $cpt_type ), 'edit.php' ) );
		}

		$tabs[] = array(
			'href'  => $group_url,
			'name'  => __( 'Group Events', 'group-events-for-buddyboss' ),
			'class' => 'group-events-for-buddyboss',
		);

		return $tabs;
	}

	/**
	 * Added Navigation tab on top of the page BuddyBoss > Group Types
	 *
	 * @since 1.0.0
	 */
	public function gb_groups_event_admin_group_type_listing_add_groups_tab() {
		global $pagenow, $post;

		if (
			( isset( $GLOBALS['wp_list_table']->screen->post_type ) && $GLOBALS['wp_list_table']->screen->post_type === $this->group_event_post_type && 'edit.php' === $pagenow ) ||
			( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'edit.php' === $pagenow ) ||
			( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'post-new.php' === $pagenow ) ||
			( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'post.php' === $pagenow )
		) {
			?>
            <div class="wrap">
                <h2 class="nav-tab-wrapper"><?php bp_core_admin_groups_tabs( __( 'Group Events', 'group-events-for-buddyboss' ) ); ?></h2>
            </div>
			<?php
		}
	}

	/**
	 * Highlights the submenu item using WordPress native styles.
	 *
	 * @param string $parent_file The filename of the parent menu.
	 *
	 * @since 1.0.0
	 * @return string $parent_file The filename of the parent menu.
	 */
	public function gb_group_event_set_platform_tab_submenu_active( $parent_file ) {
		global $pagenow, $current_screen, $post;

		if ( true === bp_disable_group_type_creation() ) {
			if (
				( isset( $GLOBALS['wp_list_table']->screen->post_type ) && $GLOBALS['wp_list_table']->screen->post_type === $this->group_event_post_type && 'edit.php' === $pagenow ) ||
				( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'edit.php' === $pagenow ) ||
				( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'post-new.php' === $pagenow ) ||
				( isset( $post->post_type ) && $post->post_type === $this->group_event_post_type && 'post.php' === $pagenow )
			) {
				$parent_file = 'buddyboss-platform';
			}
		}

		return $parent_file;
	}

	/**
	 * Modify the editor for group event.
	 * @since 1.0.0
	 */
	public function modify_editor_for_group_event() {
		global $post;
		if ( ! empty( $post ) && $post->post_type === $this->group_event_post_type ) {
			// Remove the media button
			//add_filter( 'user_can_richedit', '__return_false' ); // Disable rich editor entirely
			add_filter( 'wp_editor_settings', array( $this, 'remove_media_buttons_from_editor' ), 10, 2 );
		}
	}

	/**
	 * Remove media buttons from the editor.
	 * @since 1.0.0
	 */
	public function remove_media_buttons_from_editor( $settings, $editor_id ) {
		// Customize the editor settings
		$settings['media_buttons'] = false; // Disable the media buttons
		$settings['teeny']         = true; // Enable the 'teeny' mode for a simplified editor
		$settings['textarea_rows'] = 5; // Set the textarea rows
		$settings['quicktags']     = array( 'buttons' => 'strong,em,link,block,del,ins,img,code,spell,close' ); // Customize quicktags
		$settings['editor_height'] = 200; // Customize quicktags

		return $settings;
	}
}
