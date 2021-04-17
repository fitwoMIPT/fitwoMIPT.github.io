<div class="ps-group__item-wrapper ps-js-group-item ps-js-group-item--{{= data.id }}">
	<div class="ps-group__item {{= data.published ? '' : 'ps-group__item--unpublished' }}">
		<div class="ps-group__header">
			<img class="ps-avatar--group" src="{{= data.avatar_url_full }}" alt="{{= data.name }} avatar">
			{{ if ( !data.published ) { }}
			<div class="ps-group__alert">
				<i class="ps-icon-warning-sign"></i>
				<span><?php echo __('Unpublished', 'groupso'); ?></span>
			</div>
			{{ } }}
		</div>
		<div class="ps-group__body">
			<h3 class="ps-group__title">
				<a href="{{= data.url }}">{{= data.nameHighlight }}</a>
			</h3>
			{{ if ( data.privacy ) { }}
			<div class="ps-group__privacy">
				<i class="{{= data.privacy.icon }}"></i>
				{{= data.privacy.name }}
			</div>
			{{ } }}
			<div class="ps-group__details">
				<span>
					<i class="ps-icon-users"></i>
					<span class="ps-js-member-count">
						{{= data.members_count }} {{= data.members_count > 1 ? '<?php echo __("members", "groupso"); ?>' : '<?php echo __("member", "groupso"); ?>' }}
						{{ if ( +data.pending_admin_members_count >= 1 ) { }}
						({{- '<?php echo __("%d pending", "groupso"); ?>'.replace( '%d', data.pending_admin_members_count ) }})
						{{ } }}
					</span>
				</span>
				<?php if (intval(PeepSo::get_option('groups_listing_show_group_owner',1))) { ?>
				<span class="ps-group__details-hide">
					<i class="ps-icon-star"></i>
					<span class="ps-js-owner" data-id="{{= data.id }}"><img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" /></span>
				</span>
				<?php } ?>
				<?php if (intval(PeepSo::get_option('groups_listing_show_group_creation_date',1))) { ?>
				<span class="ps-group__details-hide"><i class="ps-icon-clock"></i> {{= data.date_created_formatted }}</span>
				<?php } ?>
				<span class="ps-group__details-hide">
					<i class="ps-icon-tag ps-js-category-icon"></i>
					<span class="ps-js-categories" data-id="{{= data.id }}"><img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" /></span>
				</span>
			</div>
			<div class="ps-group__description">
				<i class="ps-icon-vcard"></i> {{= data.description }}
			</div>
			<a href="#" class="ps-link--more ps-js-more">
				<i class="ps-icon-info-circled"></i>
				<span><?php echo __('More', 'groupso'); ?></span>
			</a>
			<div class="ps-group__actions ps-js-member-actions">{{= data.member_actions }}</div>
		</div>
	</div>
</div>
