<?php
/**
 * Single Event Template
 *
 * Template for displaying a single Group Event.
 *
 * This template can be overridden by copying it to yourtheme/bb-group-events/single-bb-group-event.php.
 *
 * @package BuddyBoss Group Events
 */

get_header();
$event_id         = get_the_ID();
$group_id         = get_post_meta( $event_id, '_event_group_id', true );
$start_date       = get_post_meta( $event_id, '_event_start_date', true );
$end_date         = get_post_meta( $event_id, '_event_end_date', true );
$location         = get_post_meta( $event_id, '_event_location', true );
$event_type       = get_post_meta( $event_id, '_event_type', true );
$group            = groups_get_group( $group_id );
$post_author_id   = get_post_field( 'post_author', $event_id );
$post_author_name = get_the_author_meta( 'display_name', $post_author_id );
$event_banner     = wp_get_attachment_url( get_post_thumbnail_id( $event_id ) );
$group_avatar_url = bp_get_group_avatar_url( $group );
$group_attendees  = bbgea_get_event_attendees( $event_id );

if ( empty( $group_avatar_url ) ) {
	$group_avatar_url = bbgea_dir_url( 'assets/img/group-default.png' );
}
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<div class="bb-group-event-header">
			<div class="bb-event-meta">
				<h1 class="bb-event-title">
					<?php echo wp_kses_post( get_the_title() ); ?>
				</h1>
			</div>
		</div>
		<div class="bb-group-event-body">
			<div class="bb-group-event-left">
				<!-- Banner Section -->
				<div class="banner-section">
					<?php if ( ! empty( $event_banner ) ) : ?>
						<div class="banner-image">
							<img src="<?php echo esc_url( $event_banner ); ?>" alt="<?php the_title_attribute(); ?>">
						</div>
					<?php endif; ?>
				</div>

				<div class="author-info">
					<div class="author-image">
						<a href="<?php echo esc_url( bp_core_get_user_domain( $post_author_id ) ); ?>">
							<?php
							echo wp_kses_post(
								bp_core_fetch_avatar(
									array(
										'item_id' => $post_author_id,
										'type'    => 'thumb',
									)
								)
							);
							?>
						</a>

					</div>
					<div class="bb-event-author-meta">
						<p class="bb-event-author">
							<?php
							echo sprintf(
								'%s: <br/><strong><a href="%s">%s</a></strong>',
								esc_html__( 'Created By', 'buddyboss-group-events' ),
								esc_url( bp_core_get_user_domain( $post_author_id ) ),
								esc_html( $post_author_name )
							);
							?>
						</p>
					</div>
				</div>

				<!-- Event Details Section -->
				<div class="details">
					<div class="event-description">
						<h2><?php esc_html_e( 'Details', 'buddyboss-group-events' ); ?></h2>
						<?php the_content(); ?>
					</div>
				</div>
			</div>
			<aside class="bb-group-event-right group-info">

				<div class="group-details">
					<h2><?php esc_html_e( 'Group Details', 'buddyboss-group-events' ); ?></h2>
					<div class="group-image">
						<a href="<?php echo esc_url( bp_get_group_permalink( $group ) . 'group-events/' ); ?>">
							<img src="<?php echo esc_url( $group_avatar_url ); ?>" alt="<?php echo esc_html( bp_get_group_name( $group ) ); ?>">
						</a>
					</div>
					<p class="group-name">
						<a href="<?php echo esc_url( bp_get_group_permalink( $group ) . 'group-events/' ); ?>">
							<?php echo esc_html( bp_get_group_name( $group ) ); ?>
						</a>
					</p>
					<p class="group-type">
						<?php echo wp_kses_post( bp_get_group_type( $group ) ); ?>
					</p>
				</div>
				<div class="event-details">
					<h2><?php esc_html_e( 'Event Details', 'buddyboss-group-events' ); ?></h2>
					<p class="event-date">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'Start Date', 'buddyboss-group-events' ),
							esc_html( gmdate( 'F j, Y', strtotime( $start_date ) ) )
						);
						?>
											</p>
					<p class="event-time">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'Start Time', 'buddyboss-group-events' ),
							esc_html( gmdate( 'g:i A', strtotime( $start_date ) ) )
						);
						?>
					</p>
					<p class="end-date">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'End Date', 'buddyboss-group-events' ),
							esc_html( gmdate( 'F j, Y', strtotime( $end_date ) ) )
						);
						?>
					</p>
					<p class="end-time">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'End Time', 'buddyboss-group-events' ),
							esc_html( gmdate( 'g:i A', strtotime( $end_date ) ) )
						);
						?>
					<p class="event-location">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'Location', 'buddyboss-group-events' ),
							esc_html( $location )
						);
						?>
					</p>
					<p class="event-type">
						<?php
						echo sprintf(
							'%s: <strong>%s</strong>',
							esc_html__( 'Type', 'buddyboss-group-events' ),
							esc_html( $event_type )
						);
						?>
					</p>
				</div>
			</aside>
		</div>
		<div class="bb-group-event-footer">
			<!-- Attendees Section -->
			<section class="attendees">
				<?php if ( is_user_logged_in() ) { ?>
				<h2>Attendees (<?php echo esc_html( count( $group_attendees ) ); ?>)</h2>
				<div class="attendee-grid">
					<?php
					if ( ! empty( $group_attendees ) ) {
						foreach ( $group_attendees as $group_attendee ) {
							?>
							<div class="attendee-card">
								<div class="attendee-image">
									<a href="<?php echo esc_url( bp_core_get_user_domain( $group_attendee['id'] ) ); ?>">
										<?php
										echo wp_kses_post(
											bp_core_fetch_avatar(
												array(
													'item_id' => $group_attendee['id'],
													'type' => 'thumb',
												)
											)
										);
										?>
									</a>
								</div>
								<p><strong><?php echo esc_html( $group_attendee['name'] ); ?></strong><br><?php echo esc_html( bbgea_get_user_role_in_group( $group_attendee['id'], $group_id ) ); ?></p>
							</div>
							<?php
						}
					} else {
						?>
						<p><?php esc_html_e( 'No attendees found.', 'buddyboss-group-events' ); ?></p>
					<?php } ?>
				</div>
				<?php } else { ?>
					<p><?php esc_html_e( 'Please login to see attendees.', 'buddyboss-group-events' ); ?></p>
				<?php } ?>
			</section>
			<!-- Attend Button -->
			<?php
			$event_rsvp = bbgea_get_group_event_rsvp( $event_id, get_current_user_id() );
			if ( is_user_logged_in() && $end_date > gmdate( 'Y-m-d H:i:s' ) ) {
				if ( $event_rsvp ) {
					if ( 'yes' === $event_rsvp->status ) {
						$status = __( 'You\'re going!', 'buddyboss-group-events' );
					} elseif ( 'no' === $event_rsvp->status ) {
						$status = __( 'You\'re not going!', 'buddyboss-group-events' );
					} else {
						$status = __( 'You\'re maybe going!', 'buddyboss-group-events' );
					}
					?>
				<div class="attend-button">
					<span> <?php echo esc_html( $status ); ?></span>
					<button class="gb-edit-rsvp-event" data-event-id="<?php echo esc_attr( $event_id ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
						<?php esc_html_e( 'Edit RSVP', 'buddyboss-group-events' ); ?>
					</button>
				</div>
				<?php } else { ?>
				<div class="attend-button">
					<button class="gb-attend-event" data-event-id="<?php echo esc_attr( $event_id ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>">
						<?php esc_html_e( 'Click to Attend Event', 'buddyboss-group-events' ); ?>
					</button>
				</div>
			<?php } ?>
			<?php } ?>

		</div>
	</main>
</div>

<?php get_footer(); ?>

<div id="gb-group-event-modal" class="gb-group-event-modal"></div>