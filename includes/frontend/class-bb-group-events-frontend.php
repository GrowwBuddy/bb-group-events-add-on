<?php

/**
 * The frontend class of Group Events for BuddyBoss.
 *
 * @package    BB_Group_Events
 * @subpackage Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class FrontEnd
 * @since 1.0.0
 */
class BB_Group_Events_FrontEnd {

	/**
	 * The instance of the class.
	 * @since 1.0.0
	 * @var BB_Group_Events_FrontEnd
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return BB_Group_Events_FrontEnd
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'enqueue_event_form_template' ) );
		add_filter( 'template_include', array( $this, 'load_event_template' ) );
		add_filter( 'render_event_item_html', array( $this, '_render_event_item_html' ), 10, 2 );
		add_action( 'wp_ajax_fetch_group_event', array( $this, 'fetch_group_event' ) );
		add_action( 'wp_ajax_group_event_save', array( $this, 'save_group_event' ) );
		add_action( 'wp_ajax_fetch_user_rsvp', array( $this, 'fetch_user_rsvp' ) );
		add_action( 'wp_ajax_save_user_rsvp', array( $this, 'handle_user_rsvp' ) );
		add_action( 'wp_ajax_load_events', array( $this, 'load_events' ) );
		add_action( 'wp_ajax_nopriv_load_events', array( $this, 'load_events' ) );
	}

	/**
	 * Enqueue scripts and styles
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_editor();
		wp_enqueue_style( 'bbgea-frontend', bbgea_dir_url( 'assets/css/frontend.css' ), array(), BB_GROUP_EVENTS_VERSION );
		wp_enqueue_script(
			'bbgea-frontend',
			bbgea_dir_url( 'assets/js/frontend.js' ),
			array( 'jquery' ),
			BB_GROUP_EVENTS_VERSION,
			true
		);

		wp_localize_script(
			'bbgea-frontend',
			'bbgea_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'bbgea_nonce' ),
			)
		);
	}

	/**
	 * Enqueue the event form template.
	 * @since 1.0.0
	 */
	public function enqueue_event_form_template() {
		if ( bbgea_can_manage_group() ) {
			include bbgea_group_events_get_template( 'events/manage/create-event.php' );
		}
		include bbgea_group_events_get_template( 'events/manage/edit-rsvp.php' );
	}

	/**
	 * Load the event template.
	 * @since 1.0.0
	 */
	public function load_event_template( $template ) {
		if ( is_singular( bbgea_groups_event_get_post_type() ) ) {
			$template = bbgea_group_events_get_template( 'single-bb-group-event.php' );
		}

		return $template;
	}

	/**
	 * Fetch the group event.
	 *
	 * @since 1.0.0
	 */
	public function fetch_group_event() {
		// Check nonce for security.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'bbgea_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );

			return;
		}

		// Validate and sanitize event ID.
		$event_id = isset( $_GET['event_id'] ) ? intval( $_GET['event_id'] ) : 0;

		if ( ! $event_id ) {
			wp_send_json_error( array( 'message' => 'Invalid event ID' ) );

			return;
		}

		// Retrieve event data.
		$event_post = get_post( $event_id );

		if ( ! $event_post || bbgea_groups_event_get_post_type() !== $event_post->post_type ) {
			wp_send_json_error( array( 'message' => 'Event not found' ) );

			return;
		}

		// Prepare event data to send to the frontend.
		$event_data = array(
			'title'          => $event_post->post_title,
			'description'    => wp_kses_post( $event_post->post_content ),
			'start_date'     => get_post_meta( $event_id, '_event_start_date', true ),
			'end_date'       => get_post_meta( $event_id, '_event_end_date', true ),
			'event_type'     => get_post_meta( $event_id, '_event_type', true ),
			'location'       => get_post_meta( $event_id, '_event_location', true ),
			'event_group_id' => get_post_meta( $event_id, '_event_group_id', true ),
		);

