jQuery( document ).ready( function () {
    var menuOpen = jQuery( '#wpwrap #adminmenumain #adminmenuwrap #adminmenu #toplevel_page_buddyboss-platform ul.wp-submenu li' );

    // Set Groups selected on Group Type post types.
    if ( jQuery( 'body.buddypress.post-type-bb-group-event' ).length ) {
        var selectorGroups = jQuery( '#wpwrap #adminmenumain #adminmenuwrap #adminmenu .toplevel_page_buddyboss-platform ul.wp-submenu-wrap li a[href*="bp-groups"]' );
        jQuery( menuOpen ).removeClass( 'current' );
        jQuery( selectorGroups ).addClass( 'current' );
        jQuery( selectorGroups ).attr( 'aria-current', 'page' );
        jQuery( '#wpwrap #adminmenumain #adminmenuwrap #adminmenu .toplevel_page_buddyboss-platform ul.wp-submenu-wrap li' ).find( 'a[href*="bp-groups"]' ).parent().addClass( 'current' );
    }

    jQuery('.select2-dropdown').select2({

    });
    jQuery('#connected_group_select').select2({
        ajax: {
            url: BBGroupEvents.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    action: 'get_bb_groups',
                    nonce: BBGroupEvents.nonce,
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Select a Group',
        minimumInputLength: 2, // Require at least 2 characters to trigger search
    });

    jQuery( document ).on( 'change', '#event_start_date_time', function() {
        var start_date = jQuery( this ).val();
        jQuery( '#event_end_date_time' ).val( start_date );
    });
} );