<?php
$PeepSoPostbox = PeepSoPostbox::get_instance();
$PeepSoGeneral = PeepSoGeneral::get_instance();
?>
<div id="new-message-dialog">
	<div class="dialog-title"><?php echo __('Write Message', 'msgso'); ?></div>
	<div class="dialog-content">
		<div class="reset-gap">
			<form class="ps-message-form ps-form" role="form" onsubmit="return false;">
				<div class="ps-form-row ps-messages-recipient ps-js-recipient-single" style="display:none">
					<div class="ps-messages-label">
						<?php echo __('Recipient', 'msgso'); ?>
					</div>
					<div class="ps-messages-label">
						<div class="ps-avatar">
							<a href=""><img class="cavatar" src="" alt=""></a>
						</div>
						<div class="ps-comment-body">
							<span class="ps-comment-user"></span>
						</div>
					</div>
				</div>
				<div class="ps-form-row ps-messages-recipient ps-js-recipient-multiple" style="display:none">
					<div class="ps-messages-label">
						<?php echo __('Recipients', 'msgso'); ?>
					</div>
					<div class="ps-messages-label">
						<select name="recipients" class="recipients-search"
							data-placeholder="<?php echo __('Select Recipients', 'msgso');?>"
							data-loading="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>"
							multiple></select>
					</div>
				</div>
				<div class="ps-form-row ps-messages-recipient ps-js-recipient-loading" style="display:none">
					<div class="ps-messages-label">
						<?php echo __('Recipient', 'msgso'); ?>
					</div>
					<div class="ps-messages-label">
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" />
					</div>
				</div>
				<div class="ps-form-row">
					<div>
						<div class="ps-postbox-message ps-postbox ps-clearfix" style="">
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
									<button type="button" class="ps-btn ps-btn--postbox ps-button-cancel" style="display:none"><?php echo __('Cancel', 'msgso'); ?></button>
									<button type="button" class="ps-btn ps-btn--postbox ps-button-action postbox-submit" style="display:none"><?php echo __('Send Message', 'msgso'); ?></button>
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
				<div class="reset-gap">
					<div class="ps-alert ps-alert-danger" style="display:none"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<div style="display: none;">
	<div id="peepsomessages-no-action-selected"><?php echo __('Please select your bulk action.', 'msgso') ?></div>
	<div id="peepsomessages-no-item-selected"><?php echo __('Please select at least one message for bulk action.', 'msgso') ?></div>
</div>
