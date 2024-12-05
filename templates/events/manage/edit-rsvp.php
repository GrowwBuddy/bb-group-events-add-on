<?php
/**
 * The template for displaying edit rsvp.
 * This template can be overridden by copying it to yourtheme/gb-gefbb/edit-rsvp.php.
 * @package    Group_Events_For_BuddyBoss
 * @subpackage Templates
 *
 */
?>
<script type="text/html" id="tmpl-edit-rsvp-template">
	<div class="modal-mask bb-white bbm-model-wrap">
		<div class="modal-wrapper">
			<div id="group-events-for-buddyboss-open-popup" class="modal-container group-events-for-buddyboss-open-popup">

				<header class="bb-model-header">
					<h4><?php esc_html_e( 'Update your RSVP', 'group-events-for-buddyboss' ); ?></h4>
					<a class="bb-model-close-button" id="group-events-for-buddyboss-modal-close" href="#"><span class="bb-icon-l bb-icon-times"></span></a>
				</header>

				<div class="bb-model-content">
					<div class="bb-field-wrap">
						<label for="event_rsvp"><?php esc_html_e( 'RSVP:', 'group-events-for-buddyboss' ); ?></label>
						<select name="event_rsvp" id="event_rsvp">
							<option value="yes" {{{ data.status === 'yes' ? 'selected' : '' }}}><?php esc_html_e( 'Yes', 'group-events-for-buddyboss' ); ?></option>
							<option value="no" {{{ data.status === 'no' ? 'selected' : '' }}}><?php esc_html_e( 'No', 'group-events-for-buddyboss' ); ?></option>
							<option value="maybe" {{{ data.status === 'maybe' ? 'selected' : '' }}}><?php esc_html_e( 'Maybe', 'group-events-for-buddyboss' ); ?></option>
						</select>
					</div>

					<div class="bb-field-wrap">
						<label for="event_comment"><?php esc_html_e( 'Comment:', 'group-events-for-buddyboss' ); ?></label>
						<textarea id="event_comment" name="event_comment">{{{ data.comment || '' }}}</textarea>
					</div>
				</div>

				<footer class="bb-model-footer">
					<button class="bb-button bb-button-primary" id="gb-update-rsvp" data-mode="edit" data-event-id="{{{ data.event_id }}}" data-group-id="{{{ data.group_id }}}"><?php esc_html_e( 'Update RSVP', 'group-events-for-buddyboss' ); ?></button>
				</footer>
			</div>
		</div>
	</div>
</script>
