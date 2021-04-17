(function( root, factory ) {

	var moduleName = 'PsGroupAvatar';
	var moduleObject = factory( moduleName, root.jQuery );

	// export module
	if ( typeof module === 'object' && module.exports ) {
		module.exports = moduleObject;
	} else {
		root[ moduleName ] = moduleObject;
	}

})( window, function( moduleName, $ ) {

	return peepso.createClass( moduleName, peepso.npm.EventEmitter, {

		/**
		 * Class constructor.
		 * @param {HTMLInputElement} [file]
		 */
		__constructor: function( file ) {
			this._isTemp = false;

			// Auto-initialize file uploader early on due to webdriver need to inject file input to test it.
			this.$file = this._uploadInit( file );
		},

		/**
		 * Upload avatar url.
		 * @type {String}
		 */
		uploadUrl: peepsodata.ajaxurl_legacy + 'groupajax.avatar_upload?avatar',

		/**
		 * Upload avatar additional parameters.
		 * @return {Object}
		 */
		uploadParams: function() {
			return {
				group_id: peepsogroupsdata.group_id,
				_wpnonce: peepsodata.groupAvatar.uploadNonce
			};
		},

		/**
		 * Upload new avatar.
		 */
		upload: function() {
			if ( this.$file ) {
				this.$file.click();
			}
		},

		/**
		 * Initialize avatar uploader.
		 * @param {HTMLInputElement} [file]
		 * @return {jQuery}
		 */
		_uploadInit: function( file ) {
			var $div, $file;

			if ( file ) {
				$file = $( file );
			} else {
				$div = $('<div class="ps-js-uploader-avatar-group" />').css({ position: 'absolute', left: 0, top: -999 });
				$div.html('<input type="file" name="filedata" accept="image/*" />');
				$div.appendTo( document.body );
				$file = $div.find('input');
			}

			// Initialize uploader library
			$file.psFileupload({
				url: this.uploadUrl,
				formData: this.uploadParams(),
				replaceFileInput: false,
				dataType: 'json',
				add: $.proxy(function( e, data ) {
					var config = peepsodata.groupAvatar,
						file = data.files[ 0 ],
						fileTypes = /(\.|\/)(gif|jpe?g|png)$/i;

					if ( ! fileTypes.test( file.type ) ) {
						this.emit('uploadfail', config.textErrorFileType );
					} else if ( parseInt( file.size ) > config.uploadMaxSize ) {
						this.emit('uploadfail', config.textErrorFileSize );
					} else {
						data.submit();
					}
				}, this ),
				submit: $.proxy(function() {
					this.emit('uploadsubmit');
				}, this ),
				done: $.proxy(function( e, data ) {
					var imgAvatar, imgOriginal,
						json = data.result;

					if ( json.success ) {
						imgAvatar = json.data && json.data.image_url;
						imgOriginal = json.data && json.data.orig_image_url;
						this._isTemp = true;
						this.emit('uploaddone', imgAvatar, imgOriginal );
					} else {
						this.emit('uploadfail', json.errors );
					}
				}, this )
			});

			return $file;
		},

		/**
		 * Crop avatar into specific dimension.
		 * @param {Number} x1
		 * @param {Number} x2
		 * @param {Number} y1
		 * @param {Number} y2
		 * @param {Number} width
		 * @param {Number} height
		 * @return {jQuery.Deferred}
		 */
		crop: function( x1, y1, x2, y2, width, height ) {
			return $.Deferred( $.proxy(function( defer ) {
				peepso.postJson('groupajax.avatar_crop', {
					u: peepsogroupsdata.group_id,
					x: x1,
					y: y1,
					x2: x2,
					y2: y2,
					width: width,
					height: height,
					tmp: this._isTemp ? 1 : 0,
					_wpnonce: peepsodata.groupAvatar.uploadNonce
				}, $.proxy(function( json ) {
					if ( json && json.success ) {
						defer.resolveWith( this, [ json.data && json.data.image_url ] );
					} else {
						defer.rejectWith( this );
					}
				}, this ));
			}, this ));
		},

		/**
		 * Remove currently active group avatar image.
		 * @return {jQuery.Deferred}
		 */
		remove: function() {
			return $.Deferred( $.proxy(function( defer ) {
				peepso.postJson('groupajax.avatar_delete', {
					group_id: peepsogroupsdata.id,
					_wpnonce: peepsodata.groupAvatar.uploadNonce
				}, $.proxy(function( json ) {
					if ( json && json.success ) {
						defer.resolveWith( this, [ json.data && json.data.image_url ] );
					} else {
						defer.rejectWith( this );
					}
				}, this ));
			}, this ));
		},

		/**
		 * Finalize avatar update. Any avatar upload without finalizing it will be discarded.
		 * @return {jQuery.Deferred}
		 */
		finalize: function() {
			return $.Deferred( $.proxy(function( defer ) {
				peepso.postJson('groupajax.avatar_confirm', {
					group_id: peepsogroupsdata.id,
					module_id: peepsogroupsdata.module_id,
					_wpnonce: peepsodata.groupAvatar.uploadNonce
				}, $.proxy(function( json ) {
					if ( json && json.success ) {
						defer.resolveWith( this );
					} else {
						defer.rejectWith( this );
					}
				}, this ));
			}, this ));
		}

	});

});