		// Send the response.
		wp_send_json_success( array( 'event_data' => $event_data ) );
	}

	/**
	 * Save the group event.
	 * @since 1.0.0
	 */
	public function save_group_event() {
		check_ajax_referer( 'bbgea_nonce', '_wpnonce' );

		// Capture and sanitize inputs
		$event_id       = ! empty( $_POST['event_id'] ) ? absint( $_POST['event_id'] ) : 0;
		$title          = ! empty( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$description    = ! empty( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';
		$start_date     = ! empty( $_POST['start_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_date_time'] ) ) : '';
		$end_date       = ! empty( $_POST['end_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date_time'] ) ) : '';
		$type           = ! empty( $_POST['event_type'] ) ? sanitize_text_field( wp_unslash( $_POST['event_type'] ) ) : '';
		$location       = ! empty( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
		$event_group_id = ! empty( $_POST['event_group_id'] ) ? absint( $_POST['event_group_id'] ) : 0;

		// Save event logic here (e.g., creating a custom post type entry)
		$post_data = array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => 'publish',
			'post_type'    => bbgea_groups_event_get_post_type(), // Ensure this post type is registered
		);

		if ( $event_id ) {
			$post_data['ID'] = $event_id;
			$event_id        = wp_update_post( $post_data );
		} else {
			$event_id = wp_insert_post( $post_data );
		}

		// Check if the event was saved successfully
		if ( is_wp_error( $event_id ) ) {
			wp_send_json_error( array( 'feedback' => 'Failed to save event.' ) );
			wp_die();
		}

		// Save or update event meta data
		update_post_meta( $event_id, '_event_start_date', $start_date );
		update_post_meta( $event_id, '_event_end_date', $end_date );
		update_post_meta( $event_id, '_event_type', $type );
		update_post_meta( $event_id, '_event_location', $location );
		update_post_meta( $event_id, '_event_group_id', $event_group_id );

		// Prepare HTML for response
		$event = BB_Group_Event_Manager::get_instance()->get_event( $event_id );
		ob_start();
		bbgea_group_events_include_template( 'events/list-item.php', array( 'event' => $event ) );
		$event_html = ob_get_clean();

		// Send the HTML as a response
		wp_send_json_success( array( 'event' => $event_html ) );
		wp_die();
	}


	/**
	 * Fetch the user RSVP status.
	 * @since 1.0.0
	 */
	public function fetch_user_rsvp() {
		// Check nonce for security.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'bbgea_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );

			return;
		}

		// Validate and sanitize event ID and user ID.
		$event_id = isset( $_GET['event_id'] ) ? intval( $_GET['event_id'] ) : 0;
		$user_id  = get_current_user_id();

		if ( ! $event_id || ! $user_id ) {
			wp_send_json_error( array( 'message' => 'Invalid event ID or user not logged in' ) );

			return;
		}

		$rsvp = BB_Group_Event_Manager::get_instance()->get_rsvp( $event_id, $user_id );

		// Send the response.
		wp_send_json_success( array( 'rsvp_data' => $rsvp ) );
	}

	/**
	 * Handle the user RSVP.
	 * @since 1.0.0
	 */
	public function handle_user_rsvp() {
		// Check nonce for security.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'bbgea_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );

			return;
		}

		$event_id    = isset( $_POST['event_id'] ) ? intval( $_POST['event_id'] ) : 0;
		$group_id    = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : null;
		$rsvp_status = isset( $_POST['rsvp_status'] ) ? sanitize_text_field( wp_unslash( $_POST['rsvp_status'] ) ) : 'no';
		$comment     = isset( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '';
		$user_id     = get_current_user_id();

		if ( ! $event_id || ! $user_id ) {
			wp_send_json_error( array( 'message' => 'Invalid event ID or user not logged in' ) );

			return;
		}

		// Check if RSVP exists and update or insert accordingly.
		$rsvp = BB_Group_Event_Manager::get_instance()->get_rsvp( $event_id, $user_id );

		if ( $rsvp ) {
			BB_Group_Event_Manager::get_instance()->update_rsvp( $rsvp->ID, $rsvp_status, $comment );
		} else {
			BB_Group_Event_Manager::get_instance()->add_rsvp( $group_id, $event_id, $user_id, $rsvp_status, $comment );
		}

		if ( 'yes' === $rsvp_status ) {
			$status = sprintf( '<span>%s</span>', esc_html__( 'You\'re going!', 'buddyboss-group-events' ) );
		} elseif ( 'no' === $rsvp_status ) {
			$status = sprintf( '<span>%s</span>', esc_html__( 'You\'re not going!', 'buddyboss-group-events' ) );
		} else {
			$status = sprintf( '<span>%s</span>', esc_html__( 'You\'re maybe going!', 'buddyboss-group-events' ) );
		}

		$button_html = sprintf(
			'<button class="gb-edit-rsvp-event" data-event-id="%1$s" data-group-id="%2$s">%3$s</button>',
			esc_attr( $event_id ),
			esc_attr( $group_id ),
			esc_html__( 'Edit RSVP', 'buddyboss-group-events' )
		);

		// Send the response.
		wp_send_json_success(
			array(
				'message'     => esc_html__( 'RSVP updated successfully', 'buddyboss-group-events' ),
				'rsvp_button' => $status . $button_html,
			)
		);
	}

	/**
	 * Render the event item HTML.
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function load_events() {
		// Check nonce for security.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'bbgea_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( array( 'message' => 'Invalid nonce' ) );

			return;
		}

		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'upcoming';
		$group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;
		$paged    = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;

		$events_data = BB_Group_Event_Manager::get_instance()->get_events(
			array(
				'status'   => $status,
				'paged'    => $paged,
				'group_id' => $group_id,
			)
		);

		$events_html = '';
		if ( ! empty( $events_data['events'] ) ) {
			foreach ( $events_data['events'] as $event ) {
				ob_start();
				bbgea_group_events_include_template( 'events/list-item.php', array( 'event' => $event ) );
				$events_html .= ob_get_clean();
			}
		} else {
			$events_html = wp_kses_post( '<p>No events found.</p>' );
		}

		// Generate pagination links using paginate_links()
		$pagination_html = '';
		if ( $events_data['pages'] > 1 ) {
			$pagination_html .= '<div class="bp-pagination-links bb-pagination bottom">';
			$pagination_html .= '<p class="pag-data">';
			$pag_args         = array( 'epage' => '%#%' );
			$pagination_html .= paginate_links(
				array(
					'base'      => add_query_arg( $pag_args, get_permalink() ),
					'format'    => '',
					'current'   => $paged,
					'total'     => $events_data['pages'],
					'prev_text' => __( '&larr;', 'buddyboss-group-events' ),
					'next_text' => __( '&rarr;', 'buddyboss-group-events' ),
					'mid_size'  => 1,
					'add_args'  => array(),
				)
			);
			$pagination_html .= '</p>';
			$pagination_html .= '</div>';
		}

		// Return both event items and pagination as JSON
		wp_send_json_success(
			array(
				'events_html'     => $events_html,
				'pagination_html' => $pagination_html,
			)
		);
	}
}
