<?php
/**
 * The admin class of Group Events for BuddyBoss.
 *
 * @package    BB_Group_Events
 * @subpackage Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class BB_Group_Events_Admin_MetaBox
 */
class BB_Group_Events_Admin_MetaBox {

	/**
	 * The instance of the class.
	 *
	 * @var BB_Group_Events_Admin_MetaBox
	 */
	private static $instance;

	/**
	 * Return the plugin instance
	 *
	 * @since 1.0.0
	 * @return BB_Group_Events_Admin_MetaBox
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 * @since 1.0.0
	 */
	public function init() {
		// Register metaboxes
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ) );

		// Save metabox data
		add_action( 'save_post', array( $this, 'save_metabox_data' ) );

		add_action( 'wp_ajax_bbgea_get_groups', array( $this, 'bbgea_get_groups' ) );
	}

	/**
	 * Register the metaboxes.
	 * @since 1.0.0
	 */
	public function register_metaboxes() {
		add_meta_box(
			'bbgea_event_details_metabox',
			__( 'Event Details', 'buddyboss-group-events' ),
			array( $this, 'render_event_details_metabox' ),
			bbgea_groups_event_get_post_type(),
			'normal',
			'high'
		);

		add_meta_box(
			'bbgea_selected_group_metabox',
			__( 'Selected Group', 'buddyboss-group-events' ),
			array( $this, 'render_selected_group_metabox' ),
			bbgea_groups_event_get_post_type(),
			'side',
			'high'
		);

		add_meta_box(
			'bbgea_group_members_metabox',
			__( 'Manage Event Members', 'buddyboss-group-events' ),
			array( $this, 'render_group_members_metabox' ),
			bbgea_groups_event_get_post_type(),
			'normal',
			'high'
		);
	}

	/**
	 * Render Event Details Metabox.
	 * @since 1.0.0
	 */
	public function render_event_details_metabox( $post ) {
		wp_nonce_field( 'bbgea_details_nonce', 'bbgea_nonce' );

		$event_id   = $post->ID;
		$start_date = get_post_meta( $event_id, '_event_start_date', true );
		$end_date   = get_post_meta( $event_id, '_event_end_date', true );
		$location   = get_post_meta( $event_id, '_event_location', true );
		$event_type = get_post_meta( $event_id, '_event_type', true );
		$attendees  = bbgea_get_event_attendees( $event_id );

		?>
		<table class="form-table">
			<tr>
				<th><label for="event_start_date_time"><?php esc_html_e( 'Start Date', 'buddyboss-group-events' ); ?></label></th>
				<td>
					<input type="datetime-local" name="start_date" id="event_start_date_time" class="widefat" value="<?php echo esc_attr( $start_date ); ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="event_end_date_time"><?php esc_html_e( 'End Date', 'buddyboss-group-events' ); ?></label></th>
				<td>
					<input type="datetime-local" name="end_date" id="event_end_date_time" class="widefat" value="<?php echo esc_attr( $end_date ); ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="location"><?php esc_html_e( 'Location', 'buddyboss-group-events' ); ?></label></th>
				<td>
					<input type="text" name="location" id="location" class="widefat" value="<?php echo esc_attr( $location ); ?>" />
				</td>
			</tr>
			<tr>
				<th><label for="type"><?php esc_html_e( 'Type', 'buddyboss-group-events' ); ?></label></th>
				<td>
					<select name="type" id="type" class="widefat">
						<option value=""><?php esc_html_e( 'Select Type', 'buddyboss-group-events' ); ?></option>
						<option value="meeting" <?php selected( 'meeting', $event_type ); ?>><?php esc_html_e( 'Meeting', 'buddyboss-group-events' ); ?></option>
						<option value="webinar" <?php selected( 'webinar', $event_type ); ?>><?php esc_html_e( 'Webinar', 'buddyboss-group-events' ); ?></option>
						<option value="workshop" <?php selected( 'workshop', $event_type ); ?>><?php esc_html_e( 'Workshop', 'buddyboss-group-events' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="attendees"><?php esc_html_e( 'RSVP Members', 'buddyboss-group-events' ); ?></label></th>
				<td>
					<table class="widefat bb-attendees-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'ID', 'buddyboss-group-events' ); ?></th>
								<th><?php esc_html_e( 'Name', 'buddyboss-group-events' ); ?></th>
								<th><?php esc_html_e( 'RSVP Status', 'buddyboss-group-events' ); ?></th>
							</tr>
						</thead>
						<tbody class="tbody">
							<?php foreach ( $attendees as $attendee ) : ?>
								<tr>
									<td><?php echo esc_html( $attendee['id'] ); ?></td>
									<td>
										<a href="<?php echo esc_url( bp_core_get_user_domain( $attendee['id'] ) ); ?>"><?php echo esc_html( bp_core_get_user_displayname( $attendee['id'] ) ); ?></a>
									</td>
									<td><?php echo esc_html( $attendee['status'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * Render Selected Group Metabox.
	 * @since 1.0.0
	 */
	public function render_selected_group_metabox( $post ) {
		$connected_group = get_post_meta( $post->ID, '_event_group_id', true );

		echo '<select name="connected_group" id="connected_group_select" class="select2-dropdown" style="width:100%">';

		// If a group is already selected, display it as the initial value.
		if ( $connected_group ) {
			$group = groups_get_group( $connected_group );
			echo '<option value="' . esc_attr( $group->id ) . '" selected>' . esc_html( $group->name ) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Render Group Members Metabox.
	 * @since 1.0.0
	 */
	public function render_group_members_metabox( $item ) {
		$group_id = get_post_meta( $item->ID, '_event_group_id', true );
		// JavaScript variable, which will help with group member autocomplete.
		$members = array(
			'admin'  => array(),
			'mod'    => array(),
			'member' => array(),
		);

		$pagination = array(
			'admin'  => array(),
			'mod'    => array(),
			'member' => array(),
		);

		foreach ( $members as $type => &$member_type_users ) {
			$page_qs_key       = $type . '_page';
			$current_type_page = isset( $_GET[ $page_qs_key ] ) ? absint( $_GET[ $page_qs_key ] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$member_type_query = new BP_Group_Member_Query(
				array(
					'group_id'   => $group_id,
					'group_role' => array( $type ),
					'type'       => 'alphabetical',
					'per_page'   => 10,
					'page'       => $current_type_page,
				)
			);

			$member_type_users   = $member_type_query->results;
			$pagination[ $type ] = bp_groups_admin_create_pagination_links( $member_type_query, $type );
		}

		// Echo out the JavaScript variable.
		echo '<script>var group_id = "' . esc_js( $item->id ) . '";</script>';

		// Loop through each profile type.
		foreach ( $members as $member_type => $type_users ) :
			?>

			<div class="bb-groups-member-rsvp" id="bb-groups-member-rsvp-<?php echo esc_attr( $member_type ); ?>">

				<h3>
					<?php
					switch ( $member_type ) :
						case 'admin':
							esc_html_e( 'Organizers', 'buddyboss-group-events' );
							break;
						case 'mod':
							esc_html_e( 'Moderators', 'buddyboss-group-events' );
							break;
						case 'member':
							esc_html_e( 'Members', 'buddyboss-group-events' );
							break;
					endswitch;
					?>
				</h3>

				<div class="bp-group-admin-pagination table-top">
					<?php echo wp_kses_post( $pagination[ $member_type ] ); ?>
				</div>

				<?php if ( ! empty( $type_users ) ) : ?>

					<table class="widefat bp-group-members">
						<thead>
						<tr>
							<th scope="col" class="uid-column"><?php esc_html_e( 'ID', 'buddyboss-group-events' ); ?></th>
							<th scope="col" class="uname-column"><?php esc_html_e( 'Name', 'buddyboss-group-events' ); ?></th>
							<th scope="col" class="urole-column"><?php esc_html_e( 'RSVP status', 'buddyboss-group-events' ); ?></th>
						</tr>
						</thead>

						<tbody>

						<?php foreach ( $type_users as $type_user ) : ?>
							<tr>
								<th scope="row" class="uid-column"><?php echo esc_html( $type_user->ID ); ?></th>

								<td class="uname-column">
									<a style="float: left;" href="<?php echo esc_url( bp_core_get_user_domain( $type_user->ID ) ); ?>">
										<?php
										echo wp_kses_post(
											bp_core_fetch_avatar(
												array(
													'item_id' => $type_user->ID,
													'width'   => '32',
													'height'  => '32',
												)
											)
										);
										?>
									</a>

									<span style="margin: 8px; float: left;"><?php echo esc_url( bp_core_get_userlink( $type_user->ID ) ); ?></span>
								</td>


								<td class="urole-column">
									<?php
									$rsvp        = BB_Group_Event_Manager::get_instance()->get_rsvp( $item->ID, $type_user->ID );
									$rsvp_status = $rsvp ? $rsvp->status : 'no';

									?>
									<label for="bbgea_member_rsvp-<?php echo esc_attr( $type_user->ID ); ?>" class="screen-reader-text">
										<?php
										/* translators: accessibility text */
										esc_html_e( 'Select group role for member', 'buddyboss-group-events' );
										?>
									</label>
									<select class="bbgea_member_rsvp" id="bbgea_member_rsvp-<?php echo esc_attr( $type_user->ID ); ?>" name="bbgea_member_rsvp[<?php echo esc_attr( $type_user->ID ); ?>]">
										<option class="no" value="no" <?php selected( 'no', $rsvp_status ); ?>><?php esc_html_e( 'No', 'buddyboss-group-events' ); ?></option>
										<option class="yes" value="yes" <?php selected( 'yes', $rsvp_status ); ?>><?php esc_html_e( 'Yes', 'buddyboss-group-events' ); ?></option>
										<option class="maybe" value="maybe" <?php selected( 'maybe', $rsvp_status ); ?>><?php esc_html_e( 'Maybe', 'buddyboss-group-events' ); ?></option>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>

						</tbody>
					</table>

				<?php else : ?>

					<p class="bp-groups-no-members description"><?php esc_html_e( 'No members of this type', 'buddyboss-group-events' ); ?></p>

				<?php endif; ?>

			</div><!-- .bb-groups-member-rsvp -->

			<?php
		endforeach;
	}

	/**
	 * Save metabox data.
	 * @since 1.0.0
	 */
	public function save_metabox_data( $post_id ) {
		if ( ! isset( $_POST['bbgea_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['bbgea_nonce'] ), 'bbgea_details_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['event_details'] ) ) {
			update_post_meta( $post_id, '_event_details', sanitize_textarea_field( wp_unslash( $_POST['event_details'] ) ) );
		}

		if ( isset( $_POST['connected_group'] ) ) {
			update_post_meta( $post_id, '_event_group_id', intval( $_POST['connected_group'] ) );
		}

		if ( isset( $_POST['bbgea_member_rsvp'] ) ) {
			$event_id = $post_id;
			$rsvps    = array_map( 'sanitize_text_field', wp_unslash( $_POST['bbgea_member_rsvp'] ) );

			foreach ( $rsvps as $user_id => $status ) {
				$event_rsvp = BB_Group_Event_Manager::get_instance()->get_rsvp( $event_id, $user_id );
				$group_id   = get_post_meta( $event_id, '_event_group_id', true );

				if ( $event_rsvp ) {
					BB_Group_Event_Manager::get_instance()->update_rsvp( $event_rsvp->ID, $status );
				} else {
					if ( 'no' === $status ) {
						continue;
					}
					BB_Group_Event_Manager::get_instance()->add_rsvp( $group_id, $event_id, $user_id, $status );
				}
			}
		}
	}

	/**
	 * AJAX callback to get BuddyBoss groups for Select2.
	 * @since 1.0.0
	 */
	public function bbgea_get_groups() {
		check_ajax_referer( 'bbgea_admin_nonce', 'nonce' );

		$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$groups = groups_get_groups(
			array(
				'search_terms' => $search,
				'per_page'     => 10,  // Limit the results to avoid heavy load.
			)
		);

		$results = array();
		foreach ( $groups['groups'] as $group ) {
			$results[] = array(
				'id'   => $group->id,
				'text' => $group->name,
			);
		}

		wp_send_json( $results );
	}
}
