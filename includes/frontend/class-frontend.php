<?php

/**
 * The frontend class of Group Events for BuddyBoss.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Frontend
 */

namespace Group_Events_For_BuddyBoss;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class FrontEnd
 */
class FrontEnd {

	/**
	 * The instance of the class.
	 *
	 * @var FrontEnd
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0
	 * @return FrontEnd
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'group-events-for-buddyboss', GROUP_EVENTS_FOR_BUDDYBOSS_ROOT_ASSETS_URL_PATH . 'css/frontend.css', array(), GROUP_EVENTS_FOR_BUDDYBOSS_VERSION );
		wp_enqueue_script( 'group-events-for-buddyboss',
			GROUP_EVENTS_FOR_BUDDYBOSS_ROOT_ASSETS_URL_PATH . 'js/frontend.js',
			array( 'jquery' ),
			GROUP_EVENTS_FOR_BUDDYBOSS_VERSION,
			true );

		wp_localize_script(
			'group-events-for-buddyboss',
			'group_events_for_buddyboss',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	public function fetch_upcoming_events() {
		$events      = array();
		$event_query = new \WP_Query( array(
			'post_type'      => 'buddyboss_event',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'     => '_group_id',
					'value'   => bp_get_group_id(),
					'compare' => '=',
				),
			),
		) );

		if ( $event_query->have_posts() ) {
			while ( $event_query->have_posts() ) {
				$event_query->the_post();
				$event_id = get_the_ID();
				$event    = array(
					'id'          => $event_id,
					'status'      => 'Upcoming',
					'title'       => get_the_title(),
					'description' => get_the_content(),
					'location'    => get_post_meta( $event_id, '_location', true ),
					'group_id'    => get_post_meta( $event_id, '_group_id', true ),
					'date'        => get_the_date( 'Y-m-d', $event_id ),
					'attendees'   => array(),
				);

				$attendees = get_post_meta( $event_id, '_attendees', true );

				if ( ! empty( $attendees ) ) {
					foreach ( $attendees as $attendee_id ) {
						$attendee             = get_userdata( $attendee_id );
						$event['attendees'][] = array(
							'id'     => $attendee_id,
							'name'   => $attendee->display_name,
							'avatar' => get_avatar_url( $attendee_id ),
						);
					}
				}

				$event['attendees_count'] = count( $event['attendees'] );


				$events[] = $event;
			}
		}
		wp_reset_postdata();

		return $events;
	}
}
