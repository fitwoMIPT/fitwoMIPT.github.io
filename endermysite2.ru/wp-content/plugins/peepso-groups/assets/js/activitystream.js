( function( root, $, factory ) {
	$( function() {
		factory( root, $ );
	} );
} )( this, jQuery, function( root, $ ) {
	var isGroupStream = !! root.peepsogroupsdata && +root.peepsogroupsdata.group_id,
		isPermalink =
			root.peepsodata && root.peepsodata.activity && +root.peepsodata.activity.is_permalink,
		stashedPosts = [];

	// Skip the process entirely on group stream or a single activity post.
	if ( isGroupStream || isPermalink ) {
		return;
	}

	// Do not show pinned group posts at the top of non-group stream.
	peepso.observer.addFilter(
		'peepso_activity',
		function( $posts ) {
			$posts = $posts.map( function() {
				var $post = $( this ),
					mappedPost = this,
					timestamp;

				// Stash pinned group post.
				if ( $post.hasClass( 'ps-js-activity-pinned' ) ) {
					if ( ! $post.data( 'pending-post' ) ) {
						// Check if it is a group post.
						if ( $post.find( '.ps-post__title .ps-icon-group' ).length ) {
							timestamp = +$post.find( '.ps-stream-time[data-timestamp]' ).data( 'timestamp' );
							if ( timestamp ) {
								mappedPost = null;
								stashedPosts.push( { post: this, timestamp: timestamp } );
								stashedPosts = _.sortBy( stashedPosts, function( stashed ) {
									return -stashed.timestamp;
								} );
							}
						}
					}
				}

				// Put stashed pinned group posts to the original location as if its not pinned.
				else if ( stashedPosts.length && $post.hasClass( 'ps-js-activity' ) ) {
					timestamp = +$post.find( '.ps-stream-time[data-timestamp]' ).data( 'timestamp' );
					if ( timestamp ) {
						stashedPosts = $.map( stashedPosts, function( stashed ) {
							if ( stashed.timestamp > timestamp ) {
								if ( ! _.isArray( mappedPost ) ) {
									mappedPost = [ mappedPost ];
								}
								mappedPost.splice( mappedPost.length - 1, 0, stashed.post );
								return null;
							}
							return stashed;
						} );
					}
				}

				return mappedPost;
			} );

			return $posts;
		},
		10,
		1
	);

	// Clear the pending post cache on every filter change.
	peepso.observer.addFilter(
		'show_more_posts',
		function( params ) {
			if ( +params.page === 1 ) {
				stashedPosts = [];
			}
			return params;
		},
		10,
		1
	);

	// Return any pending posts HTML if available.
	peepso.observer.addFilter(
		'activitystream_pending_html',
		function( html ) {
			var pendingHtml = '';
			$.each( stashedPosts, function( index, stashed ) {
				var $wrapper = $( '<div />' ).append( stashed.post );
				// Add a marker so the post will not be stashed again by "peepso_activity" filter above.
				$wrapper.find( '.ps-js-activity-pinned' ).attr( 'data-pending-post', 'group' );
				pendingHtml += $wrapper.html();
			} );
			return html + pendingHtml;
		},
		10,
		1
	);
} );
