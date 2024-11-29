<?php
/**
 * The template for displaying single event.
 * This template can be overridden by copying it to yourtheme/bb-group-events/single-event.php.
 *
 * @package    BB_Group_Events
 * @subpackage Templates
 */

$event_id    = $event['id'];
$start_date  = gmdate( 'D, M d, Y, h:i A T', strtotime( $event['start_date'] ) );
$end_date    = $event['end_date'];
$group_id    = $event['group_id'];
$title       = $event['title'];
$description = $event['description'];
$attendees   = $event['attendees'];
$is_rsvp     = $event['is_rsvp'];
?>
	<div class="gb-event-item" data-event-id="<?php echo esc_attr( $event_id ); ?>">
		<?php if ( bbgea_can_manage_group() ) { ?>
			<a class="gb-action-dots" href="javascript:void(0);">
				<i class="bb-icon-f bb-icon-ellipsis-v"></i>
			</a>
			<div class="gb-dropdown-menu" style="display: none;">
				<ul>
					<li>
						<a href="javascript:void(0);" class="gb-edit-action" data-event-id="<?php echo esc_attr( $event_id ); ?>">
							<?php esc_html_e( 'Edit', 'buddyboss-group-events' ); ?>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" class="gb-delete-action" data-event-id="<?php echo esc_attr( $event_id ); ?>">
							<?php esc_html_e( 'Delete', 'buddyboss-group-events' ); ?>
						</a>
					</li>
				</ul>
			</div>
		<?php } ?>
		<div class="gb-event-date">
			<?php echo esc_html( $start_date ); ?>
		</div>
		<div class="gb-event-title">
			<a href="<?php echo esc_url( get_permalink( $event['id'] ) ); ?>">
				<?php echo esc_html( $title ); ?>
			</a>
		</div>
		<div class="gb-event-description"><?php echo wp_kses_post( wp_trim_words( $description, 20 ) ); ?></div>
		<div class="gb-event-footer">
			<div class="gb-attendees">
				<?php
				$attendees_count = count( $attendees );
				if ( $attendees_count > 0 ) {
					$attendees = array_slice( $attendees, 0, 3 );
					foreach ( $attendees as $attendee ) {
						echo sprintf( '<img src="%s" alt="%s" class="gb-avatar">', esc_url( $attendee['avatar'] ), esc_attr( $attendee['name'] ) );
					}
					if ( $attendees_count > 3 ) {
						echo sprintf( '<span class="gb-attendees-count">+%d %s</span>', esc_html( $attendees_count - 3 ), esc_html__( 'Attendees', 'buddyboss-group-events' ) );
					}
				}
				?>
			</div>
			<?php if ( is_user_logged_in() && $end_date > gmdate( 'Y-m-d H:i:s' ) ) { ?>
				<div class="gb-attend-btn">
					<?php
					if ( $is_rsvp ) {
						if ( 'yes' === $is_rsvp->status ) {
							$status = __( 'You\'re going!', 'buddyboss-group-events' );
						} elseif ( 'no' === $is_rsvp->status ) {
							$status = __( 'You\'re not going!', 'buddyboss-group-events' );
						} else {
							$status = __( 'You\'re maybe going!', 'buddyboss-group-events' );
						}
						?>
						<span> <?php echo esc_html( $status ); ?></span>
						<button class="gb-edit-rsvp-event" data-event-id="<?php echo esc_attr( $event_id ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
							<?php esc_html_e( 'Edit RSVP', 'buddyboss-group-events' ); ?>
						</button>
					<?php } else { ?>
						<button class="gb-attend-event" data-event-id="<?php echo esc_attr( $event_id ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
							<?php esc_html_e( 'Attend', 'buddyboss-group-events' ); ?>
						</button>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php