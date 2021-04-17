(function($, factory) {
	var PsGroupDialogInvite = factory('PsGroupDialogInvite', $);

	peepso.groups || (peepso.groups = {});
	peepso.groups.dlgInvite = function(groupId) {
		new PsGroupDialogInvite(groupId);
	};
})(jQuery, function(name, $) {
	return peepso.createClass(name, {
		__constructor: function(groupId) {
			this.$el = $(peepsogroupsdata.dialogInviteTemplate);
			this.$loading = this.$el.find('.ps-js-loading').hide();
			this.$list = this.$el.find('.ps-js-member-items').hide();

			this.itemTemplate = this.$el.find('.ps-js-member-item').html();
			this.itemTemplate = peepso.template(this.itemTemplate);

			this.$el.on('input', 'input[type=text]', $.proxy(this.onInput, this));
			this.$el.on('click', '.ps-js-invite', $.proxy(this.onInvite, this));
			this.$el.on('click', '.ps-js-cancel', $.proxy(this.onClose, this));

			this.$el.appendTo(document.body);

			this.groupId = groupId;
			this.search();
		},

		search: function(query) {
			this.$loading.show();
			this.$list.hide();
			this._search(query);
		},

		_search: _.debounce(function(query) {
			var params = {
				group_id: this.groupId,
				query: query || undefined,
				keys: 'id,avatar,fullname,profileurl,fullname_with_addons',
				page: 1
			};
			this.fetch(params)
				.done(this.render)
				.fail(this.renderError);
		}, 500),

		fetch: function(params) {
			return $.Deferred(
				$.proxy(function(defer) {
					peepso.getJson(
						'groupusersajax.search_to_invite',
						params,
						$.proxy(function(response) {
							this.$loading.hide();
							this.$list.empty().show();
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

		render: function(data) {
			var html = '',
				i;

			if (data.users && data.users.length) {
				for (var i = 0; i < data.users.length; i++) {
					html += this.itemTemplate($.extend({ group_id: this.groupId }, data.users[i]));
				}
				this.$list.append(html);
			}
		},

		renderError: function(errors) {
			if (errors && errors.length) {
				this.$list.append(errors.join('<br />'));
			}
		},

		invite: function(userId) {
			return $.Deferred(
				$.proxy(function(defer) {
					ps_group
						.inviteUser(this.groupId, userId)
						.done(
							$.proxy(function(data) {
								defer.resolveWith(this, [data]);
							}, this)
						)
						.fail(
							$.proxy(function(errors) {
								defer.rejectWith(this, [errors]);
							}, this)
						);
				}, this)
			);
		},

		close: function(reload) {
			if (reload) {
				window.location.reload();
			} else {
				this.$el.remove();
			}
		},

		onInput: function(e) {
			e.preventDefault();
			e.stopPropagation();
			this.search($.trim(e.target.value));
		},

		onInvite: function(e) {
			var $btn = $(e.currentTarget),
				$loading = $btn.find('img').show(),
				$text = $btn.find('span'),
				userId = $btn.data('id');

			e.preventDefault();
			e.stopPropagation();

			this.invite(userId)
				.always(function() {
					$loading.hide();
				})
				.done(function() {
					$text.html($text.data('invited'));
					$btn.attr('disabled', 'disabled');
				});
		},

		onClose: function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $btn = $(e.currentTarget);
			var reload = +$btn.data('reload-on-close');

			this.close(reload);
		}
	});
});
