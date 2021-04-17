<?php
$PeepSoUser	= PeepSoUser::get_instance($freq_user_id);
?>
<div class="ps-notification-item">
	<div class="ps-comment-item">
		<div class="ps-avatar-comment">
			<a href="<?php echo $PeepSoUser->get_profileurl(); ?>">
				<img src="<?php echo $PeepSoUser->get_avatar(); ?>" alt="<?php echo trim(strip_tags($PeepSoUser->get_fullname())); ?>">
			</a>
		</div>
		<div class="ps-comment-body">
			<div class="ps-messages-title">
				<a href="<?php echo $PeepSoUser->get_profileurl(); ?>">
					<?php

					do_action('peepso_action_render_user_name_before', $PeepSoUser->get_id());

					echo $PeepSoUser->get_fullname(); 
					
					do_action('peepso_action_render_user_name_after', $PeepSoUser->get_id());

					?>
				</a>
			</div>
		</div>
		<div class="ps-popover-actions">
			<button class="ps-btn ps-btn-small ps-button-cancel" onclick="psfriends.ignore_notification_request(this, <?php echo $freq_id;?>)">
				<?php echo __('Ignore', 'friendso'); ?>
			</button>
			<button class="ps-btn ps-btn-small ps-button-action" onclick="psfriends.accept_notification_request(this, <?php echo $freq_id;?>)">
				<?php echo __('Approve', 'friendso'); ?>
			</button>
		</div>
	</div>
</div>