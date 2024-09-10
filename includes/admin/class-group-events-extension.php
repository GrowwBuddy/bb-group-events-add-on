<?php

/**
 * Forums BuddyBoss Group Extension Class
 *
 * This file is responsible for connecting Forums to BuddyBoss's Groups
 * Component. It's a great example of how to perform both simple and advanced
 * techniques to manipulate Forums' default output.
 *
 * @package BuddyBoss\Group_Events
 * @todo    maybe move to BuddyBoss Forums once bbPress 1.1 can be removed
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Group_Events_Extension' ) && class_exists( 'BP_Group_Extension' ) ) {
	/**
	 * Loads Group Extension for Group Events
	 *
	 * @since 1.0.0
	 *
	 */
	class Group_Events_Extension extends BP_Group_Extension {
		public function __construct() {
			$args = array(
				'slug'              => 'events',
				'name'              => __( 'Events', 'group-events-for-buddyboss' ),
				'nav_item_position' => 200,
				'access'            => $this->showTabOnView(),
				'screens'           => array(
					'edit'   => array(
						'name'     => __( 'Events', 'group-events-for-buddyboss' ),
						'enabled'  => true,
						'slug'     => 'events',
						'position' => 10,
					),
					'create' => array(
						'name'     => __( 'Create Event', 'group-events-for-buddyboss' ),
						'enabled'  => true,
						'slug'     => 'create',
						'position' => 20,
					),
					'admin'  => array(
						'metabox_context'  => 'normal',
						'metabox_priority' => 'core',
					),
				),
			);
			parent::init( $args );
		}

		/**
		 * Determine who can see the tab
		 *
		 * @since BuddyBoss 1.0.0
		 */
		protected function showTabOnView() {
			if ( ! $currentGroup = groups_get_current_group() ) {
				return 'noone';
			}

			return 'public';
		}

		public function display( $group_id = null ) {
			$event_id = get_query_var( 'page' );

			if ( ! empty( $event_id ) ) {
				$this->single_screen_disaply( $event_id );
			} else {
				$group_id    = bp_get_group_id();
				$event_query = new WP_Query( array(
					'post_type'      => 'buddyboss_event',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						array(
							'key'     => '_group_id',
							'value'   => $group_id,
							'compare' => '=',
						),
					),
				) );

				include GROUP_EVENTS_FOR_BUDDYBOSS_PLUGIN_DIR_PATH . 'views/display.php';
			}
		}

		public function settings_screen( $group_id = null ) {
			if ( isset( $_GET['action'] ) && ( 'create' === $_GET['action'] || 'edit' === $_GET['action'] ) ) {
				$this->create_event_screen( $group_id );
			} else {
				$event_query = new WP_Query( array(
					'post_type'      => 'buddyboss_event',
					'posts_per_page' => - 1,
					'meta_query'     => array(
						array(
							'key'     => '_group_id',
							'value'   => $group_id,
							'compare' => '=',
						),
					),
				) );
				?>
                <div class="gb-event-form">
                    <h4><?php _e( 'Events', 'group-events-for-buddyboss' ); ?></h4>
                    <a href="<?php echo bp_get_group_permalink( groups_get_current_group() ); ?>admin/events/?action=create"><?php _e( 'Create Event',
							'group-events-for-buddyboss' ); ?></a>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th><?php _e( 'Event Title', 'group-events-for-buddyboss' ); ?></th>
                        <th><?php _e( 'Event Date', 'group-events-for-buddyboss' ); ?></th>
                        <th><?php _e( 'Actions', 'group-events-for-buddyboss' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					if ( $event_query->have_posts() ) {
						while ( $event_query->have_posts() ) {
							$event_query->the_post();
							?>
                            <tr>
                                <td><?php the_title(); ?></td>
                                <td><?php echo get_the_date(); ?></td>
                                <td>
                                    <a href="<?php the_permalink(); ?>"><?php _e( 'View', 'group-events-for-buddyboss' ); ?></a>
                                    <a href="<?php echo bp_get_group_permalink( groups_get_current_group() ); ?>admin/events/?action=edit&event_id=<?php the_ID(); ?>"><?php _e( 'Edit',
											'group-events-for-buddyboss' ); ?></a>
                                </td>
                            </tr>
							<?php
						}
					} else {
						?>
                        <tr>
                            <td colspan="3"><?php _e( 'No events found', 'group-events-for-buddyboss' ); ?></td>
                        </tr>
						<?php
					}
					?>
                    <tbody>
                </table>
                <input type="submit" style="display: none;">
				<?php
			}
		}

		public function create_event_screen( $group_id = null ) {
			include GROUP_EVENTS_FOR_BUDDYBOSS_ROOT_VIEW_DIR_PATH . 'create-event.php';
		}

		public function settings_screen_save( $group_id = null ) {
			if ( ! isset( $_POST['save_group_extension_setting'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['save_group_extension_setting'] ) ),
					'save_group_extension_setting' ) ) {
				bp_core_add_message( __( 'Nonce verification failed', 'group-events-for-buddyboss' ), 'error' );
			}

			$event_title       = sanitize_text_field( $_POST['event_title'] );
			$event_description = wp_kses_post( $_POST['event_description'] );
			$event_date        = sanitize_text_field( $_POST['event_date'] );
			$event_id          = isset( $_POST['event_id'] ) ? $_POST['event_id'] : 0;

			if ( ! $event_title || ! $event_description || ! $event_date ) {
				bp_core_add_message( __( 'All fields are required', 'group-events-for-buddyboss' ), 'error' );
			}

			if ( $event_id ) {
				$post_id = wp_update_post( array(
					'ID'           => $event_id,
					'post_title'   => $event_title,
					'post_content' => $event_description,
					'post_date'    => $event_date,
				) );
			} else {
				$new_post = array(
					'post_title'   => $event_title,
					'post_content' => $event_description,
					'post_date'    => $event_date,
					'post_type'    => 'buddyboss_event',
					'post_status'  => 'publish',
					'meta_input'   => array(
						'_group_id' => $group_id,
					),
				);
				$post_id  = wp_insert_post( $new_post );
				// Update group meta
				$event_ids = groups_get_groupmeta( $group_id, '_event_ids', true );
				if ( ! is_array( $event_ids ) ) {
					$event_ids = [];
				}
				$event_ids[] = $post_id;
				groups_update_groupmeta( $group_id, '_event_ids', $event_ids );
			}

			if ( is_wp_error( $post_id ) ) {
				bp_core_add_message( $post_id->get_error_message(), 'error' );
			}

			bp_core_add_message( __( 'Event saved successfully', 'group-events-for-buddyboss' ), 'success' );
		}

		public function single_screen_disaply( $event_id ) {
			$group_event = get_post( $event_id );

			?>
            <div class="gb-event-form">
                <h4><?php echo get_the_title( $event_id ); ?></h4>
                <p><?php echo get_the_date( '', $event_id ); ?></p>
                <p><?php echo $group_event->post_content; ?></p>
                <button>SRVP</button>
            </div>
			<?php
		}


	}
}