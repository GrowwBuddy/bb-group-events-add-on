<?php

/**
 * The admin class of Group Events for BuddyBoss.
 *
 * @since 1.0
 */
function bbgea_disable_group_event( $disable = false ) {
	return (bool) apply_filters( 'bbgea_disable', (bool) get_option( 'bbgea-disable', $disable ) );
}

/**
 * Get the post type for group events.
 * @since 1.0
 */
function bbgea_groups_event_get_post_type() {
	return BB_Group_Events_DB::get_instance()->group_event_post_type;
}

/**
 * Get the taxonomy for group events.
 * @since 1.0
 */
function bbgea_can_manage_group( $user_id = '', $group_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( empty( $group_id ) ) {
		$group_id = bp_get_current_group_id();
	}

	return groups_is_user_admin( $user_id, $group_id ) || groups_is_user_mod( $user_id, $group_id );
}

/**
 * Get the group event.
 *
 * @since 1.0
 */
function bbgea_get_group_event_rsvp( $event_id, $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$rsvp = BB_Group_Event_Manager::get_instance()->get_rsvp( $event_id, $user_id );

	return $rsvp;
}


/**
 * Get template for group events.
 *
 * @since 1.0
 */
function bbgea_group_events_include_template( $template_name, $args = array() ) {
	// Look for the template file in the active theme's folder first.
	$theme_template = locate_template( 'bb-group-events/' . $template_name );

	// If found in the theme, use it; otherwise, fallback to the plugin template.
	if ( $theme_template ) {
		$template = $theme_template;
	} else {
		$template = BB_GROUP_EVENTS_PLUGIN_DIR_PATH . 'templates/' . $template_name;
	}

	// Pass arguments to the template
	if ( ! empty( $args ) ) {
		extract( $args );
	}

	// Include the template file
	include $template;
}

/**
 *  Get the template for group events.
 *
 * @since 1.0.0
 * @param string $template_name The name of the template file.
 * @return string
 */
function bbgea_group_events_get_template( $template_name ) {
	// Look for the template file in the active theme's folder first.
	$theme_template = locate_template( 'bb-group-events/' . $template_name );

	// If found in the theme, use it; otherwise, fallback to the plugin template.
	if ( $theme_template ) {
		$template = $theme_template;
	} else {
		$template = BB_GROUP_EVENTS_PLUGIN_DIR_PATH . 'templates/' . $template_name;
	}
	return $template;
}

/**
 * Get the group event.
 *
 * @since 1.0
 * @param int $event_id The event ID.
 *
 * @return array
 */
function bbgea_get_event_attendees( $event_id ) {
	$rsvps = BB_Group_Event_Manager::get_instance()->get_rsvps_by_event( $event_id );

	$attendees = array();
	foreach ( $rsvps as $rsvp ) {
		$attendees[] = array(
			'id'      => $rsvp->user_id,
			'name'    => get_the_author_meta( 'display_name', $rsvp->user_id ),
			'avatar'  => get_avatar_url( $rsvp->user_id ),
			'status'  => $rsvp->status,
			'comment' => $rsvp->comment,
		);
	}

	return $attendees;
}

/**
 * Get the user role in a group.
 *
 * @since 1.0.0
 * @param int $user_id The user ID.
 * @param int $group_id The group ID.
 * @return string
 */
function bbgea_get_user_role_in_group( $user_id, $group_id ) {
	if ( groups_is_user_admin( $user_id, $group_id ) ) {
		$button_text = apply_filters( 'bp_group_organizer_label_text', get_group_role_label( $group_id, 'organizer_singular_label_name' ), $group_id, get_group_role_label( $group_id, 'organizer_singular_label_name' ) );
	} elseif ( groups_is_user_mod( $user_id, $group_id ) ) {
		$button_text = apply_filters( 'bp_group_moderator_label_text', get_group_role_label( $group_id, 'moderator_singular_label_name' ), $group_id, get_group_role_label( $group_id, 'moderator_singular_label_name' ) );
	} else {
		$button_text = apply_filters( 'bp_group_member_label_text', get_group_role_label( $group_id, 'member_singular_label_name' ), $group_id, get_group_role_label( $group_id, 'member_singular_label_name' ) );
	}
	return $button_text;
}
