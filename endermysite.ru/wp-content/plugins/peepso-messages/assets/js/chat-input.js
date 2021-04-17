(function( $, _, peepso, factory ) {

	window.PsChatInput = factory( $, _, peepso );

})( jQuery, _, peepso, function( $, _, peepso ) {

/**
 * Create new chat text input.
 * @class PsChatInput
 */
function PsChatInput() {
	this.create();
}

peepso.npm.objectAssign( PsChatInput.prototype, peepso.npm.EventEmitter.prototype, /** @lends PsChatInput.prototype */ {
	/**
	 * Chat input template.
	 * @type {string}
	 */
	template: peepsochatdata.windowInputTemplate,

	/**
	 * Initialize chat input.
	 */
	create: function() {
		this.$el = $( this.template );
		this.$textarea = this.$el.find('textarea');

		this.$textarea.on('keydown', $.proxy( this.onKeyDown, this ));
		this.$textarea.on('input', $.proxy( this.onInput, this ));
		this.$textarea.on('click', $.proxy( this.onClick, this ));
		this.$textarea.on('focus', $.proxy( this.onFocus, this ));
		this.$textarea.on('blur', $.proxy( this.onBlur, this ));

		// Initialize photo upload.
		if ( window.peepsophotosdata ) {
			this.$el.addClass('ps-chat-with-photo');
			this.$upload = this.$el.find('.ps-icon-camera').show();
			this.$file = this.$el.find('input[type=file]');
			this.$upload.on('click', $.proxy( this.onUploadPhoto, this ));
			this.uploadPhotoInit();
		}
	},

	/**
	 * Set focus to current input.
	 */
	focus: function() {
		this.$textarea.focus();
	},

	/**
	 * Initialize upload photo.
	 */
	uploadPhotoInit: function() {
		var $doc = $( document ),
			dataName = 'ps-chat-drop-intercept',
			isIntercepted = $doc.data( dataName ),
			parentLookupTimer;

		// Disable default drop action on chat window.
		if ( ! isIntercepted ) {
			$doc.data( dataName, 1 );
			$doc.bind( 'drop dragover', function( e ) {
				var isChatWindow = $( e.target ).closest( '.ps-chat-window' ).length;
				if ( isChatWindow ) {
					e.preventDefault();
				}
			});
		}

		this.$file.psFileupload({
			formData: {
				user_id: peepsodata.currentuserid
			},
			singleFileUploads: false,
			sequentialUploads: false,
			replaceFileInput: false,
			pasteZone: null,
			dropZone: this.$el,
			dataType: 'json',
			url: peepsodata.ajaxurl_legacy + 'photosajax.upload_photo',
			add: $.proxy(function( e, data ) {
				this.validatePhoto( data );
			}, this ),
			done: $.proxy(function( e, data ) {
				var response = data.result;
				if ( response.success ) {
					this.emit('submit', '', {
						type: 'photo',
						'files[]': response.data.files
					});
				}
			}, this )
		});

		// Ugly hack! Add messages container as drop target.
		parentLookupTimer = setInterval( $.proxy(function() {
			var $parent = this.$el.parent();
			if ( $parent ) {
				clearInterval( parentLookupTimer );
				this.$file.psFileupload( 'option', 'dropZone', $parent.add( $parent.prev() ) );
			}
		}, this ), 1000 );
	},

	/**
	 * Upload photo.
	 */
	uploadPhoto: function() {
		this.$file.trigger('click');
	},

	/**
	 * Validate selected photo. Upload it if validation check passed.
	 */
	validatePhoto: function( data ) {
		var file,
			fileSize = 0;

		// Check file extension.
		for ( var i = 0; i < data.files.length; i++ ) {
			file = data.files[i];
			if ( !(/\.(gif|jpg|jpeg|tiff|png)$/i).test( file.name ) ) {
				psmessage.show('', $('#photo-supported-format').text() ).fade_out( psmessage.fade_time );
				return false;
			}
			fileSize += parseInt( file.size );
		}

		var req = {
			size: fileSize,
			filesize: fileSize,
			photos: data.files.length
		};

		var uploadId = (new Date()).getTime() + Math.floor( Math.random() * 1000 );
		this.emit('photos_added', data.files.length, uploadId );

		var that = this;
		peepso.postJson('photosajax.validate_photo_upload', req, function( response ) {
			if ( response.success ) {
				data.submit();
			} else {
				that.emit('photos_cancel', uploadId );
				psmessage.show('', response.errors[0] ).fade_out( psmessage.fade_time );
			}
		});
	},

	/**
	 * Handles keydown event.
	 * @param {event} e Keyboard event.
	 */
	onKeyDown: function( e ) {
		if ( e.keyCode === 13 && !e.shiftKey ) {
			e.preventDefault();
			e.stopPropagation();
			this.emit('submit', $.trim( e.target.value ));
			this.$textarea.val('');
			this.autosize();
		}
	},

	/**
	 * Handles input change.
	 * @param {event} e Keyboard event.
	 */
	onInput: function( e ) {
		this.autosize();
		this.emit('change', e.target.value );
	},

	/**
	 * Emits `click` event.
	 * @param {event} e Keyboard event.
	 */
	onClick: function( e ) {
		this.emit('click', e );
	},

	/**
	 * Emits `focus` event.
	 * @param {event} e Keyboard event.
	 */
	onFocus: function( e ) {
		this.emit('focus', e );
	},

	/**
	 * Emits `blur` event.
	 * @param {event} e Keyboard event.
	 */
	onBlur: function( e ) {
		this.emit('blur', e );
	},

	/**
	 * Handle click on camera icon.
	 */
	onUploadPhoto: function() {
		this.uploadPhoto();
	},

	/**
	 * Resize textarea height based on it's content.
	 * @function
	 */
	autosize: _.debounce(function() {
        this.$textarea[0].style.height = '';
        this.$textarea[0].style.height = Math.min( +this.$textarea[0].scrollHeight, 100 ) + 'px';
	}, 1 )
});

return PsChatInput;

});
