<?php
/**
 * The admin class of Group Events for BuddyBoss.
 *
 * @package    BB_Group_Events
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Admin
 */
class BB_Group_Events_Admin {

	/**
	 * The instance of the class.
	 *
	 * @var BB_Group_Events_Admin
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return BB_Group_Events_Admin
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
		add_filter( 'bp_admin_setting_groups_register_fields', array( $this, 'bbgea_admin_setting_groups_register_fields' ) );
		add_filter( 'bb_admin_icons', array( $this, 'admin_setting_icons' ), 10, 2 );
		add_filter( 'manage_' . bbgea_groups_event_get_post_type() . '_posts_columns', array( $this, 'bbgea_group_events_columns_head' ) );
		add_action( 'manage_' . bbgea_groups_event_get_post_type() . '_posts_custom_column', array( $this, 'bbgea_group_events_columns_content' ), 10, 2 );
	}

	/**
	 * Enqueue scripts and styles.
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'select2-js', bbgea_dir_url( 'assets/lib/select2-4.0.13/js/select2' . $suffix . '.js', array( 'jquery' ), BB_GROUP_EVENTS_VERSION, true ) );
		wp_enqueue_style( 'select2-css', bbgea_dir_url( 'assets/lib/select2-4.0.13/css/select2' . $suffix . '.css' ), array(), BB_GROUP_EVENTS_VERSION );

		wp_enqueue_style( 'buddyboss-group-events-add-on-admin', bbgea_dir_url( 'assets/css/admin.css' ), array(), BB_GROUP_EVENTS_VERSION );
		wp_enqueue_script(
			'buddyboss-group-events-add-on-admin',
			bbgea_dir_url( 'assets/js/admin.js' ),
			array( 'jquery', 'select2-js' ),
			BB_GROUP_EVENTS_VERSION,
			true
		);

		// Pass AJAX URL and nonce to JavaScript
		wp_localize_script(
			'buddyboss-group-events-add-on-admin',
			'BBGroupEvents',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'bbgea_admin_nonce' ),
			)
		);
	}

	/**
	 * Register the settings fields.
	 * @since 1.0.0
	 */
	public function bbgea_admin_setting_groups_register_fields( $admin_setting_groups ) {
		$admin_setting_groups->add_section( 'bbgea_bb_settings', __( 'Group Events', 'buddyboss-group-events' ) );

		// enable or disable group events.
		$admin_setting_groups->add_field( 'bbgea-disable', __( 'Enable Group Events', 'buddyboss-group-events' ), array( $this, 'bbgea_enable_group_events' ), 'intval' );
	}

	/**
	 * Enable or disable group events.
	 * @since 1.0.0
	 */
	public function bbgea_enable_group_events() {
		?>
		<input id="bbgea-disable" name="bbgea-disable" type="checkbox" value="1" <?php checked( bbgea_disable_group_event() ); ?> />
		<?php
		if ( true === bbgea_disable_group_event() ) {

			printf(
				'<label for="bbgea-disable">%s</label>',
				sprintf(
						/* translators: 1. Enable group events link 2. Group events 3. Group events description */
					'%s<a href="%s">%s</a> %s',
					esc_html__( 'Enable ', 'buddyboss-group-events' ),
					esc_url(
						add_query_arg(
							array(
								'post_type' => bbgea_groups_event_get_post_type(),
							),
							admin_url( 'edit.php' )
						)
					),
					esc_html__( 'group events', 'buddyboss-group-events' ),
					esc_html__( 'to better organize groups events', 'buddyboss-group-events' ),
				)
			);
		} else {
			?>
			<label for="bbgea-disable"><?php esc_html_e( 'Enable group event to better organize groups events', 'buddyboss-group-events' ); ?></label>
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
		if ( ! empty( $id ) && 'bbgea_bb_settings' === $id ) {
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
	public function bbgea_group_events_columns_head( $columns ) {
		$columns['group']      = __( 'Group', 'buddyboss-group-events' );
		$columns['event_date'] = __( 'Event Date', 'buddyboss-group-events' );
		$columns['total_rsvp'] = __( 'Total RSVP', 'buddyboss-group-events' );

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
	public function bbgea_group_events_columns_content( $column, $post_id ) {
		switch ( $column ) {
			case 'group':
				$event = BB_Group_Event_Manager::get_instance()->get_event( $post_id );
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
					echo '<br><span style="color: red;">' . esc_html__( 'Past Event', 'buddyboss-group-events' ) . '</span>';
				}

				if ( ! empty( $event_start_date ) && $event_start_date > gmdate( 'Y-m-d H:i:s' ) ) {
					echo '<br><span style="color: green;">' . esc_html__( 'Upcoming Event', 'buddyboss-group-events' ) . '</span>';
				}

				break;
			case 'total_rsvp':
				$rsvps_yes   = BB_Group_Event_Manager::get_instance()->get_rsvps_by_event( $post_id, 'yes' );
				$rsvps_no    = BB_Group_Event_Manager::get_instance()->get_rsvps_by_event( $post_id, 'no' );
				$rsvps_maybe = BB_Group_Event_Manager::get_instance()->get_rsvps_by_event( $post_id, 'maybe' );
				if ( ! empty( count( $rsvps_yes ) ) ) {
					echo sprintf( '<span style="color:green">%s: %d</span><br>', esc_html__( 'Yes', 'buddyboss-group-events' ), count( $rsvps_yes ) );
				}
				if ( ! empty( count( $rsvps_no ) ) ) {
					echo sprintf( '<span style="color:red">%s: %d</span><br>', esc_html__( 'No', 'buddyboss-group-events' ), count( $rsvps_no ) );
				}
				if ( ! empty( count( $rsvps_maybe ) ) ) {
					echo sprintf( '<span style="color:grey">%s: %d</span>', esc_html__( 'Maybe', 'buddyboss-group-events' ), count( $rsvps_maybe ) );
				}
				break;
		}
	}
}
