<?php
/**
 * The database class of the plugin.
 *
 * @package    BB_Group_Events
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class BB_Group_Event_Manager
 * @since 1.0.0
 */
class BB_Group_Event_Manager {

	/**
	 * The instance of the class.
	 * @since 1.0.0
	 * @var BB_Group_Event_Manager
	 */
	private static $instance;

	/**
	 * The name of the RSVP table.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name;

	/**
	 * Return the plugin instance.
	 *
	 * @since 1.0.0
	 * @return BB_Group_Event_Manager
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
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'bb_group_event_rsvp';
	}

	/**
	 * Insert an RSVP record.
	 *
	 * @param int    $group_id Group ID.
	 * @param int    $event_id Event ID.
	 * @param int    $user_id  User ID.
	 * @param string $status   RSVP status.
	 * @param string $comment  Optional. RSVP comment.
	 *
	 * @since 1.0.0
	 * @return int|false Insert ID or false on failure.
	 */
	public function add_rsvp( $group_id, $event_id, $user_id, $status, $comment = null ) {
		global $wpdb;

		$result = $wpdb->insert( // phpcs:ignore
			$this->table_name,
			array(
				'group_id'    => $group_id,
				'event_id'    => $event_id,
				'user_id'     => $user_id,
				'status'      => $status,
				'comment'     => $comment,
				'create_date' => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%d', '%s', '%s', '%s' )
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update an RSVP record.
	 *
	 * @param int    $id      Record ID.
	 * @param string $status  RSVP status.
	 * @param string $comment Optional. RSVP comment.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public function update_rsvp( $id, $status, $comment = null ) {
		global $wpdb;

		$result = $wpdb->update( // phpcs:ignore
			$this->table_name,
			array(
				'status'     => $status,
				'comment'    => $comment,
				'modif_date' => current_time( 'mysql' ),
			),
			array( 'ID' => $id ),
			array( '%s', '%s', '%s' ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Delete an RSVP record.
	 *
	 * @param int $id Record ID.
	 *
	 * @since 1.0.0
	 * @return bool True on success, false on failure.
	 */
	public function delete_rsvp( $id ) {
		global $wpdb;

		$result = $wpdb->delete( // phpcs:ignore
			$this->table_name,
			array( 'ID' => $id ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Get an RSVP record by event ID and user ID.
	 *
	 * @param int $event_id Event ID.
	 * @param int $user_id  User ID.
	 *
	 * @since 1.0.0
	 * @return object|null The RSVP record or null if not found.
	 */
	public function get_rsvp( $event_id, $user_id ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE event_id = %d AND user_id = %d", $event_id, $user_id ) ); // phpcs:ignore
	}

	/**
	 * Get all RSVPs for an event.
	 *
	 * @param int $event_id Event ID.
	 *
	 * @since 1.0.0
	 * @return array The RSVP records.
	 */
	public function get_rsvps_by_event( $event_id, $status = null ) {
		global $wpdb;

		$allow_status = array( 'yes', 'no', 'maybe' );

		if ( ! in_array( $status, $allow_status, true ) ) {
			$status = null;
		}

		if ( $status ) {
			$result = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"SELECT * FROM {$this->table_name} WHERE event_id = %d AND status = %s", // phpcs:ignore
					$event_id,
					$status
				)
			);
		} else {
			$result = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"SELECT * FROM {$this->table_name} WHERE event_id = %d", // phpcs:ignore
					$event_id
				)
			);
		}

		return $result;
	}

	/**
	 * Get all RSVPs for a user.
	 *
	 * @param int $user_id User ID.
	 *
	 * @since 1.0.0
	 * @return array The RSVP records.
	 */
	public function get_rsvps_by_user( $user_id ) {
		global $wpdb;

		$result = $wpdb->get_results( // phpcs:ignore
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE user_id = %d", // phpcs:ignore
				$user_id
			)
		);

		return $result;
	}

	/**
	 * Get event by event ID.
	 *
	 * @param int $event_id Event ID.
	 *
	 * @since 1.0.0
	 * @return array The event record or null if not found.
	 */
	public function get_event( $event_id ) {
		global $wpdb;

		$event = $this->prepare_event_item( $event_id );

		return $event;
	}

	/**
	 * Get all events.
	 *
	 * @param array $args Query arguments.
	 *
	 * @since 1.0.0
	 * @return array The events.
	 */
	public function get_events( $args = array() ) {
		$default_args = array(
			'post_type'      => bbgea_groups_event_get_post_type(),
			'posts_per_page' => 2,
			'paged'          => 1,
			'order'          => 'ASC',
		);

		$args = wp_parse_args( $args, $default_args );

		// Determine event status filter
		if ( isset( $args['status'] ) && 'upcoming' === $args['status'] ) {
			$args['meta_query'] = array( // phpcs:ignore
				array(
					'key'     => '_event_start_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				),
			);
		} elseif ( isset( $args['status'] ) && 'past' === $args['status'] ) {
			$args['meta_query'] = array( // phpcs:ignore
				array(
					'key'     => '_event_end_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
			);
			$args['order']      = 'DESC';
		}

		if ( ! empty( $args['group_id'] ) ) {
			if ( is_array( $args['group_id'] ) ) {
				$args['meta_query'][] = array(
					'key'     => '_event_group_id',
					'value'   => $args['group_id'],
					'compare' => 'IN',
					'type'    => 'NUMERIC',
				);
			} else {
				$args['meta_query'][] = array(
					'key'     => '_event_group_id',
					'value'   => $args['group_id'],
					'compare' => '=',
					'type'    => 'NUMERIC',
				);
			}
		}

		$query  = new WP_Query( $args );
		$result = array();

		if ( $query->have_posts() ) {
			$event_items = array();
			while ( $query->have_posts() ) {
				$query->the_post();
				$event_items[] = $this->prepare_event_item( get_the_ID() );
			}
			$result['events'] = $event_items;
			$result['total']  = $query->found_posts;
			$result['pages']  = $query->max_num_pages;
		}

		wp_reset_postdata();

		return $result;
	}

	/**
	 * Prepare an event item.
	 *
	 * @param int $event_id Event ID.
	 *
	 * @since 1.0.0
	 * @return array The event item.
	 */
	public function prepare_event_item( $event_id ) {
		$event    = get_post( $event_id );
		$group_id = get_post_meta( $event->ID, '_event_group_id', true );

		$event_item = array(
			'id'          => $event->ID,
			'title'       => $event->post_title,
			'description' => $event->post_content,
			'start_date'  => get_post_meta( $event->ID, '_event_start_date', true ),
			'end_date'    => get_post_meta( $event->ID, '_event_end_date', true ),
			'location'    => get_post_meta( $event->ID, '_event_location', true ),
			'type'        => get_post_meta( $event->ID, '_event_type', true ),
			'group_id'    => $group_id,
			'attendees'   => bbgea_get_event_attendees( $event->ID ),
			'is_rsvp'     => is_user_logged_in() ? bbgea_get_group_event_rsvp( $event->ID, get_current_user_id() ) : false,
		);

		return $event_item;
	}
}
