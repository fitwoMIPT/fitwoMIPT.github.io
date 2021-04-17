<div class="ps-chat-window ps-chat-window-{id}" data-id="{id}">
	<div class="ps-chat-window-box">
		<div class="ps-chat-window-header">
			<div class="ps-chat-status"><div class="ps-icon-clock"></div></div>
			<div class="ps-chat-window-notif">0</div>
			<span class="ps-chat-window-caption"><?php echo __('Loading', 'msgso');?>&hellip;</span>
			<div class="ps-chat-icons">
				<div class="ps-chat-icon ps-chat-options ps-icon-cog" data-id="{id}"></div>
				<div class="ps-chat-icon ps-chat-close ps-icon-cancel" data-id="{id}"></div>
			</div>
		</div>
		<div style="position:relative">
			<div class="ps-chat-window-content">
				<div class="ps-chat-window-messages" style="position:relative"></div>
				<div class="ps-chat-window-tmpchat" style="position:relative"></div>
				<div class="ps-chat-window-typing" style="position:relative"></div>
			</div>
			<div class="ps-chat-window-muted"><?php echo __('This conversation is muted. New chat tabs will not pop up and you will not receive notifications.','msgso');?> <a href="#" class="ps-chat-mute"><?php echo __('Unmute','msgso');?></a></div>
			<div class="ps-chat-window-turned-off"><?php echo __('You turned off chat for this conversation but you can still send a message.', 'msgso'); ?></div>
			<div class="ps-chat-window-dropdown">
				<?php if (isset($read_notification) && (TRUE == $read_notification)) { ?>
				<a href="#" class="ps-chat-checkmark">
					<span><?php echo __("Don't send read receipt", 'msgso'); ?></span>
					<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none">
				</a>
				<?php } ?>
				<a href="#" class="ps-chat-disable">
					<span><?php echo __('Turn on chat', 'msgso'); ?></span>
					<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none">
				</a>
				<a href="#" class="ps-chat-mute">
					<span><?php echo __('Mute conversation', 'msgso'); ?></span>
					<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none">
				</a>
				<a href="#" class="ps-chat-fullscreen"><?php echo __('View full conversation', 'msgso'); ?></a>
				<a href="#" class="ps-chat-blockuser">
					<span><?php echo __('Block this user', 'msgso'); ?></span>
					<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none">
				</a>
			</div>
		</div>
		<div class="ps-chat-window-input"></div>
	</div>
</div>
