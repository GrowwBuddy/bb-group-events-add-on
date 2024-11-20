<?php
/**
 * The template for displaying group events.
 * This template can be overridden by copying it to yourtheme/bb-group-events/bb-group-event.php.
 *
 * @package    BB_Group_Events
 * @subpackage Templates
 */

$user_id  = get_current_user_id();
$group_id = bp_get_current_group_id();
?>
<div class="gb-events-container">
	<div class="gb-group-actions-wrap events-actions-wrap">
		<h2 class="gb-title">
			<?php esc_html_e( 'Events', 'bb-group-events' ); ?>
			<div class="gb-event-actions">
				<div class="gb-event-tabs">
					<a href="#list" class="gb-tab active" data-tab="list"><?php esc_html_e( 'List', 'bb-group-events' ); ?></a>
					<a href="#calendar" class="gb-tab" data-tab="calendar"><?php esc_html_e( 'Calendar', 'bb-group-events' ); ?></a>
				</div>
				<?php if ( bbgea_can_manage_group() ) { ?>
					<div class="gb-event-btn">
						<a href="javascript:void(0);" id="gb-create-events" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>" class="gb-create-events button small outline">
							<i class="bb-icon-l bb-icon-plus"></i>
							<?php esc_html_e( 'Create Event', 'bb-group-events' ); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		</h2>
	</div>
	<div class="gb-event-wrap">
		<?php
		require bbgea_group_events_get_template( 'events/list.php' );
		require bbgea_group_events_get_template( 'events/calendar.php' );
		?>
	</div>
</div>
<div id="gb-group-event-modal" class="gb-group-event-modal"></div>