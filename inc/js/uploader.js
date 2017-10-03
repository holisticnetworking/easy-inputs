/* Admin scripts for the Reactive Theme */
jQuery(document).ready(function($) {
    // Media uploader add image:
    $( document ).on('click', '.set-image', function( evt ) {
        // Stop the anchor's default behavior
        evt.preventDefault();
        var target  = $(evt.currentTarget).siblings('input').attr('id');
        console.log(target);
        // Display the media uploader
        renderMediaUploader($, target);
    });

    // Media uploader remove image:
    $( document ).on('click', '.remove-image', function( evt ) {
        // Stop the anchor's default behavior
        evt.preventDefault();
        var target  = $(evt.currentTarget).siblings('input').attr('id');
        // Display the media uploader
        removeImage($, target);
    });
});

/**
 * Callback function for the 'click' event of the 'Set Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @since 0.1.0
 */
function renderMediaUploader($, target) {
    'use strict';

    var file_frame;

    /**
     * If an instance of file_frame already exists, then we can open it
     * rather than creating a new instance.
     */
    if ( undefined !== file_frame ) {
        file_frame.open();
        return;
    }

    file_frame = wp.media.frames.file_frame = wp.media({
        frame:    'post',
        state:    'insert',
        multiple: false
    });

    file_frame.on( 'insert', function() {
        var json	= file_frame.state().get( 'selection' ).first().toJSON();
        var img		= $('<img />');
            img.attr( 'src', json.url );
            img.css( 'maxWidth', '100px' );
        var set	= $( '<a />' );
            set.attr( 'href', 'javascript:;' );
            set.attr( 'title', 'Set Background Image' );
            set.attr( 'class', 'set-image' );
        set.append( img );
        var abreak	= $( '<br />' );
        var remove	= $( '<a />' );
            remove.attr( 'href', 'javascript:;' );
            remove.attr( 'title', 'Remove Background Image' );
            remove.attr( 'class', 'remove-image' );
            remove.html( 'Remove Background Image' );
        $( '#' + target + '-image .hide-if-no-js' ).empty()
            .append( set )
            .append( abreak )
            .append( remove );
        $( '#' + target ).val( json.url );
    });
    // Now display the actual file_frame
    file_frame.open();
}

function removeImage( $, target ) {
    $( '#styling_background_image' ).val( '' );
    $( '#' + target + ' .hide-if-no-js' ).html( '<a title="Set Background Image" href="javascript:;" class="set-image">Set Background Image</a><br /><a title="Remove Background Image" href="javascript:;" class="remove-image">Remove Background Image</a>' );
    $( '.remove-image' ).css( 'display', 'none' );
}