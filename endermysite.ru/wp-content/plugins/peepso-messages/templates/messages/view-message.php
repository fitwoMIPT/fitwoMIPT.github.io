<?php
$PeepSoPostbox = PeepSoPostbox::get_instance();
$PeepSoMessages= PeepSoMessages::get_instance();
$PeepSoGeneral = PeepSoGeneral::get_instance();

// Conversation flags.
$muted = isset($muted) && $muted;
$read_notification = isset($read_notification) && $read_notification;
#$notif = isset($notif) && $notif;

?>
<div class="peepso">
	<?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

	<div class="ps-page ps-page--conversation">
		<div class="ps-conversation">
			<div class="ps-conversation__header">
				<div class="ps-conversation__header-inner">
					<div class="ps-conversation__back">
						<a href="<?php echo PeepSo::get_page('messages') ?>">
							<i class="ps-icon-caret-left"></i> <span><?php echo __('Back to Messages', 'msgso'); ?></span>
						</a>
					</div>

					<div class="ps-conversation__actions">
						<?php if ($show_blockuser) { ?>
						<a href="javascript:" class="ps-js-btn-blockuser" data-tooltip="<?php echo __('Block this user', 'msgso');?>" data-user-id="<?php echo $show_blockuser_id; ?>">
							<i class="ps-icon-remove"></i>
						</a>
						<?php } ?>
						<a href="javascript:" id="add-recipients-toggle" data-tooltip="<?php echo __('Add People to the conversation', 'msgso');?>"><i class="ps-icon-user-add"></i></a>
						<?php if ($read_notification) { ?>
						<a href="javascript:" class="ps-js-btn-toggle-checkmark <?php echo $notif ? '' : ' disabled' ?>" data-tooltip="<?php echo $notif ? __("Don't send read receipt", 'msgso') : __('Send read receipt', 'msgso'); ?>"
							onclick="return ps_messages.toggle_checkmark(<?php echo $parent->ID;?>, <?php echo $notif ? 0 : 1 ?>);"
						>
							<i class="ps-icon-ok"></i>
						</a>
						<?php } ?>
						<a href="javascript:" class="ps-js-btn-mute-conversation" data-tooltip="<?php echo $muted ? __('Unmute conversation', 'msgso') : __('Mute conversation', 'msgso'); ?>"
							onclick="return ps_messages.<?php echo $muted ? 'unmute' : 'mute'; ?>_conversation(<?php echo $parent->ID;?>, <?php echo $muted ? 0 : 1; ?>);"
						>
							<i class="<?php echo $muted ? 'ps-icon-bell-off' : 'ps-icon-bell-alt'; ?>"></i>
						</a>
						<a data-tooltip="<?php echo __('Leave this conversation', 'msgso');?>"
							href="<?php echo $PeepSoMessages->get_leave_conversation_url();?>"
							onclick="return ps_messages.leave_conversation('<?php echo __('Are you sure you want to leave this conversation?', 'msgso'); ?>', this)"
						>
							<i class="ps-icon-minus-sign"></i>
						</a>
					</div>
				</div>
				<div class="ps-conversation__add ps-js-recipients" style="display: none">
					<select name="recipients" id="recipients-search"
						data-placeholder="<?php echo __('Add People to the conversation', 'msgso');?>"
						data-loading="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>"
						multiple></select>
					<?php wp_nonce_field('add-participant', 'add-participant-nonce'); ?>
					<button class="ps-btn ps-btn-action ps-btn-small" onclick="ps_messages.add_recipients(<?php echo $parent->ID;?>);">
						<?php echo __('Done', 'msgso'); ?>
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none;margin-left:5px">
					</button>
				</div>
				<div class="ps-conversation__participants ps-js-participant-summary">
					<span><?php echo __('Conversation with', 'msgso'); ?>:</span> <span class="ps-conversation__status"><span class="ps-icon-clock"></span></span><?php $PeepSoMessages->display_participant_summary();?>
				</div>
			</div>

			<div class="ps-conversation__chat ps-chat">
				<div class="ps-chat__messages">
					<div class="ps-chat__loading ps-js-loading" style="display:block">
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
					</div>
					<div class="ps-chat__typing ps-js-currently-typing"></div>
				</div>
			</div>

			<div class="ps-conversation__postbox">
				<div id="postbox-message" class="ps-postbox ps-postbox--conversation ps-clearfix" style="">
					<?php $PeepSoPostbox->before_postbox(); ?>
					<div id="ps-postbox-status" class="ps-postbox-content">
						<div class="ps-postbox-tabs">
							<?php $PeepSoPostbox->postbox_tabs('messages'); ?>
						</div>
						<?php PeepSoTemplate::exec_template('general', 'postbox-status'); ?>
					</div>
					<nav class="ps-postbox-tab ps-postbox-tab-root ps-clearfix" style="display:none">
						<div class="ps-postbox__menu">
							<?php $PeepSoGeneral->post_types(array('postbox_message' => TRUE)); ?>
						</div>
					</nav>
					<nav class="ps-postbox-tab selected interactions">
						<div class="ps-postbox__menu">
							<?php $PeepSoPostbox->post_interactions(array('postbox_message' => TRUE)); ?>
						</div>
						<div class="ps-postbox__action ps-postbox-action">
							<span class="ps-checkbox ps-checkbox--enter">
								<input type="checkbox" id="enter-to-send" class="ps-js-checkbox-entertosend">
								<label for="enter-to-send"><?php echo __('Press "Enter" to send', 'msgso'); ?></label>
							</span>
							<span>
								<button type="button" class="ps-btn ps-btn--postbox ps-button-cancel" style="display:none"><?php echo __('Cancel', 'msgso'); ?></button>
								<button type="button" class="ps-btn ps-btn--postbox ps-button-action postbox-submit" style="display:none"><?php echo __('Send', 'msgso'); ?></button>
							</span>
						</div>
						<div class="ps-edit-loading" style="display: none;">
							<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
							<div> </div>
						</div>
					</nav>
					<?php $PeepSoPostbox->after_postbox(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function() {
		ps_messages.init_conversation_view(<?php echo $parent->ID; ?>);
	});
</script>
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
