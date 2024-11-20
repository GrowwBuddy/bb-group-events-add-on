<?php
/**
 * The template for displaying create event form.
 * This template can be overridden by copying it to yourtheme/bb-group-events/events/manage/create-event.php.
 * @package    BB_Group_Events
 * @subpackage Templates
 */
?>
<script type="text/html" id="tmpl-event-form-template">
	<div class="modal-mask bb-white bbm-model-wrap">
		<div class="modal-wrapper">
			<div id="gb-group-event-open-popup" class="modal-container gb-group-event-open-popup">
				<input type="hidden" name="event_group_id" id="event_group_id" value="{{{ data.group_id }}}">
				<input type="hidden" name="event_id" id="event_id" value="{{{ data.event_id || '' }}}">

				<header class="bb-model-header">
					<h4>{{{ data.mode === 'edit' ? '<?php esc_html_e( 'Edit Event', 'bb-group-events' ); ?>' : '<?php esc_html_e( 'Create Event', 'bb-group-events' ); ?>' }}}</h4>
					<a class="bb-model-close-button" id="gb-group-event-modal-close" href="#"><span class="bb-icon-l bb-icon-times"></span></a>
				</header>

				<div class="bb-model-content">
					<!-- Event Title -->
					<div class="bb-field-wrap">
						<label for="event_title"><?php esc_html_e( 'Event Title:', 'bb-group-events' ); ?></label>
						<input type="text" id="event_title" name="event_title" value="{{{ data.title || '' }}}" placeholder="<?php esc_html_e( 'Enter Event Title', 'bb-group-events' ); ?>">
					</div>

					<!-- Event Description -->
					<div class="bb-field-wrap">
						<label for="event_description"><?php esc_html_e( 'Event Description:', 'bb-group-events' ); ?></label>
						<textarea id="event_description" name="event_description">{{{ data.description || '' }}}</textarea>
					</div>
					<!-- Event Start Date & Time -->
					<div class="bb-field-wrap">
						<label for="event_start_date_time"><?php esc_html_e( 'Event Start Date & Time:', 'bb-group-events' ); ?></label>
						<input type="datetime-local" id="event_start_date_time" name="event_start_date_time" value="{{{ data.start_date_time || '' }}}">
					</div>

					<!-- Event End Date & Time -->
					<div class="bb-field-wrap">
						<label for="event_end_date_time"><?php esc_html_e( 'Event End Date & Time:', 'bb-group-events' ); ?></label>
						<input type="datetime-local" id="event_end_date_time" name="event_end_date_time" value="{{{ data.end_date_time || '' }}}">
					</div>

					<!-- Event Type -->
					<div class="bb-field-wrap">
						<label for="event_type"><?php esc_html_e( 'Event Type:', 'bb-group-events' ); ?></label>
						<select name="event_type" id="event_type">
							<option value="meeting" {{{ data.type === 'meeting' ? 'selected' : '' }}}><?php esc_html_e( 'Meeting', 'bb-group-events' ); ?></option>
							<option value="webinar" {{{ data.type === 'webinar' ? 'selected' : '' }}}><?php esc_html_e( 'Webinar', 'bb-group-events' ); ?></option>
							<option value="workshop" {{{ data.type === 'workshop' ? 'selected' : '' }}}><?php esc_html_e( 'Workshop', 'bb-group-events' ); ?></option>
						</select>
					</div>

					<!-- Event Location -->
					<div class="bb-field-wrap">
						<label for="event_location"><?php esc_html_e( 'Event Location:', 'bb-group-events' ); ?></label>
						<input type="text" id="event_location" name="event_location" value="{{{ data.location || '' }}}">
					</div>
				</div>

				<footer class="bb-model-footer">
					<a class="button" id="gb-group-event-save-submit">{{{ data.mode === 'edit' ? '<?php esc_html_e( 'Update Event', 'bb-group-events' ); ?>' : '<?php esc_html_e( 'Create Event', 'bb-group-events' ); ?>' }}}</a>
				</footer>
			</div>
		</div>
	</div>
</script>
