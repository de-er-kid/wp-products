jQuery(document).ready(function($) {
    // Handle clicking on the "Featured" star icon
    $( '.wbthnk-featured-action' ).on( 'click', function( event ) {
        event.preventDefault();

        var $star = $( this ).find( '.wbthnk-featured' ),
            post_id = $( this ).data( 'post-id' ),
            is_featured = $star.hasClass( 'dashicons-star-filled' ) ? 'no' : 'yes';

        // Make the AJAX request to toggle the "Featured" status
        $.ajax( {
            type: 'POST',
            url: wbthnk_product_featured.ajax_url,
            data: {
                action: 'wbthnk_make_product_featured',
                post_id: post_id,
                _wpnonce: wbthnk_product_featured.nonce
            },
            success: function() {
                // Update the "Featured" star icon
                if ( is_featured == 'yes' ) {
                    $star.removeClass( 'dashicons-star-empty' ).addClass( 'dashicons-star-filled' );
                } else {
                    $star.removeClass( 'dashicons-star-filled' ).addClass( 'dashicons-star-empty' );
                }
                alert("Product Featured meta changed");
                console.log("Ajax request sent successfully");
            },
            error: function() {
                alert( 'An error occurred. Please try again.' );
            }
        } );
    } );
});
