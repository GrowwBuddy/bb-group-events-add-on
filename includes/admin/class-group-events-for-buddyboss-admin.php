<?php
/**
 * The admin class of Group Events for BuddyBoss.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Admin
 */
class Group_Events_For_BuddyBoss_Admin {

	/**
	 * The instance of the class.
	 *
	 * @var Group_Events_For_BuddyBoss_Admin
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return Group_Events_For_BuddyBoss_Admin
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'bp_admin_setting_groups_register_fields', array( $this, 'gb_gefbb_admin_setting_groups_register_fields' ) );
		add_filter( 'bb_admin_icons', array( $this, 'admin_setting_icons' ), 10, 2 );
		add_filter( 'manage_' . gb_gefbb_groups_event_get_post_type() . '_posts_columns', array( $this, 'gb_gefbb_group_events_columns_head' ) );
		add_action( 'manage_' . gb_gefbb_groups_event_get_post_type() . '_posts_custom_column', array( $this, 'gb_gefbb_group_events_columns_content' ), 10, 2 );
	}

	/**
	 * Enqueue scripts and styles.
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'select2-js', gb_gefbb_dir_url( 'assets/lib/select2-4.0.13/js/select2' . $suffix . '.js' ), array( 'jquery' ), GB_GEFBB_VERSION, array( 'in_footer' => true ) );
		wp_register_style( 'select2-css', gb_gefbb_dir_url( 'assets/lib/select2-4.0.13/css/select2' . $suffix . '.css' ), array(), GB_GEFBB_VERSION );
		wp_register_script( 'group-events-for-buddyboss-admin', gb_gefbb_dir_url( 'assets/js/admin.js' ), array( 'jquery', 'select2-js' ), GB_GEFBB_VERSION, array( 'in_footer' => true ) );
		wp_register_style( 'group-events-for-buddyboss-admin', gb_gefbb_dir_url( 'assets/css/admin.css' ), array(), GB_GEFBB_VERSION );

		wp_enqueue_script( 'select2-js' );
		wp_enqueue_style( 'select2-css' );
		wp_enqueue_script( 'group-events-for-buddyboss-admin' );
		wp_enqueue_style( 'group-events-for-buddyboss-admin' );

		// Pass AJAX URL and nonce to JavaScript
		wp_localize_script(
			'group-events-for-buddyboss-admin',
			'BBGroupEvents',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'gb_gefbb_admin_nonce' ),
			)
		);
	}

	/**
	 * Register the settings fields.
	 * @since 1.0.0
	 */
	public function gb_gefbb_admin_setting_groups_register_fields( $admin_setting_groups ) {
		$admin_setting_groups->add_section( 'gb_gefbb_settings', __( 'Group Events', 'group-events-for-buddyboss' ) );

		// enable or disable group events.
		$admin_setting_groups->add_field( 'gb-gefbb-disable', __( 'Enable Group Events', 'group-events-for-buddyboss' ), array( $this, 'gb_gefbb_enable_group_events' ), 'intval' );
	}

	/**
	 * Enable or disable group events.
	 * @since 1.0.0
	 */
	public function gb_gefbb_enable_group_events() {
		?>
		<input id="gb-gefbb-disable" name="gb-gefbb-disable" type="checkbox" value="1" <?php checked( gb_gefbb_disable_group_event() ); ?> />
		<?php
		if ( true === gb_gefbb_disable_group_event() ) {
			printf(
				'<label for="gb-gefbb-disable">%s</label>',
				sprintf(
				/* translators: 1. Enable group events link 2. Group events 3. Group events description */
					'%s<a href="%s">%s</a> %s',
					esc_html__( 'Enable ', 'group-events-for-buddyboss' ),
					esc_url(
						add_query_arg(
							array(
								'post_type' => gb_gefbb_groups_event_get_post_type(),
							),
							admin_url( 'edit.php' )
						)
					),
					esc_html__( 'group events', 'group-events-for-buddyboss' ),
					esc_html__( 'to better organize groups events', 'group-events-for-buddyboss' ),
				)
			);
		} else {
			?>
			<label for="gb-gefbb-disable"><?php esc_html_e( 'Enable group event to better organize groups events', 'group-events-for-buddyboss' ); ?></label>
			<?php
		}
	}

