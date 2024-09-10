<?php
/**
 * The admin class of Group Events for BuddyBoss.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Admin
 */

namespace Group_Events_For_BuddyBoss;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Admin
 */
class Admin {

	/**
	 * The instance of the class.
	 *
	 * @var Admin
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0
	 * @return Admin
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
		$this->init();
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bp_after_group_header', array( $this, 'display_group_events' ) );

        add_filter('bp_core_get_groups_admin_tabs', array($this, 'add_group_events_tab'));
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'group-events-for-buddyboss-admin', GROUP_EVENTS_FOR_BUDDYBOSS_ROOT_ASSETS_URL_PATH . 'css/admin.css', array(), GROUP_EVENTS_FOR_BUDDYBOSS_VERSION );
		wp_enqueue_script( 'group-events-for-buddyboss-admin',
			GROUP_EVENTS_FOR_BUDDYBOSS_ROOT_ASSETS_URL_PATH . 'js/admin.js',
			array( 'jquery' ),
			GROUP_EVENTS_FOR_BUDDYBOSS_VERSION,
			true );
	}

	/**
	 * Display group events
	 */
	public function display_group_events() {
		// Get current group ID
		$group_id = bp_get_group_id();
echo '<pre>===>';
print_r($group_id);
echo '</pre>';
exit;
		// Check if the current user is an admin or moderator in the group
		if ( $this->is_user_admin( $group_id ) ) {
			?>
            <div class="group-event-creation">
                <h3>Create a New Event</h3>
                <form action="" method="post">
                    <input type="text" name="event_title" placeholder="Event Title" required><br>
                    <textarea name="event_description" placeholder="Event Description" required></textarea><br>
                    <input type="date" name="event_date" required><br>
                    <input type="hidden" name="group_id" value="<?php echo esc_attr( $group_id ); ?>">
                    <input type="submit" name="create_event" value="Create Event">
                </form>
            </div>
			<?php
		}
	}

	// Check if user is a group admin before allowing event creation
	public function is_user_admin( $group_id ) {
		return bp_group_is_admin() || bp_group_is_mod();
	}

	public function add_group_events_tab( $tabs ) {
		$group_url = bp_get_admin_url( add_query_arg( array( 'post_type' => 'buddyboss_event' ), 'edit.php' ) );
		$tabs[]    = array(
			'href'  => $group_url,
			'name'  => __( 'Group Events', 'group-events-for-buddyboss' ),
			'class' => 'bb-events',
		);

		return $tabs;
	}

}
