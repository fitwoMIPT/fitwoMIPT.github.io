(function($, peepso, factory) {
	// psfriends is still used in peepsomessages.js
	psfriends = peepso.friends = factory($, peepso);
})(jQuery || $, peepso, function($, peepso) {
	function PsFriends() {}

	var psfriends = new PsFriends();

	// Sets up event callbacks
	PsFriends.prototype.init = function() {
		$(document).on('peepsofriends_request', function(e, user_id) {
			var req = { user_id: user_id };
			peepso.postJson('friendsajax.get_request_options', req, function(json) {
				var $elems = $('.friend-request-option[data-user-id="' + user_id + '"]');
				var $parent_ul = $elems.first().parent();
				$elems.remove();
				$parent_ul.prepend($(json.data.options).filter('.friend-request-option'));
				// update buttons
				$('.ps-members-item-buttons[data-user-id="' + user_id + '"]').each(function() {
					$(this).replaceWith(json.data.buttons);
				});
			});
		});
	};

	/**
	 * Set follower status via ajax.
	 * @param  {int} user_id The user ID to be followed
	 * @param {int} follow 0/1 flag to be set
	 * @param  {object} elem Clicked button.
	 */
	PsFriends.prototype.set_follow_status = function(user_id, follow, elem) {
		if (this.sending_request) {
			return;
		}

		if (elem) {
			elem = jQuery(elem);
			elem.find('img').css('display', 'inline');
		}

		var req = { uid: peepsodata.currentuserid, user_id: user_id, follow: follow };

		this.sending_request = true;
		peepso.postJson(
			'friendsajax.set_follow_status',
			req,
			$.proxy(function(json) {
				this.sending_request = false;
				$('.ps-js-focus--' + user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				if (elem && json.data && json.data.buttons_notif) {
					elem.closest('.ps-popover-actions').html(json.data.buttons_notif);
				}
				$(document).trigger('peepsofriends_request', [user_id]);
			}, this)
		);
	};

	/**
	 * Send a friend request via ajax.
	 * @param  {int} user_id The user ID to sent the request to.
	 * @param  {object} elem Clicked button.
	 */
	PsFriends.prototype.send_request = function(user_id, elem) {
		if (this.sending_request) {
			return;
		}

		if (elem) {
			elem = jQuery(elem);
			elem.find('img').css('display', 'inline');
		}

		var req = { uid: peepsodata.currentuserid, user_id: user_id };

		this.sending_request = true;
		peepso.postJson(
			'friendsajax.send_request',
			req,
			$.proxy(function(json) {
				this.sending_request = false;
				$('.ps-js-focus--' + user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				if (elem && json.data && json.data.buttons_notif) {
					elem.closest('.ps-popover-actions').html(json.data.buttons_notif);
				}
				$(document).trigger('peepsofriends_request', [user_id]);
			}, this)
		);
	};

	/**
	 * Cancel an existing friend request.
	 * @param  {int} request_id The friend request ID
	 * @param  {string} action  ignore|deny|ignore_block
	 * @param  {object} elem Clicked button.
	 */
	PsFriends.prototype.cancel_request = function(request_id, action, elem) {
		if (this.canceling_request) {
			return;
		}

		if (elem) {
			elem = jQuery(elem);
			elem.find('img').css('display', 'inline');
		}

		var req = {
			uid: peepsodata.currentuserid,
			user_id: peepsodata.userid,
			request_id: request_id,
			action: action
		};

		this.canceling_request = true;
		peepso.postJson(
			'friendsajax.cancel_request',
			req,
			$.proxy(function(json) {
				this.canceling_request = false;
				$('.ps-js-focus--' + req.user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				if (elem && json.data && json.data.buttons_notif) {
					elem.closest('.ps-popover-actions').html(json.data.buttons_notif);
				}
				peepso.observer.applyFilters('friendsrequests_cancel_request', request_id, json, req);
				$(document).trigger('peepsofriends_request', [json.data.user_id]);
			}, this)
		);
	};

	/**
	 * Accept a friend request.
	 * @param  {int} request_id The friend request ID.
	 * @param  {object} elem Clicked button.
	 */
	PsFriends.prototype.accept_request = function(request_id, elem) {
		if (this.accepting_request) {
			return;
		}

		if (elem) {
			elem = jQuery(elem);
			elem.find('img').css('display', 'inline');
		}

		var req = { uid: peepsodata.currentuserid, request_id: request_id };

		this.accepting_request = true;
		peepso.postJson(
			'friendsajax.accept_request',
			req,
			$.proxy(function(json) {
				this.accepting_request = false;
				$('.ps-js-focus--' + json.data.user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				peepso.observer.applyFilters('friendsrequests_accept_request', request_id, json, req);
				$(document).trigger('peepsofriends_request', [json.data.user_id]);
			}, this)
		);
	};

	/**
	 * Remove a friend.
	 * @param  {int} user_id The friend's user ID.
	 * @param  {object} elem Clicked button.
	 */
	PsFriends.prototype.remove_friend = function(user_id) {
		if (this.removing_request) {
			return;
		}

		if (this.remove_elem) {
			this.remove_elem = jQuery(this.remove_elem);
			this.remove_elem.find('img').css('display', 'inline');
		}

		var req = { uid: peepsodata.currentuserid, user_id: user_id };

		this.removing_request = true;
		peepso.postJson(
			'friendsajax.remove_friend',
			req,
			$.proxy(function(json) {
				this.removing_request = false;
				$('.ps-js-focus--' + user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				if (this.remove_elem && json.data && json.data.buttons_notif) {
					this.remove_elem.closest('.ps-popover-actions').html(json.data.buttons_notif);
				}
				peepso.observer.applyFilters('friendsrequests_remove_friend', user_id, json, req);
				$(document).trigger('peepsofriends_request', [user_id]);

				pswindow.hide();
				setTimeout(function() {
					psmessage.show(json.data.header, json.data.message, psmessage.fade_time);
				}, Math.min(1000, psmessage.fade_time));
			}, this)
		);
	};

	PsFriends.prototype.remove_friend_confirmation = function(user_id, elem) {
		if (this.removing_request) {
			return;
		}

		this.remove_elem = elem;
		var title = peepsofriendsdata.removefriend_popup_title;
		var content = peepsofriendsdata.removefriend_popup_content;
		var actions = [
			'<button type="button" class="ps-btn ps-btn-small ps-button-cancel" onclick="return pswindow.do_no_confirm();">',
			peepsofriendsdata.removefriend_popup_cancel,
			'</button>',
			'<button type="button" class="ps-btn ps-btn-small ps-button-action" onclick="return peepso.friends.remove_friend(' +
				user_id +
				');">',
			peepsofriendsdata.removefriend_popup_save,
			'</button>'
		].join(' ');

		var popup = pswindow.show(title, content).set_actions(actions);
	};

	/**
	 * Called when accepting a friend request via the dropdown notifications.
	 * @param  {object} btn A DOM element that was clicked.
	 * @param  {int} request_id The request ID.
	 */
	PsFriends.prototype.accept_notification_request = function(btn, request_id) {
		var $root = $('.ps-js-friends-notification'),
			$counter = $root.find('.ps-js-counter'),
			count = +$counter
				.eq(0)
				.html()
				.trim();

		// Manually update counter value.
		$counter.html(count - 1).css('display', count - 1 > 0 ? '' : 'none');

		peepso.postJson(
			'friendsajax.accept_request',
			{
				uid: peepsodata.currentuserid,
				request_id: request_id
			},
			function(json) {
				if (!json.success) {
					count = +$counter
						.eq(0)
						.html()
						.trim();
					$counter.html(count + 1).css('display', '');
					return;
				}

				if (btn && json.data && json.data.buttons_notif) {
					$(btn)
						.closest('.ps-popover-actions')
						.html(json.data.buttons_notif);
				}

				$('.ps-js-focus--' + json.data.user_id)
					.find('.ps-focus-actions, .ps-focus-actions-mobile')
					.html(json.data.actions);
				peepso.observer.applyFilters('friendsrequests_accept_request', request_id, json, req);
			}
		);
	};

	/**
	 * Called when ignoring a friend request via the dropdown notifications.
	 * @param  {object} btn A DOM element that was clicked.
	 * @param  {int} request_id The request ID.
	 */
	PsFriends.prototype.ignore_notification_request = function(btn, request_id) {
		var $root = $('.ps-js-friends-notification'),
			$counter = $root.find('.ps-js-counter'),
			count = +$counter
				.eq(0)
				.html()
				.trim();

		// Manually update counter value.
		$counter.html(count - 1).css('display', count - 1 > 0 ? '' : 'none');

		peepso.postJson(
			'friendsajax.cancel_request',
			{
				uid: peepsodata.currentuserid,
				request_id: request_id,
				action: 'ignore'
			},
			function(json) {
				if (!json.success) {
					count = +$counter
						.eq(0)
						.html()
						.trim();
					$counter.html(count + 1).css('display', '');
					return;
				}

				$(btn)
					.closest('.ps-notification__wrapper')
					.fadeOut();
				peepso.observer.applyFilters('friendsrequests_cancel_request', request_id, json, req);
			}
		);
	};

	/**
	 * Prevents submission of the search for if the field is empty
	 *
	 * @param form The form field.
	 */
	PsFriends.prototype.submit_search = function(form) {
		var search = $('input[name="query"]', form);
		if ('' === $.trim(search.val())) return false;

		search.val(encodeURIComponent(search.val()));
		return true;
	};

	/**
	 * TODO: docblock
	 */
	PsFriends.prototype.show_mutual_friends = function(from_id, to_id) {
		var req = {
			from_id: from_id,
			to_id: to_id
		};

		// cancel ajax
		this.show_mutual_friends_ajax &&
			this.show_mutual_friends_ajax.ret &&
			this.show_mutual_friends_ajax.ret.abort();
		this.show_mutual_friends_ajax = false;

		var getMutual = $.proxy(
			_.debounce(function(callback) {
				if (!this.show_mutual_friends_ajax) {
					req.page = (req.page || 0) + 1;
					this.show_mutual_friends_ajax = peepso.postJson(
						'friendsajax.get_mutual_friends',
						req,
						$.proxy(function(response) {
							this.show_mutual_friends_ajax = false;
							callback(response);
						}, this)
					);
				}
			}, 500),
			this
		);

		getMutual(function(response) {
			var data, title, content, popup;
			if (response.success) {
				data = response.data || {};
				title = data.title;
				content = data.template.replace('##friends##', data.friends);
				pswindow.hide();
				popup = pswindow.show(title, content);

				// prevent parent scrolling
				$('html').css('overflow', 'hidden');
				peepso.observer.addFilter('pswindow_close', function() {
					$('html').css('overflow', '');
				});

				popup.$container.find('.ps-members-item-popup').on('scroll', function() {
					var $el = $(this),
						$ct,
						$loading;

					if ($el.scrollTop() + $el.innerHeight() >= this.scrollHeight - 5) {
						$ct = popup.$container.find('.ps-members-item-popup');
						$loading = $ct.next('img').show();
						getMutual(function(response) {
							$loading.hide();
							if (response.success) {
								$ct.append(response.data.friends);
								if (!+response.data.found_friends) {
									$ct.off('scroll');
								}
							}
						});
					}
				});
			}
		});
	};

	/**
	 * Menu selector
	 */
	PsFriends.prototype.select_menu = function(select) {
		var $option = $(select.options[select.selectedIndex]),
			value = $option.val(),
			url = $option.data('url'),
			loc = window.location + '',
			samePage = loc.match(/\/requests/) && url.match(/\/requests/);

		if (samePage) {
			$('.ps-js-friends-submenu')
				.siblings('.tab-content')
				.find(value === 'sent-request' ? '#sent' : '#received')
				.addClass('active')
				.siblings()
				.removeClass('active');
		} else {
			window.location = $option.data('url');
		}
	};

	$(function() {
		// initialize the friend notification popover
		$('.ps-js-friends-notification').psnotification({
			view_all_link: peepsofriendsdata.friend_requests_page,
			source: 'friendsajax.get_requests'
		});

		psfriends.init();
	});

	return psfriends;
});
