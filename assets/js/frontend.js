class BuddyBossGroupEvents {
    constructor() {
        // Bind event handlers
        this.initEventHandlers();
    }

    initEventHandlers() {
        // Handle tab switching for List and Calendar
        jQuery( '.gb-tab' ).on( 'click', ( e ) => this.handleTabSwitch( e ) );
        // Handle event type switching for Upcoming and Past Events
        jQuery( '.gb-event-type-btn' ).on( 'click', ( e ) => this.handleEventTypeSwitch( e ) );
        // Toggle dropdown for event actions
        jQuery( '.gb-action-dots' ).on( 'click', ( e ) => this.toggleDropdown( e ) );
        // Close dropdown if clicked outside of it
        jQuery( document ).on( 'click', ( e ) => this.closeDropdownOnOutsideClick( e ) );
        // Open modal for creating a new group event
        jQuery( '.bp-nouveau' ).on( 'click', '.gb-create-events', ( e ) => this.openCreateEventModal( e ) );
        jQuery( '.bp-nouveau' ).on( 'click', '.gb-edit-action', ( e ) => this.openEditEventModal( e ) );
        // Close modal for group event creation
        jQuery( '.bp-nouveau' ).on( 'click', '#gb-group-event-modal-close', ( e ) => this.closeModal( e ) );
        jQuery( document ).on( 'click', '#gb-group-event-save-submit', ( e ) => this.saveEvent( e ) ); // Add submit event handler
        jQuery( document ).on( 'click', '.gb-edit-rsvp-event', ( e ) => this.loadEditRSVPTemplate( e ) );
        jQuery( document ).on( 'click', '.gb-attend-event, #gb-update-rsvp', ( e ) => this.saveUserRSVP( e ) );
        jQuery( document ).on( 'click', '.bb-pagination a.page-numbers', ( e ) => this.loadEvents( e ) );

        jQuery( document ).on( 'change', '#event_start_date_time', ( e ) => this.manageDateTime( e ) );
    }

    handleTabSwitch( e ) {
        e.preventDefault();

        // Remove active class from all tabs
        jQuery( '.gb-tab' ).removeClass( 'active' );

        // Add active class to the clicked tab
        jQuery( e.currentTarget ).addClass( 'active' );

        // Get the clicked tab type
        const tab = jQuery( e.currentTarget ).data( 'tab' );

        // Show or hide the event wrap based on the tab clicked
        if ( tab === 'list' ) {
            jQuery( '.gb-event-list' ).show();
            jQuery( '.gb-calendar-view' ).hide();
        } else if ( tab === 'calendar' ) {
            jQuery( '.gb-event-list' ).hide();
            jQuery( '.gb-calendar-view' ).show();
        }
    }

    handleEventTypeSwitch( e ) {
        e.preventDefault();

        // Remove active class from all event type buttons
        jQuery( '.gb-event-type-btn' ).removeClass( 'active' );

        // Add active class to the clicked button
        jQuery( e.currentTarget ).addClass( 'active' );

        // Hide all events lists
        jQuery( '.gb-events-list' ).hide();

        // Show the corresponding events section based on the selected event type
        const eventType = jQuery( e.currentTarget ).data( 'event' );
        if ( eventType === 'upcoming' ) {
            jQuery( '.gb-upcoming-events' ).show();
        } else if ( eventType === 'past' ) {
            jQuery( '.gb-past-events' ).show();
        }
    }

    toggleDropdown( e ) {
        e.preventDefault();
        const dropdown = jQuery( e.currentTarget ).closest( '.gb-event-item' ).find( '.gb-dropdown-menu' );
        dropdown.toggle();
    }

    closeDropdownOnOutsideClick( e ) {
        if ( !jQuery( e.target ).closest( '.gb-event-item' ).length ) {
            jQuery( '.gb-dropdown-menu' ).hide();
        }
    }

    openCreateEventModal( e ) {
        e.preventDefault();
        let data = {
            mode: 'create', // Or any other mode you want to define
            group_id: jQuery( e.currentTarget ).data( 'group-id' ), // Assuming you're passing the group ID
            title: '',
            description: '',
            // Add more fields as necessary
        };
        console.log(data);
        this.loadEventTemplate( null, data );
    }

    openEditEventModal( e ) {
        e.preventDefault();
        const eventId = jQuery( e.currentTarget ).data( 'event-id' );
        this.loadEventTemplate( eventId );
    }

    closeModal(e) {
        e.preventDefault();
        jQuery('#gb-group-event-modal').empty(); // Empty the modal content.

        // Destroy the TinyMCE editor instance
        wp.editor.remove('event_description');
    }

    loadEventTemplate( eventId = null, data = {} ) {
        // Clear previous error messages
        jQuery( '.error' ).removeClass( 'error' );
        // Load the template
        const template = wp.template( 'event-form-template' );
        // Prepare data for the template

        if ( eventId ) {
            // AJAX request to get event data by `eventId`
            jQuery.ajax( {
                type: 'GET',
                url: bbgea_object.ajax_url,
                data: {
                    action: 'fetch_group_event',
                    event_id: eventId,
                    _wpnonce: bbgea_object.nonce,
                },
                success: ( response ) => {
                    if ( response.success ) {
                        const eventData = response.data.event_data;
                        // Prepare data for the template
                        const data = {
                            mode: 'edit',
                            group_id: eventData.event_group_id,
                            event_id: eventId,
                            title: eventData.title,
                            description: eventData.description,
                            start_date_time: eventData.start_date,
                            end_date_time: eventData.end_date,
                            event_type: eventData.event_type,
                            location: eventData.location,
                        };

                        // Render the template with event data
                        jQuery( '#gb-group-event-modal' ).html( template( data ) );

                        // Initialize TinyMCE or Quicktags on the existing textarea
                        wp.editor.initialize('event_description', {
                            tinymce: true,
                            quicktags: true,
                            mediaButtons: false,
                        });

                    } else {
                        console.error( response.data.message );
                    }
                },
                error: ( xhr, status, error ) => {
                    console.error( "Error loading event data:", error );
                }
            } );
        } else {
            // Render the template with event data
            jQuery( '#gb-group-event-modal' ).html( template( data ) );

            // Initialize TinyMCE or Quicktags on the existing textarea
            wp.editor.initialize('event_description', {
                tinymce: true,
                quicktags: true,
                mediaButtons: false,
            });
        }

    }

    // Method to save event data from the modal form
    saveEvent( event ) {
        event.preventDefault();

        const target = jQuery( event.currentTarget );
        const eventId = jQuery( '#event_id' ).val();
        const title = jQuery( '#event_title' );
        const description = typeof tinyMCE !== 'undefined' && tinyMCE.get( 'event_description' ) ? tinyMCE.get( 'event_description' ).getContent() : jQuery( '#event_description' ).val();
        const startDate = jQuery( '#event_start_date_time' );
        const endDate = jQuery( '#event_end_date_time' );
        const eventType = jQuery( '#event_type' );
        const location = jQuery( '#event_location' );
        const event_group_id = jQuery( '#event_group_id' );

        if ( target.hasClass( 'saving' ) ) {
            return false;
        }

        // Validation
        if ( jQuery.trim( title.val() ) === '' ) {
            title.addClass( 'error' );
            return false;
        } else {
            title.removeClass( 'error' );
        }

        if ( jQuery.trim( startDate.val() ) === '' ) {
            startDate.addClass( 'error' );
            return false;
        } else {
            startDate.removeClass( 'error' );
        }

        target.addClass( 'saving' );
        target.attr( 'disabled', true );

        // Data to send via AJAX
        const data = {
            action: 'group_event_save',
            _wpnonce: bbgea_object.nonce,
            event_id: eventId,
            title: title.val(),
            description: description,
            start_date_time: startDate.val(),
            end_date_time: endDate.val(),
            event_type: eventType.val(),
            location: location.val(),
            event_group_id: event_group_id.val(),
        };

        // AJAX request to save the event
        jQuery.ajax(
            {
                type: 'POST',
                url: bbgea_object.ajax_url,
                data: data,
                success: ( response ) => {
                    setTimeout(
                        () => {
                            target.removeClass( 'saving' );
                            target.prop( 'disabled', false );
                        },
                        500
                    );

                    if ( response.success ) {
                        const eventElement = jQuery( `[data-event-id='${ eventId }']` );
                        const newStartDate = new Date( data.start_date_time );
                        const today = new Date();
                        let newContainer;

                        // Determine the new container based on the updated date
                        if ( newStartDate > today ) {
                            newContainer = jQuery( '.gb-upcoming-events' );
                        } else {
                            newContainer = jQuery( '.gb-past-events' );
                        }

                        if ( eventElement.length > 0 ) {
                            // Remove the existing event from its current section
                            eventElement.remove();
                        }

                        // Append or prepend the updated event to the correct section
                        if ( newContainer.find( '.gb-event-item' ).length === 0 ) {
                            newContainer.html( response.data.event );
                        } else {
                            newContainer.prepend( response.data.event );
                        }
                    } else {
                        // Handle error feedback
                        jQuery( '#gb-group-event-open-popup .bb-model-header' ).after( response.data.feedback );
                    }

                    // Close the modal
                    jQuery( '#gb-group-event-open-popup' ).find( '#gb-group-event-modal-close' ).trigger( 'click' );
                    //window.location.reload();
                },
                error: ( xhr, status, error ) => {
                    console.error( "Error saving event:", error );
                    target.removeClass( 'saving' ).prop( 'disabled', false );
                }
            }
        );
    }

    loadEditRSVPTemplate( e ) {
        // Load the template
        const template = wp.template( 'edit-rsvp-template' );
        const eventId = jQuery( e.currentTarget ).data( 'event-id' );
        jQuery.ajax({
            type: 'GET',
            url: bbgea_object.ajax_url,
            data: {
                action: 'fetch_user_rsvp',
                event_id: eventId,
                _wpnonce: bbgea_object.nonce,
            },
            success: (response) => {
                if (response.success) {
                    const data = response.data.rsvp_data;

                    // Render the template with the fetched data
                    jQuery('#gb-group-event-modal').html(template(data));
                } else {
                    console.error(response.data.message);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error fetching RSVP data:', error);
            }
        });
    }

    saveUserRSVP(e) {
        e.preventDefault();
        const button = jQuery(e.currentTarget);
        const eventId = button.data('event-id');
        const groupId = button.data('group-id') || null;
        const rsvpStatus = jQuery('#event_rsvp').val() || 'yes';  // Default RSVP status
        const comment = jQuery('#event_comment').val() || '';
        const mode = button.data('mode');

        button.prop('disabled', true); // Disable button to prevent multiple clicks

        // AJAX data payload
        const data = {
            action: 'save_user_rsvp',
            _wpnonce: bbgea_object.nonce,
            event_id: eventId,
            group_id: groupId,
            rsvp_status: rsvpStatus,
            comment: comment,
        };

        jQuery.ajax({
            type: 'POST',
            url: bbgea_object.ajax_url,
            data: data,
            success: (response) => {
                if (response.success) {
                    alert(response.data.message);
                    if( mode === 'edit' ) {
                        jQuery('#gb-group-event-modal').empty(); // Empty the modal content.
                    }
                    if( jQuery('.gb-events-list').length > 0 ) {
                        jQuery('.gb-events-list').find(`[data-event-id='${eventId}']`).find('.gb-attend-btn').html(response.data.rsvp_button);
                    } else {
                        jQuery('.bb-group-event-footer').find('.attend-button').html(response.data.rsvp_button);
                    }
                } else {
                    console.error(response.data.message);
                }
                button.prop('disabled', false);
            },
            error: (xhr, status, error) => {
                console.error('Error saving RSVP data:', error);
                button.prop('disabled', false); // Re-enable the button
            }
        });
    }

    loadEvents(e) {
        e.preventDefault();
        const page = jQuery(e.currentTarget).data('page') ||
            jQuery(e.currentTarget).attr('href').match(/epage=(\d+)/)[1]; // Retrieve the page number
        const eventType = jQuery('.gb-event-type-btn.active').data('event'); // Get the active event type
        const groupID = jQuery('.gb-event-type-btn.active').data('group-id'); // Get the active event type

        jQuery.ajax({
            url: bbgea_object.ajax_url,
            type: 'POST',
            data: {
                action: 'load_events',
                status: eventType,
                group_id: groupID,
                paged: page,
                _wpnonce: bbgea_object.nonce,
            },
            success: (response) => {
                if (response.success) {
                    // Update the events list based on the active event type
                    if (eventType === 'upcoming') {
                        jQuery('.gb-upcoming-events').find('.gb-event-list-body').html(response.data.events_html);
                        jQuery('.gb-upcoming-events').find('.gb-event-list-footer').html(response.data.pagination_html);
                    } else {
                        jQuery('.gb-past-events').find('.gb-event-list-body').html(response.data.events_html);
                        jQuery('.gb-past-events').find('.gb-event-list-footer').html(response.data.pagination_html);
                    }
                } else {
                    console.error(response.data.message);
                }
            },
            error: (xhr, status, error) => {
                console.error("Error loading events:", error);
            }
        });
    }

    manageDateTime( e ) {
        const startDate = jQuery( e.currentTarget ).val();
        jQuery( '#event_end_date_time' ).attr( 'min', startDate );
    }

}

// Initialize the BuddyBossGroupEvents class when the document is ready
jQuery( document ).ready(
    () => {
        new BuddyBossGroupEvents();
    }
);
