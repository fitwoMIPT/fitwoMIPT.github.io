(function( $, factory ) {

	factory( $ );

})( jQuery, function( $ ) {

	var mediaUploader,
		$doc = $( document.body );

	$doc.on( 'click', '.advads_avatar_upload', function( e ) {
		var $button = $( this );

		e.preventDefault();

		// If the media uploader is already exists, reopen it.
		if ( mediaUploader ) {
			mediaUploader.open();
			return;
		}

		// Create the media uploader.
		mediaUploader = wp.media.frames.mediaUploader = wp.media( {
			id: 'advads_type_avatar_wp_media',
			title: $button.data( 'uploaderTitle' ),
			button: { text: $button.data( 'uploaderButtonText' ) },
			library: { type: 'image' },
			multiple: false // only allow one file to be selected
		} );

		// When an image is selected, run a callback.
		mediaUploader.on( 'select', function() {
			var newAvatar,
				selection = mediaUploader.state().get( 'selection' );

			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();
				if ( 0 === index ) {
					newAvatar = '<img src="'+ attachment.url + '" title="' + attachment.title + '" alt="' + attachment.alt + '"/>';
					$( '#advads-avatar-id' ).val( attachment.id );
					$( '#advads-avatar-preview' ).html( newAvatar );
					$( '#advads-avatar-edit-link' ).attr( 'href', attachment.editLink );
				}
			});
		});

		// Finally, open the modal
		mediaUploader.open();
	});

});
