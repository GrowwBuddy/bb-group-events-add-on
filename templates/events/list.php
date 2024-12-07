<?php
/**
 * The template for displaying events list.
 * This template can be overridden by copying it to yourtheme/gb-gefbb/events/list.php.
 * @package    GB_GEFBB
 * @subpackage Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
} // Exit if accessed directly
?>
<div class="gb-event-list">
	<!-- Event Type Switch -->
	<div class="gb-event-left">
		<div class="gb-event-types">
			<a href="#upcoming" class="gb-event-type-btn active" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>"  data-event="upcoming"><?php esc_html_e( 'Upcoming Events', 'group-events-for-buddyboss' ); ?></a>
			<a href="#past" class="gb-event-type-btn" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>" data-event="past"><?php esc_html_e( 'Past Events', 'group-events-for-buddyboss' ); ?></a>
		</div>
	</div>

	<!-- Events List -->
	<div class="gb-event-right">
		<div class="gb-events-list gb-upcoming-events">
			<?php
			$events_data = GB_GEFBB_Manager::get_instance()->get_events(
				array(
					'status'   => 'upcoming',
					'paged'    => 1,
					'group_id' => bp_get_current_group_id(),
				)
			);

			if ( ! empty( $events_data['events'] ) ) {
				echo '<div class="gb-event-list-body">';
				foreach ( $events_data['events'] as $event ) {
					gb_gefbb_group_events_include_template( 'events/list-item.php', array( 'event' => $event ) );
				}
				echo '</div>';

				// Pagination
				if ( $events_data['pages'] > 1 ) {
					echo '<div class="gb-event-list-footer">';
					echo '<div class="bp-pagination-links bb-pagination bottom">';
					echo '<p class="pag-data">';
					$pag_args = array( 'epage' => '%#%' );
					echo wp_kses_post(
						paginate_links(
							array(
								'base'      => add_query_arg( $pag_args, get_permalink( bp_get_current_group_id() ) ),
								'format'    => '',
								'current'   => max( 1, 1 ),
								'total'     => $events_data['pages'],
								'prev_text' => __( '&larr;', 'group-events-for-buddyboss' ),
								'next_text' => __( '&rarr;', 'group-events-for-buddyboss' ),
								'mid_size'  => 1,
								'add_args'  => array(),
							)
						)
					);
					echo '</p>';
					echo '</div>';
					echo '</div>';
				}
			} else {
				echo wp_kses_post( '<p>No upcoming events found.</p>' );
			}
			?>
		</div>

		<!-- Past Events Section -->
		<div class="gb-events-list gb-past-events" style="display: none;">
			<?php
			$events_data = GB_GEFBB_Manager::get_instance()->get_events(
				array(
					'status'   => 'past',
					'paged'    => 1,
					'group_id' => bp_get_current_group_id(),
				)
			);

			if ( ! empty( $events_data['events'] ) ) {
				echo '<div class="gb-event-list-body">';
				foreach ( $events_data['events'] as $event ) {
					gb_gefbb_group_events_include_template( 'events/list-item.php', array( 'event' => $event ) );
				}
				echo '</div>';

				if ( $events_data['pages'] > 1 ) {
					echo '<div class="gb-event-list-footer">';
					echo '<div class="bp-pagination-links bb-pagination bottom">';
					echo '<p class="pag-data">';

					$pag_args = array( 'epage' => '%#%' );
					echo wp_kses_post(
						paginate_links(
							array(
								'base'      => add_query_arg( $pag_args, get_permalink( bp_get_current_group_id() ) ),
								'format'    => '',
								'current'   => max( 1, 1 ),
								'total'     => $events_data['pages'],
								'prev_text' => __( '&larr;', 'group-events-for-buddyboss' ),
								'next_text' => __( '&rarr;', 'group-events-for-buddyboss' ),
								'mid_size'  => 1,
								'add_args'  => array(),
							)
						)
					);
					echo '</p>';
					echo '</div>';
					echo '</div>';
				}
			} else {
				echo wp_kses_post( '<p>No past events found.</p>' );
			}
			?>
		</div>
	</div>
</div>

