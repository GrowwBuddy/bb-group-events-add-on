<?php

$upcoming_events = \Group_Events_For_BuddyBoss\FrontEnd::get_instance()->fetch_upcoming_events();
?>
<div class="gb-event-container">
    <div class="gb-event-view">
        <a href="javascript:void(0)" class="active"><?php esc_html_e( 'List', 'group-events-for-buddyboss' ); ?></a>
        <a href="javascript:void(0)"><?php esc_html_e( 'Calendar', 'group-events-for-buddyboss' ); ?></a>
    </div>
    <div class="gb-event-main">
        <div class="gb-event-left">
            <ul>
                <li class="active"><a href="javascript:void(0)"><?php esc_html_e( 'Upcoming', 'group-events-for-buddyboss' ); ?></a></li>
                <li><a href="javascript:void(0)"><?php esc_html_e( 'Past', 'group-events-for-buddyboss' ); ?></a></li>
            </ul>
        </div>
        <div class="gb-event-right">
            <div class="gb-event-items">
				<?php if ( ! empty( $upcoming_events ) ) { ?>
					<?php foreach ( $upcoming_events as $event ) { ?>
                        <div class="gb-event-item">
                            <span><?php echo esc_html( get_the_date( 'D, M d, Y, h:i A', $event['id'] ) ); ?></span>
                            <h3><?php echo esc_html( $event['title'] ); ?></h3>
                            <p><?php echo esc_html( $event['location'] ); ?></p>
                            <div class="gb-event-attendees">
                                <ul class="gb-event-attendees-list">
									<?php if ( ! empty( $event['attendees'] ) ) { ?>
										<?php foreach ( $event['attendees'] as $attendee ) { ?>
                                            <li><img src="<?php echo esc_url( $attendee['avatar'] ); ?>" alt="<?php echo esc_attr( $attendee['name'] ); ?>"></li>
										<?php } ?>
									<?php } ?>
                                </ul>
                                <span><?php echo esc_html( $event['attendees_count'] ); ?>+ attendees</span>
                            </div>
                        </div>
					<?php } ?>
				<?php } ?>
                <div class="gb-event-item">
                    <span>Date</span>
                    <h3>Event title</h3>
                    <p>Event status like location or online or passed or today</p>
                    <div class="gb-event-attendees">
                        <ul class="gb-event-attendees-list">
                            <li style="z-index: 5"><img src="https://via.placeholder.com/150" alt="attendee"></li>
                            <li style="z-index: 4"><img src="https://via.placeholder.com/150" alt="attendee"></li>
                            <li style="z-index: 3"><img src="https://via.placeholder.com/150" alt="attendee"></li>
                            <li style="z-index: 2"><img src="https://via.placeholder.com/150" alt="attendee"></li>
                            <li style="z-index: 1"><img src="https://via.placeholder.com/150" alt="attendee"></li>
                        </ul>
                        <span>5+ attendees</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
