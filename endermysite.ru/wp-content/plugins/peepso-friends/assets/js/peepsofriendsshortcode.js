peepso.observer.addFilter(
	'friendsrequests_accept_request',
	function(request_id) {
		jQuery('#freq-' + request_id).fadeOut();
	},
	10,
	1
);

peepso.observer.addFilter(
	'friendsrequests_cancel_request',
	function(request_id) {
		jQuery('#freq-' + request_id).fadeOut();
	},
	10,
	1
);

peepso.observer.addFilter(
	'friendsrequests_remove_friend',
	function(user_id) {
		jQuery('#friend-' + user_id).fadeOut();
	},
	10,
	1
);
