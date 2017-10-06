/* Admin scripts for the Reactive Theme */
(function( $ ) {
    'use strict';
    $(function() {
        $( '.set-image' ).on( 'click', function( evt ) {
            var file_frame, input, trigger, remove, img;
            trigger = $(evt.currentTarget);
            input   = trigger.siblings('input[type="hidden"]');
            evt.preventDefault();
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
                var json;
                json = file_frame.state().get( 'selection' ).first().toJSON();
                if ( 0 > $.trim( json.url.length ) ) {
                    return;
                }
                // Set our input value:
                input.attr('value', json.url);

                // Set our image preview:
                img = $('<img />');
                    img.attr('src', json.sizes.thumbnail.url);
                    img.addClass('preview');
                trigger.html(img);
            });

            // Now display the actual file_frame
            file_frame.open();
        });
        $( '.remove-image' ).on( 'click', function( evt ) {
            // Stop the anchor's default behavior
            evt.preventDefault();
            // Remove image
            var input, trigger, add, img;
            trigger = $(evt.currentTarget);
            add     = trigger.siblings('a.set-image');
            input   = trigger.siblings('input[type="hidden"]');

            add.html('Set Image');
            input.attr('value', '');
        });
    });
})( jQuery );