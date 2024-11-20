<?php
/**
 * The template for displaying edit rsvp.
 * This template can be overridden by copying it to yourtheme/bb-group-events/edit-rsvp.php.
 * @package    BB_Group_Events
 * @subpackage Templates
 *
 */
?>
<script type="text/html" id="tmpl-edit-rsvp-template">
	<div class="modal-mask bb-white bbm-model-wrap">
		<div class="modal-wrapper">
			<div id="gb-group-event-open-popup" class="modal-container gb-group-event-open-popup">

				<header class="bb-model-header">
					<h4><?php esc_html_e( 'Update your RSVP', 'bb-group-events-add-on' ); ?></h4>
					<a class="bb-model-close-button" id="gb-group-event-modal-close" href="#"><span class="bb-icon-l bb-icon-times"></span></a>
				</header>

				<div class="bb-model-content">
					<div class="bb-field-wrap">
						<label for="event_rsvp"><?php esc_html_e( 'RSVP:', 'bb-group-events-add-on' ); ?></label>
						<select name="event_rsvp" id="event_rsvp">
							<option value="yes" {{{ data.status === 'yes' ? 'selected' : '' }}}><?php esc_html_e( 'Yes', 'bb-group-events-add-on' ); ?></option>
							<option value="no" {{{ data.status === 'no' ? 'selected' : '' }}}><?php esc_html_e( 'No', 'bb-group-events-add-on' ); ?></option>
							<option value="maybe" {{{ data.status === 'maybe' ? 'selected' : '' }}}><?php esc_html_e( 'Maybe', 'bb-group-events-add-on' ); ?></option>
						</select>
					</div>

					<div class="bb-field-wrap">
						<label for="event_comment"><?php esc_html_e( 'Comment:', 'bb-group-events' ); ?></label>
						<textarea id="event_comment" name="event_comment">{{{ data.comment || '' }}}</textarea>
					</div>
				</div>

				<footer class="bb-model-footer">
					<button class="bb-button bb-button-primary" id="gb-update-rsvp" data-mode="edit" data-event-id="{{{ data.event_id }}}" data-group-id="{{{ data.group_id }}}"><?php esc_html_e( 'Update RSVP', 'bb-group-events' ); ?></button>
				</footer>
			</div>
		</div>
	</div>
</script>
