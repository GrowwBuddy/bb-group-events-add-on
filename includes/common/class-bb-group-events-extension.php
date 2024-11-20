<?php

/**
 * Forums BuddyBoss Group Extension Class
 *
 * This file is responsible for connecting Forums to BuddyBoss's Groups
 * Component. It's a great example of how to perform both simple and advanced
 * techniques to manipulate Forums' default output.
 *
 * @package BB_Group_Events
 * @todo    maybe move to BuddyBoss Forums once bbPress 1.1 can be removed
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BB_Group_Events_Extension' ) && class_exists( 'BP_Group_Extension' ) ) {
	/**
	 * Loads Group Extension for Group Events
	 *
	 * @since 1.0.0
	 *
	 */
	class BB_Group_Events_Extension extends BP_Group_Extension {

		/**
		 * BB_Group_Events_Extension constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$args = array(
				'slug'              => 'group-events',
				'name'              => __( 'Events', 'bb-group-events-add-on' ),
				'nav_item_position' => 50,
				'display_hook'      => 'bp_template_content',
				'screens'           => array(
					'create' => array(
						'name'        => __( 'Create Events', 'bb-group-events-add-on' ),
						'enabled'     => true,
						'slug'        => 'create-events',
						'submit_text' => __( 'Create Event', 'bb-group-events-add-on' ),
					),

				),
			);
			parent::init( $args );
		}

		/**
		 * Display the content of the Events tab
		 *
		 * @since 1.0.0
		 */
		public function display( $group_id = null ) {
			include bbgea_group_events_get_template( 'bb-group-event.php' );
		}
	}
}
