<?php

$nonce       = wp_create_nonce( 'save_group_extension_setting' );
$is_edit     = isset( $_GET['event_id'] ) && 'edit' === $_GET['action'];
$event_id    = ! empty( $_GET['event_id'] ) ? $_GET['event_id'] : 0;
$group_event = get_post( $event_id );
?>
    <div class="gb-event-form">
        <h4><?php $is_edit ? _e( 'Edit Event', 'group-events-for-buddyboss' ) : _e( 'Create Event', 'group-events-for-buddyboss' ); ?></h4>
        <input type="hidden" name="save_group_extension_setting" value="<?php echo esc_attr( $nonce ); ?>">
        <input type="hidden" name="event_id" value="<?php echo esc_attr( $event_id ); ?>">
        <p>
            <label for="event_title"><?php esc_html_e( 'Event Title:', 'group-events-for-buddyboss' ); ?></label>
            <input type="text" id="event_title" name="event_title" value="<?php echo $is_edit ? get_the_title( $event_id ) : ''; ?>" required>
        </p>
        <p>
            <label for="event_description"><?php esc_html_e( 'Event Description:', 'group-events-for-buddyboss' ); ?></label>
			<?php
			$content   = $is_edit ? $group_event->post_content : '';
			$editor_id = 'event_description';
			$settings  = array(
				'textarea_name' => 'event_description',
				'media_buttons' => false,
				'textarea_rows' => 10,
				'tinymce'       => array(
					'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,blockquote',
					'toolbar2' => '',
				),
			);
			wp_editor( $content, $editor_id, $settings );
			?>
        </p>
        <p>
            <label for="event_start_date_time"><?php esc_html_e( 'Event Start Date & Time:', 'group-events-for-buddyboss' ); ?></label>
            <input type="datetime-local" id="event_start_date_time" name="event_start_date_time"
                   value="<?php echo $is_edit ? get_post_meta( $event_id, '_start_date', true ) : ''; ?>" required>
        <p>
            <label for="event_end_date_time"><?php esc_html_e( 'Event End Date & Time:', 'group-events-for-buddyboss' ); ?></label>
            <input type="datetime-local" id="event_end_date_time" name="event_end_date_time" value="<?php echo $is_edit ? get_post_meta( $event_id, '_end_date', true ) : ''; ?>"
                   required>
        </p>
        <p>
            <label for="event_date"><?php esc_html_e( 'Event Type:', 'group-events-for-buddyboss' ); ?></label>
            <select name="event_type" id="event_type">
                <option value="online"><?php esc_html_e( 'Online', 'group-events-for-buddyboss' ); ?></option>
                <option value="offline"><?php esc_html_e( 'Offline', 'group-events-for-buddyboss' ); ?></option>
            </select>
        </p>
        <p>
            <label for="event_date"><?php esc_html_e( 'Event Location:', 'group-events-for-buddyboss' ); ?></label>
            <input type="text" id="event_location" name="event_location" value="<?php echo $is_edit ? get_post_meta( $event_id, '_location', true ) : ''; ?>">
        </p>
        <p>
            <label for="event_date"><?php esc_html_e( 'Event Link:', 'group-events-for-buddyboss' ); ?></label>
            <input type="text" id="event_link" name="event_link" value="<?php echo $is_edit ? get_post_meta( $event_id, '_link', true ) : ''; ?>">
        </p>
        <p>
            <label for="event_date"><?php esc_html_e( 'Event Capacity:', 'group-events-for-buddyboss' ); ?></label>
            <input type="number" id="event_capacity" name="event_capacity" value="<?php echo $is_edit ? get_post_meta( $event_id, '_capacity', true ) : ''; ?>">
        </p>
        <div id="event-form-response"></div>
    </div>
<?php
