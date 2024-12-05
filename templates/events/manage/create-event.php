<?php
/**
 * The template for displaying create event form.
 * This template can be overridden by copying it to yourtheme/gb-gefbb/events/manage/create-event.php.
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Templates
 */
?>
<script type="text/html" id="tmpl-event-form-template">
	<div class="modal-mask bb-white bbm-model-wrap">
		<div class="modal-wrapper">
			<div id="group-events-for-buddyboss-open-popup" class="modal-container group-events-for-buddyboss-open-popup">
				<input type="hidden" name="event_group_id" id="event_group_id" value="{{{ data.group_id }}}">
				<input type="hidden" name="event_id" id="event_id" value="{{{ data.event_id || '' }}}">

				<header class="bb-model-header">
					<h4>{{{ data.mode === 'edit' ? '<?php esc_html_e( 'Edit Event', 'group-events-for-buddyboss' ); ?>' : '<?php esc_html_e( 'Create Event', 'group-events-for-buddyboss' ); ?>' }}}</h4>
					<a class="bb-model-close-button" id="group-events-for-buddyboss-modal-close" href="#"><span class="bb-icon-l bb-icon-times"></span></a>
				</header>

				<div class="bb-model-content">
					<!-- Event Title -->
					<div class="bb-field-wrap">
						<label for="event_title"><?php esc_html_e( 'Event Title:', 'group-events-for-buddyboss' ); ?></label>
						<input type="text" id="event_title" name="event_title" value="{{{ data.title || '' }}}" placeholder="<?php esc_html_e( 'Enter Event Title', 'group-events-for-buddyboss' ); ?>">
					</div>

					<!-- Event Description -->
					<div class="bb-field-wrap">
						<label for="event_description"><?php esc_html_e( 'Event Description:', 'group-events-for-buddyboss' ); ?></label>
						<textarea id="event_description" name="event_description">{{{ data.description || '' }}}</textarea>
					</div>
					<!-- Event Start Date & Time -->
					<div class="bb-field-wrap">
						<label for="event_start_date_time"><?php esc_html_e( 'Event Start Date & Time:', 'group-events-for-buddyboss' ); ?></label>
						<input type="datetime-local" id="event_start_date_time" name="event_start_date_time" value="{{{ data.start_date_time || '' }}}">
					</div>

					<!-- Event End Date & Time -->
					<div class="bb-field-wrap">
						<label for="event_end_date_time"><?php esc_html_e( 'Event End Date & Time:', 'group-events-for-buddyboss' ); ?></label>
						<input type="datetime-local" id="event_end_date_time" name="event_end_date_time" value="{{{ data.end_date_time || '' }}}">
					</div>

					<!-- Event Type -->
					<div class="bb-field-wrap">
						<label for="event_type"><?php esc_html_e( 'Event Type:', 'group-events-for-buddyboss' ); ?></label>
						<select name="event_type" id="event_type">
							<option value="meeting" {{{ data.type === 'meeting' ? 'selected' : '' }}}><?php esc_html_e( 'Meeting', 'group-events-for-buddyboss' ); ?></option>
							<option value="webinar" {{{ data.type === 'webinar' ? 'selected' : '' }}}><?php esc_html_e( 'Webinar', 'group-events-for-buddyboss' ); ?></option>
							<option value="workshop" {{{ data.type === 'workshop' ? 'selected' : '' }}}><?php esc_html_e( 'Workshop', 'group-events-for-buddyboss' ); ?></option>
						</select>
					</div>

					<!-- Event Location -->
					<div class="bb-field-wrap">
						<label for="event_location"><?php esc_html_e( 'Event Location:', 'group-events-for-buddyboss' ); ?></label>
						<input type="text" id="event_location" name="event_location" value="{{{ data.location || '' }}}">
					</div>
				</div>

				<footer class="bb-model-footer">
					<a class="button" id="group-events-for-buddyboss-save-submit">{{{ data.mode === 'edit' ? '<?php esc_html_e( 'Update Event', 'group-events-for-buddyboss' ); ?>' : '<?php esc_html_e( 'Create Event', 'group-events-for-buddyboss' ); ?>' }}}</a>
				</footer>
			</div>
		</div>
	</div>
</script>
