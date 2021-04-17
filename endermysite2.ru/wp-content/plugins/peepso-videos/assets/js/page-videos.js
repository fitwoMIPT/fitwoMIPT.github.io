(function($, factory) {
	var PsPageVideos = factory($);
	var ps_page_videos = new PsPageVideos('.ps-js-videos');
})(jQuery, function($) {
	function PsPageVideos() {
		PsPageVideos.super_.apply(this, arguments);
		$($.proxy(this.init_page, this));
	}

	// inherit from `PsPageAutoload`
	peepso.npm.inherits(PsPageVideos, PsPageAutoload);

	peepso.npm.objectAssign(PsPageVideos.prototype, {
		init_page: function() {
			this._search_$sortby = $('.ps-js-videos-sortby').on('change', $.proxy(this._filter, this));
			if (this._search_$sortby.length) {
				this._search_$sortby.trigger('change');
			}

			// Remove item after delete post action.
			peepso.observer.addAction(
				'peepso_delete_post',
				function(postId) {
					var $item = $('.ps-js-video').filter('[data-post-id="' + postId + '"]');
					$item.remove();
				},
				10,
				1
			);
		},

		_search_url: 'videosajax.get_user_videos',

		_search_params: {
			uid: peepsodata.currentuserid,
			user_id: peepsodata.userid,
			sort: undefined,
			limit: 3,
			page: 1
		},

		_search_render_html: function(data) {
			return data.videos || '';
		},

		_search_get_items: function() {
			return this._search_$ct.children('.ps-video-wrapper');
		},

		/**
		 * @param {object} params
		 * @returns jQuery.Deferred
		 */
		_fetch: function(params) {
			return $.Deferred(
				$.proxy(function(defer) {
					params = peepso.observer.applyFilters('peepso_list_videos', params);

					// Multiply limit value by 2 which translate to 2 rows each call.
					params = $.extend({}, params);
					if (!_.isUndefined(params.limit)) {
						params.limit *= 2;
					}

					this._fetch_xhr && this._fetch_xhr.abort();
					this._fetch_xhr = peepso.getJson(
						this._search_url,
						params,
						$.proxy(function(response) {
							if (response.success) {
								defer.resolveWith(this, [response.data]);
							} else {
								defer.rejectWith(this, [response.errors]);
							}
						}, this)
					);
				}, this)
			);
		},

		/**
		 * Filter search based on selected elements.
		 */
		_filter: function() {
			var sortby = this._search_$sortby.val();

			// abort current request
			this._fetch_xhr && this._fetch_xhr.abort();

			this._search_params.sort = sortby || undefined;
			this._search_params.page = 1;
			this._search();
		}
	});

	return PsPageVideos;
});
