import './chat-input';

(function($, _, peepso, factory) {
	window.PsChatWindow = factory($, _, peepso);
})(jQuery, _, peepso, function($, _, peepso) {
	var POLLING_INTERVAL = 2000;
	var POLLING_INTERVAL_INACTIVE = 3000;

	/**
	 * Create new conversation window.
	 * @class PsChatWindow
	 * @param {number} id Conversation ID.
	 */
	function PsChatWindow(id, state) {
		state || (state = {});
		this.id = id;
		this.oldestMessageId = false;
		this.newestMessageId = false;
		this.deletedMessageIds = [];
		this.caption = null;
		this.input = new PsChatInput();
		this.disabled = state.disabled || false;
		this.muted = state.muted || false;
		this.send_receipt = state.send_receipt || false;
		this.receipt = state.receipt || false;
		this.receipt_unread = state.receipt_unread || false;
		this.create();
		this.checkStatus();
	}

	peepso.npm.objectAssign(
		PsChatWindow.prototype,
		peepso.npm.EventEmitter.prototype,
		/** @lends PsChatWindow.prototype */ {
			/**
			 * Conversation window template.
			 * @type {string}
			 */
			template: peepsochatdata.windowTemplate,

			/**
			 * Send message template.
			 * @type {string}
			 */
			messageTemplate: peepsochatdata.sendMessageTemplate,

			/**
			 * Send message template.
			 * @type {string}
			 */
			photosTemplate: peepsochatdata.sendPhotosTemplate,

			/**
			 * Languge translations.
			 * @type {Object.<string, string>}
			 */
			translations: peepsochatdata.translations,

			/**
			 * Message url pattern.
			 * @type {string}
			 */
			messageUrl: peepsochatdata.messageUrl,

			/**
			 * Initialize conversation window.
			 */
			create: function() {
				this.$el = $(this.template.replace(/\{id\}/g, this.id));
				this.$header = this.$el.find('.ps-chat-window-header');
				this.$status = this.$el.find('.ps-chat-status');
				this.$notif = this.$header.find('.ps-chat-window-notif');
				this.$caption = this.$header.find('span.ps-chat-window-caption');
				this.$dropdown = this.$el.find('.ps-chat-window-dropdown');
				this.$turnoff = this.$el.find('.ps-chat-window-turned-off');
				this.$muted = this.$el.find('.ps-chat-window-muted');
				this.$content = this.$el.find('.ps-chat-window-content');
				this.$messages = this.$el.find('.ps-chat-window-messages');
				this.$tmpchat = this.$el.find('.ps-chat-window-tmpchat');
				this.$typing = this.$el.find('.ps-chat-window-typing');
				this.$btnOption = this.$el.find('.ps-chat-options').hide();
				this.$el.find('.ps-chat-window-input').append(this.input.$el);
				this.$el.on(
					'click',
					'.ps-chat-window-header',
					$.proxy(function(e) {
						e.preventDefault();
						e.stopPropagation();
						this.toggle(true);
					}, this)
				);
				this.$content.on('click', '.ps-conversation-content', function(e) {
					e.stopPropagation();
				});
				this.$content.on(
					'click',
					$.proxy(function(e) {
						this.focus();
					}, this)
				);
				this.$el.on('click', '.ps-chat-options', $.proxy(this.onOptions, this));
				this.$el.on('click', '.ps-chat-disable', $.proxy(this.onDisable, this));
				this.$el.on('click', '.ps-chat-mute', $.proxy(this.onMute, this));
				this.$el.on('click', '.ps-chat-fullscreen', $.proxy(this.onFullScreen, this));
				this.$el.on('click', '.ps-chat-blockuser', $.proxy(this.onBlockUser, this));
				this.$el.on('click', '.ps-chat-close', $.proxy(this.onClose, this));
				this.$el.on(
					'click',
					'.ps-chat-checkmark',
					$.proxy(this.onToggleNotification, this)
				);
				this.expanded = false;
				this.loadInitialMessages();

				this.$caption.on(
					'click',
					'a',
					$.proxy(function(e) {
						this.expanded ? e.stopPropagation() : e.preventDefault();
					}, this)
				);

				this.updateDisabled();
				this.updateMuted();

				peepso.observer.addAction('msgso_send_message', $.proxy(this.send, this), 10, 3);
			},

			/**
			 * Load initial messages in current conversation.
			 */
			loadInitialMessages: function() {
				this.loadNewerLock = true;
				this.loadNewerCount || (this.loadNewerCount = 0);
				this.loadMessages(
					{
						msg_id: this.id,
						chat: 1,
						get_messages: 1,
						get_participants: 1
					},
					function(response) {
						if (response.success) {
							this.renderConversation(response.data);
							this.updateCheckmark();
							this.scrollTo('bottom');
							if (!this.expanded) {
								this.scrollOnExpand = true;
							}
							// listed for input submit after initial messages are loaded
							this.input.on('submit', $.proxy(this.onInputSubmit, this));
							this.input.on('click', $.proxy(this.onInputFocus, this));
							this.input.on('focus', $.proxy(this.onInputFocus, this));
							this.input.on('blur', $.proxy(this.onInputBlur, this));
							this.input.on('change', $.proxy(this.onInputChange, this));
							this.input.on('photos_added', $.proxy(this.onInputAddPhotos, this));
							this.input.on(
								'photos_cancel',
								$.proxy(this.onInputCancelAddPhotos, this)
							);
							// check for newer message queue
							this.loadNewerLock = false;
							if (this.loadNewerQueue && this.expanded) {
								this.loadNewerQueue = false;
								this.loadNewerMessages();
							}
							// http://stackoverflow.com/questions/5802467/prevent-scrolling-of-parent-element
							this.$content.bind(
								'mousewheel',
								$.proxy(function(e, d) {
									var t = $(e.currentTarget);
									if (d > 0 && t.scrollTop() === 0) {
										e.preventDefault();
										this.loadOlderMessages();
									} else if (
										d < 0 &&
										t.scrollTop() == t.get(0).scrollHeight - t.innerHeight()
									) {
										e.preventDefault();
									}
								}, this)
							);
						}
					}
				);
			},

			/**
			 * Handle send message.
			 * @param {number} id Conversation ID.
			 * @param {string} content Message string to be sent.
			 * @param {object} params Additional parameters.
			 * @return {jQuery.Deferred}
			 */
			send: function(id, content, params) {
				return $.Deferred(
					$.proxy(function(defer) {
						id = +id;
						if (id !== +this.id) {
							defer.reject();
							return;
						}

						content = $.trim(content);
						params = params || {};
						if (!content && !params.type) {
							defer.reject();
							return;
						}

						var req = $.extend(
							{},
							{
								content: content,
								id: peepsodata.currentuserid,
								uid: peepsodata.userid,
								type: 'activity',
								parent_id: id
							},
							params
						);

						peepso
							.disableAuth()
							.disableError()
							.postJson(
								'messagesajax.add_message',
								req,
								$.proxy(function(response) {
									if (response.success) {
										this.loadNewerMessages();
										defer.resolve();
									}
								}, this)
							);
					}, this)
				);
			},

			/**
			 * Load newer messages in current conversation.
			 */
			loadNewerMessages: function() {
				if (this.loadNewerLock) {
					this.loadNewerQueue = true;
					return;
				}
				this.loadNewerLock = true;
				this.loadNewerCount || (this.loadNewerCount = 0);
				this.loadMessages(
					{
						msg_id: this.id,
						chat: 1,
						get_messages: 1,
						get_participants: ++this.loadNewerCount % 10 === 0 ? 1 : 0,
						get_recently_deleted: 1,
						direction: 'new',
						from_id: this.newestMessageId
					},
					function(response) {
						this.renderConversation(response.data, 'append');
						this.scrollTo('bottom');
						this.loadNewerLock = false;
						if (this.tmpNotEmpty) {
							this.$tmpchat.empty();
							this.tmpNotEmpty = false;
						}
						if (this.loadNewerQueue) {
							this.loadNewerQueue = false;
							this.loadNewerMessages();
						}
					}
				);
			},

			/**
			 * Load older messages in current conversation.
			 */
			loadOlderMessages: function() {
				if (this.loadOlderLock) {
					return;
				}
				this.loadOlderLock = true;
				this.loadMessages(
					{
						msg_id: this.id,
						chat: 1,
						get_messages: 1,
						get_participants: 0,
						get_recently_deleted: 0,
						direction: 'old',
						from_id: this.oldestMessageId
					},
					function(response) {
						this.renderConversation(response.data, 'prepend');
						this.updateCheckmark();
						this.scrollTo('top');
						this.loadOlderLock = false;
					}
				);
			},

			/**
			 * Load messages in current conversation.
			 * @param {object} req Request parameter.
			 * @param {function} callback Request callback.
			 */
			loadMessages: function(req, callback) {
				this.loadMessagesXHR && this.loadMessagesXHR.ret.abort();
				this.loadMessagesXHR = peepso
					.disableAuth()
					.disableError()
					.postJson(
						'messagesajax.get_messages_in_conversation',
						req,
						$.proxy(function(response) {
							$.proxy(callback, this)(response);
							this.loadMessagesXHR = false;
						}, this)
					);
			},

			/**
			 * Check participants online status.
			 */
			checkStatus: function() {
				var req = {
					msg_id: this.id,
					chat: 1,
					get_messages: 0,
					get_participants: 1,
					get_recently_deleted: 0
				};

				clearInterval(this.checkStatusTimer);
				this.checkStatusTimer = setInterval(
					$.proxy(function() {
						this.checkStatusXHR && this.checkStatusXHR.ret.abort();
						this.checkStatusXHR = peepso
							.disableAuth()
							.disableError()
							.postJson(
								'messagesajax.get_messages_in_conversation',
								req,
								$.proxy(function(response) {
									this.renderConversation(response.data);
									this.checkStatusXHR = false;
								}, this)
							);
					}, this),
					60000
				);
			},

			/**
			 * Render data into current conversation.
			 * @param {object} data
			 * @param {boolean} method
			 */
			renderConversation: function(data, method) {
				var deletedId;

				// render participants
				if (data.users && data.users.length) {
					this.caption = this.formatParticipants(data.users);
					this.$caption.html(this.caption || '&nbsp;');
					// check online status
					this.$status.children().get(0).className = this.isOnline(data.users)
						? 'ps-icon-circle'
						: 'ps-icon-clock';
					// toggle block user menu
					var $blockUser = this.$dropdown.find('.ps-chat-blockuser');
					if (data.users.length > 1) {
						$blockUser.hide();
					} else {
						$blockUser.show();
						$blockUser.data('user-id', data.users[0].id);
					}
				}
				// render chat messages
				if (data.ids && data.ids.length && data.html) {
					var $elem = peepso.observer.applyFilters('messages_render', $(data.html));

					peepso.observer.doAction('peepso_external_link', $elem);
					if (method === 'prepend') {
						this.oldestMessageId = +data.ids[0];
						this.$messages.prepend($elem);
					} else if (method === 'append') {
						this.newestMessageId = +data.ids[data.ids.length - 1];
						this.$messages.append($elem);
					} else {
						this.oldestMessageId = +data.ids[0];
						this.newestMessageId = +data.ids[data.ids.length - 1];
						this.$messages.html($elem);
					}
				}
				// remove message listed on recently_deleted field
				if (data.recently_deleted && data.recently_deleted.length) {
					while (data.recently_deleted.length) {
						deletedId = +data.recently_deleted.shift();
						if (this.deletedMessageIds.indexOf(deletedId) === -1) {
							this.deletedMessageIds.push(deletedId);
							this.$messages.find('.ps-js-message-' + deletedId).remove();
							if (deletedId === this.newestMessageId) {
								this.newestMessageId = +this.$messages
									.find('.ps-js-message')
									.last()
									.data('id');
							} else if (deletedId === this.oldestMessageId) {
								this.oldestMessageId = +this.$messages
									.find('.ps-js-message')
									.first()
									.data('id');
							}
						}
					}
				}
				// render somebody-is-typing
				this.renderTyping(data.currently_typing);
			},

			/**
			 * Render someone else is currently typing information.
			 * @param {string=} html Html string to be printed.
			 */
			renderTyping: function(html) {
				this.$typing.html(html || '');
				clearTimeout(this.renderTypingTimer);
				this.renderTypingTimer = setTimeout(
					$.proxy(function() {
						this.$typing.html('');
					}, this),
					5000
				);
			},

			/**
			 * Sends mark-as-read request for current conversation.
			 * @function
			 */
			markAsRead: _.throttle(function() {
				peepso
					.disableAuth()
					.disableError()
					.postJson(
						'messagesajax.mark_read_messages_in_conversation',
						{ msg_id: this.id },
						function() {
							peepso.observer.applyFilters('pschat_mark_as_read');
						}
					);
			}, 2000),

			/**
			 * Sends i-am-typing request for current conversation.
			 * @function
			 */
			iAmTyping: _.throttle(function() {
				peepso
					.disableAuth()
					.disableError()
					.postJson('messagesajax.i_am_typing', { msg_id: this.id });
			}, +peepsodata.notification_ajax_delay_min || 5000),

			/**
			 * Focusing to window input.
			 * @function
			 */
			focus: _.debounce(function() {
				this.input.focus();
			}, 100),

			/**
			 * Update chat window based on current status.
			 */
			update: function(state) {
				state || (state = {});
				// update unread counter
				var unread = +state.unread || 0;
				this.unread || (this.unread = 0);
				if (this.unread !== unread) {
					this.unread = unread;
					if (this.expanded) {
						this.loadNewerMessages();
					} else if (!this.disabled) {
						if (this.unread) {
							this.$notif.html(this.unread).show();
						} else {
							this.$notif.hide();
						}
					}
				} else if (this.last_activity !== state.last_activity) {
					this.last_activity = state.last_activity;
					this.loadNewerMessages();
				}
				if (this.disabled !== state.disabled) {
					this.disabled = state.disabled;
					this.updateDisabled();
				}
				if (this.muted !== state.muted) {
					this.muted = state.muted;
					this.updateMuted();
				}
				if (this.send_receipt !== state.send_receipt) {
					this.send_receipt = state.send_receipt;
					this.updateNotification();
				}
				if (
					this.receipt !== state.receipt ||
					this.receipt_unread !== state.receipt_unread
				) {
					this.receipt = state.receipt;
					this.receipt_unread = state.receipt_unread;
					if (this.receipt) {
						this.updateCheckmark();
					}
				}
			},

			/**
			 * Toggle window expand/collapse.
			 * @param {boolean=} triggerEvent Trigger event's flag.
			 */
			toggle: function(triggerEvent) {
				this.expanded ? this.collapse(triggerEvent) : this.expand(triggerEvent);
			},

			/*
			 * Maximize window.
			 * @param {Boolean=} triggerEvent Trigger event's flag.
			 * @fires PsChatWindow#expand
			 */
			expand: function(triggerEvent) {
				if (!this.expanded) {
					this.$el.addClass('ps-chat-window-open');
					this.$btnOption.show();
					this.expanded = true;
					this.focus();
					// hide unread counter
					if (this.unread > 0) {
						this.loadNewerMessages();
						this.unread = 0;
						this.$notif.hide();
					}
					// scroll content
					if (this.scrollOnExpand) {
						this.scrollTo('bottom');
						this.scrollOnExpand = false;
					}
					/**
					 * Event fired when chat window is minimized.
					 * @event PsChatWindow#collapse
					 */
					if (triggerEvent) {
						this.emit('expand', this.id);
					}
				}
			},

			/*
			 * Minimize window.
			 * @param {Boolean=} triggerEvent Trigger event's flag.
			 * @fires PsChatWindow#collapse
			 */
			collapse: function(triggerEvent) {
				if (this.expanded) {
					this.$el.removeClass('ps-chat-window-open');
					this.$btnOption.hide();
					this.expanded = false;
					if (triggerEvent) {
						/**
						 * Event fired when chat window is minimized.
						 * @event PsChatWindow#collapse
						 */
						this.emit('collapse', this.id);
					}
				}
			},

			/*
			 * Destroy window.
			 * @param {Boolean=} triggerEvent Trigger event's flag.
			 * @fires PsChatWindow#destroy
			 */
			destroy: function(triggerEvent) {
				this.$el.remove();
				if (triggerEvent) {
					/**
					 * Event fired when chat window is destroyed.
					 * @event PsChatWindow#destroy
					 */
					this.emit('destroy', this.id);
				}
				this.removeAllListeners();
			},

			/**
			 * Participant names formatter.
			 * @param {Object[]} users
			 * @return {String} Formatted participant names.
			 */
			formatParticipants: function(users) {
				var str;
				if (users.length === 1) {
					str = '<a href="' + users[0].url + '">' + users[0].name_full + '</a>';
				} else if (users.length > 1) {
					str = [];
					for (var i = 0, len = Math.min(2, users.length - 1); i < len; i++) {
						str.push(users[i].name_first);
					}
					str = str.join(', ');
					if (users.length === 2) {
						str = this.translations.and.replace(
							/%s(.+)%s/,
							str + '$1' + users[users.length - 1].name_first
						);
					} else if (users.length === 3) {
						str = this.translations.and_x_other.replace('%s', str).replace('%d', 1);
					} else {
						str = this.translations.and_x_others
							.replace('%s', str)
							.replace('%d', users.length - 2);
					}
					str =
						'<a href="' +
						this.messageUrl.replace('{id}', this.id) +
						'">' +
						str +
						'</a>';
				}
				return str;
			},

			/**
			 * Check whether chat participants are online.
			 * @param {Object[]} users
			 */
			isOnline: function(users) {
				var online = false;
				if (users && users.length) {
					for (var i = 0; i < users.length; i++) {
						if (users[i].online) {
							online = true;
							break;
						}
					}
				}
				return online;
			},

			/**
			 * Scrolls chat window to top or bottom depending on provided parameter.
			 * @param {string=} to Scroll direction.
			 */
			scrollTo: function(to) {
				this.$content[0].scrollTop = to === 'top' ? 0 : this.$content[0].scrollHeight;
			},

			/**
			 * Toggle disable/enable chat.
			 * @function
			 */
			toggleDisable: _.debounce(function() {
				var req = { msg_id: this.id, disabled: this.disabled ? 0 : 1 };
				this.$el.find('.ps-chat-disable img').show();
				peepso
					.disableAuth()
					.disableError()
					.postJson(
						'chatajax.set_chat_disabled',
						req,
						$.proxy(function(response) {
							this.$el.find('.ps-chat-disable img').hide();
							if (response.success) {
								this.disabled = req.disabled;
								this.updateDisabled();
								this.updateMuted();
								this.toggleDropdown('hide');
							}
						}, this)
					);
			}, 400),

			/**
			 * Update chat status based on disabled/enabled flag.
			 */
			updateDisabled: function() {
				var text;
				if (this.disabled) {
					text = this.translations.turn_on_chat;
					this.$el.removeClass('active');
					this.$turnoff.show();
					this.$notif.hide();
				} else {
					text = this.translations.turn_off_chat;
					this.$turnoff.hide();
				}
				this.$el.find('.ps-chat-disable span').html(text);
			},

			/**
			 * Toggle mute/unmute chat.
			 * @function
			 */
			toggleMute: _.debounce(function() {
				if (!this.muted) {
					this.toggleDropdown('hide');
					ps_messages.mute_conversation(this.id);
					return;
				}

				var req = { parent_id: this.id, mute: this.muted ? 0 : 1 };
				this.$el.find('.ps-chat-mute img').show();
				peepso
					.disableAuth()
					.disableError()
					.postJson(
						'messagesajax.set_mute',
						req,
						$.proxy(function(response) {
							this.$el.find('.ps-chat-mute img').hide();
							if (response.success) {
								peepso.observer.applyFilters(
									'psmessages_conversation_' + (req.mute ? 'mute' : 'unmute'),
									req.parent_id
								);
								this.toggleDropdown('hide');
							}
						}, this)
					);
			}, 400),

			/**
			 * Update chat status based on disabled/enabled flag.
			 */
			updateMuted: function() {
				var text = this.muted ? this.translations.unmute_chat : this.translations.mute_chat;
				if (this.muted && !this.disabled) {
					this.$el.removeClass('active');
					this.$muted.show();
					this.$notif.hide();
				} else {
					this.$muted.hide();
				}
				this.$el.find('.ps-chat-mute span').html(text);
			},

			/**
			 * Toggle block user.
			 *
			 * @function
			 * @param {number} userId
			 */
			toggleBlockUser: _.debounce(function(userId) {
				if (confirm(peepsomessagesdata.blockuser_confirm_text)) {
					this.destroy(true);
					ps_member.block_user(userId);
				}
			}, 400),

			/**
			 * Toggle message-is-read notification.
			 * @function
			 */
			toggleNotification: _.debounce(function() {
				var req = { msg_id: this.id, read_notif: this.send_receipt ? 0 : 1 };
				this.$el.find('.ps-chat-checkmark img').show();
				peepso
					.disableAuth()
					.disableError()
					.postJson(
						'chatajax.set_chat_read_notification',
						req,
						$.proxy(function(response) {
							this.$el.find('.ps-chat-checkmark img').hide();
							if (response.success) {
								this.send_receipt = req.read_notif;
								this.updateNotification();
								this.toggleDropdown('hide');
							}
						}, this)
					);
			}, 400),

			/**
			 * Update message-is-read status based on notif flag.
			 */
			updateNotification: function() {
				this.$el
					.find('.ps-chat-checkmark')
					.find('span')
					.html(
						this.send_receipt
							? this.translations.hide_checkmark
							: this.translations.show_checkmark
					);
			},

			/**
			 * Update checkmark.
			 */
			updateCheckmark: _.throttle(function() {
				var $checkmarks;
				if (this.receipt) {
					$checkmarks = this.$messages.find('.ps-icon-ok').addClass('read');
					if (this.receipt_unread > 0) {
						$checkmarks.slice(0 - this.receipt_unread).removeClass('read');
					}
				}
			}, 1000),

			/**
			 * Toggle dropdown.
			 * @param {Boolean} state
			 */
			toggleDropdown: function(state) {
				if (state === 'hide') {
					this.$dropdown.hide();
					this.$btnOption.removeClass('ps-chat-icon-on');
				} else {
					this.$dropdown.show();
					this.$btnOption.addClass('ps-chat-icon-on');
				}
			},

			/**
			 * Event handler when options button is being clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onOptions: function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (this.$dropdown.is(':visible')) {
					this.toggleDropdown('hide');
				} else {
					this.toggleDropdown('show');
				}
			},

			/**
			 * Event handler when disable button is clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onDisable: function(e) {
				e.preventDefault();
				e.stopPropagation();
				this.toggleDisable();
			},

			/**
			 * Event handler when mute button is clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onMute: function(e) {
				e.preventDefault();
				e.stopPropagation();
				this.toggleMute();
			},

			/**
			 * Event handler when fullscreen button is being clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onFullScreen: function(e) {
				e.preventDefault();
				e.stopPropagation();
				window.location = this.messageUrl.replace('{id}', this.id);
			},

			/**
			 * Event handler when block user button is clicked.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onBlockUser: function(e) {
				e.preventDefault();
				e.stopPropagation();

				var data = $(e.currentTarget).data();
				if (+data.userId) {
					this.toggleBlockUser(+data.userId);
				}
			},

			/**
			 * Event handler when close button is being clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onClose: function(e) {
				e.preventDefault();
				e.stopPropagation();
				this.destroy(true);
			},

			/**
			 * Event handler when toggle message-is-read button is being clicked by user.
			 * @private
			 * @param {Event} e Browser event.
			 */
			onToggleNotification: function(e) {
				e.preventDefault();
				e.stopPropagation();
				this.toggleNotification();
			},

			/**
			 * Handle post chat.
			 * @param {string} content Message string to be sent.
			 * @param {object} params Additional parameters.
			 */
			onInputSubmit: function(content, params) {
				content = $.trim(content);
				params = params || {};
				if (!content && !params.type) {
					return;
				}

				var req = $.extend(
					{},
					{
						content: content,
						id: peepsodata.currentuserid,
						uid: peepsodata.userid,
						type: 'activity',
						parent_id: this.id
					},
					params
				);

				// insert temporary message
				if (req.type === 'activity') {
					this.tmpNotEmpty = true;
					this.$tmpchat.append(this.messageTemplate.replace('{content}', content));
					this.scrollTo('bottom');
				}

				// send message
				peepso
					.disableAsync()
					.disableAuth()
					.disableError()
					.postJson(
						'messagesajax.add_message',
						req,
						$.proxy(function(response) {
							if (response.success) {
								this.loadNewerMessages();
							}
						}, this)
					);
			},

			/**
			 * Handles input focus.
			 * @param {event} e
			 */
			onInputFocus: function(e) {
				e.stopPropagation();
				this.markAsRead();
				if (!this.disabled) {
					this.$el.addClass('active');
				}
			},

			/**
			 * Handles input blur.
			 * @param {event} e
			 */
			onInputBlur: function(e) {
				e.stopPropagation();
				this.$el.removeClass('active');
				this.toggleDropdown('hide');
			},

			/**
			 * Handle changes in input content.
			 * @param {string} content New input contents.
			 */
			onInputChange: function(content) {
				this.iAmTyping();
			},

			/**
			 * Handle add photo event on input.
			 * @param {number} count Added images.
			 * @param {number} id Upload ID.
			 */
			onInputAddPhotos: function(count, id) {
				var rItem = /\{item\}([\s\S]+)\{\/item\}/,
					itemTemplate = this.photosTemplate.match(rItem)[1],
					content = '',
					i;

				for (i = 1; i <= count; i++) {
					content += itemTemplate;
				}

				this.tmpNotEmpty = true;
				this.$tmpchat.append(
					this.photosTemplate.replace('{id}', id).replace(rItem, content)
				);
				this.scrollTo('bottom');
			},

			/**
			 * Handle add photo event on input.
			 * @param {number} count Added images.
			 * @param {number} id Upload ID.
			 */
			onInputCancelAddPhotos: function(id) {
				this.$tmpchat.find('.my-message-photos-' + id).remove();
			}
		}
	);

	return PsChatWindow;
});
