<?php
$force_add = FALSE;
if(PeepSo::is_admin() && 1 == PeepSo::get_option('groups_add_by_admin_directly', 0)) {
    $force_add = TRUE;
}
?>
<div class="ps-dialog-wrapper">
	<div class="ps-dialog-container">
		<div class="ps-dialog ps-dialog-wide">
			<div class="ps-dialog-header">
				<span><?php echo $force_add ? __('Add users to group', 'groupso') : __('Invite users to group', 'groupso'); ?></span>
				<a class="ps-dialog-close ps-js-cancel" href="#" data-reload-on-close="<?php echo isset($reload_on_close) && $reload_on_close ? 1 : 0; ?>">
					<span class="ps-icon-remove"></span>
				</a>
			</div>
			<div class="ps-dialog-body">
				<div class="ps-input__wrapper"><input type="text" class="ps-input ps-full" value="" placeholder="<?php echo __('Start typing to search...', 'groupso'); ?>" /></div>
				<div class="ps-loading ps-js-loading"><img class="ps-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" /></div>
				<div class="ps-js-member-items ps-clearfix"></div>
				<div class="ps-notice ps-notice--bottom">
					<p>
						<?php echo __('Please note: Users who are either banned, already invited, members or blocked receiving invitations to this group will not show in this listing.', 'groupso'); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<script type="text/template" class="ps-js-member-item">
		<div class="ps-members-item-wrapper">
			<div class="ps-members-item">
				<div class="ps-members-item-avatar">
					<span class="ps-avatar">
						<img src="{{= data.avatar }}" title="{{= data.fullname }}" alt="{{= data.fullname }} avatar">
					</span>
				</div>
				<div class="ps-members-item-body">
					<a href="{{= data.profileurl }}" class="ps-members-item-title" title="{{= data.fullname }}" alt="{{= data.fullname }}">
						{{= data.fullname_with_addons }}
					</a>
				</div>
				<div class="ps-members-item-buttons">
					<button class="ps-btn ps-btn-small ps-js-invite" data-id="{{= data.id }}">
						<span data-invited="<?php echo $force_add ? __('Added', 'groupso') : __('Invited', 'groupso'); ?>"><?php echo $force_add ? __('Add to group', 'groupso') : __('Invite to Group', 'groupso'); ?></span>
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="display:none" />
					</button>
				</div>
			</div>
		</div>
	</script>
</div>
