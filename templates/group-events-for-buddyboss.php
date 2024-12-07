<?php
/**
 * The template for displaying group events.
 * This template can be overridden by copying it to yourtheme/gb-gefbb/group-events-for-buddyboss.php.
 *
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Templates
 */

$user_id  = get_current_user_id();
$group_id = bp_get_current_group_id();
?>
<div class="gb-events-container">
	<div class="gb-group-actions-wrap events-actions-wrap">
		<h2 class="gb-title">
			<?php esc_html_e( 'Events', 'group-events-for-buddyboss' ); ?>
			<div class="gb-event-actions">
				<div class="gb-event-tabs">
					<a href="#list" class="gb-tab active" data-tab="list"><?php esc_html_e( 'List', 'group-events-for-buddyboss' ); ?></a>
					<a href="#calendar" class="gb-tab" data-tab="calendar"><?php esc_html_e( 'Calendar', 'group-events-for-buddyboss' ); ?></a>
				</div>
				<?php if ( gb_gefbb_can_manage_group() ) { ?>
					<div class="gb-event-btn">
						<a href="javascript:void(0);" id="gb-create-events" data-group-id="<?php echo esc_attr( bp_get_current_group_id() ); ?>" class="gb-create-events button small outline">
							<i class="bb-icon-l bb-icon-plus"></i>
							<?php esc_html_e( 'Create Event', 'group-events-for-buddyboss' ); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		</h2>
	</div>
	<div class="gb-event-wrap">
		<?php
		require gb_gefbb_group_events_get_template( 'events/list.php' );
		require gb_gefbb_group_events_get_template( 'events/calendar.php' );
		?>
	</div>
</div>
<div id="group-events-for-buddyboss-modal" class="group-events-for-buddyboss-modal"></div>