	/**
	 * Added icon for the event admin settings.
	 *
	 * @param string $meta_icon Icon class.
	 * @param string $id        Section ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function admin_setting_icons( $meta_icon, $id = '' ) {
		if ( ! empty( $id ) && 'gb_gefbb_settings' === $id ) {
			$meta_icon = 'bb-icon-bf bb-icon-calendar';
		}

		return $meta_icon;
	}

	/**
	 * Add columns to the group events list.
	 *
	 * @param array $columns Columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function gb_gefbb_group_events_columns_head( $columns ) {
		$columns['group']      = __( 'Group', 'group-events-for-buddyboss' );
		$columns['event_date'] = __( 'Event Date', 'group-events-for-buddyboss' );
		$columns['total_rsvp'] = __( 'Total RSVP', 'group-events-for-buddyboss' );

		return $columns;
	}

	/**
	 * Add content to the group events list.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 *
	 * @since 1.0.0
	 */
	public function gb_gefbb_group_events_columns_content( $column, $post_id ) {
		switch ( $column ) {
			case 'group':
				$event = Group_Events_For_BuddyBoss_Manager::get_instance()->get_event( $post_id );
				if ( ! empty( $event['group_id'] ) ) {
					$group = groups_get_group( $event['group_id'] );
					echo sprintf(
						'<a href="%s"><strong>%s</strong></a>',
						esc_url( bp_get_group_permalink( $group ) ),
						esc_html( bp_get_group_name( $group ) )
					);
				}

				break;
			case 'event_date':
				$event_start_date = get_post_meta( $post_id, '_event_start_date', true );
				if ( ! empty( $event_start_date ) ) {
					echo esc_html( gmdate( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $event_start_date ) ) );
				}

				if ( ! empty( $event_start_date ) && $event_start_date < gmdate( 'Y-m-d H:i:s' ) ) {
					echo '<br><span style="color: red;">' . esc_html__( 'Past Event', 'group-events-for-buddyboss' ) . '</span>';
				}

				if ( ! empty( $event_start_date ) && $event_start_date > gmdate( 'Y-m-d H:i:s' ) ) {
					echo '<br><span style="color: green;">' . esc_html__( 'Upcoming Event', 'group-events-for-buddyboss' ) . '</span>';
				}

				break;
			case 'total_rsvp':
				$rsvps_yes   = Group_Events_For_BuddyBoss_Manager::get_instance()->get_rsvps_by_event( $post_id, 'yes' );
				$rsvps_no    = Group_Events_For_BuddyBoss_Manager::get_instance()->get_rsvps_by_event( $post_id, 'no' );
				$rsvps_maybe = Group_Events_For_BuddyBoss_Manager::get_instance()->get_rsvps_by_event( $post_id, 'maybe' );
				if ( ! empty( count( $rsvps_yes ) ) ) {
					echo sprintf( '<span style="color:green">%s: %d</span><br>', esc_html__( 'Yes', 'group-events-for-buddyboss' ), count( $rsvps_yes ) );
				}
				if ( ! empty( count( $rsvps_no ) ) ) {
					echo sprintf( '<span style="color:red">%s: %d</span><br>', esc_html__( 'No', 'group-events-for-buddyboss' ), count( $rsvps_no ) );
				}
				if ( ! empty( count( $rsvps_maybe ) ) ) {
					echo sprintf( '<span style="color:grey">%s: %d</span>', esc_html__( 'Maybe', 'group-events-for-buddyboss' ), count( $rsvps_maybe ) );
				}
				break;
		}
	}
}
