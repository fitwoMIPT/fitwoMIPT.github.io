<?php
$PeepSoActivity = PeepSoActivity::get_instance();
$PeepSoMessages = PeepSoMessages::get_instance();
$PeepSoUser = PeepSoUser::get_instance($post_author);
?>

<div class="ps-message ps-message--inbox <?php echo ($mrec_viewed) ? '' : 'ps-message--unread'; ?>">
	<div class="ps-message__checkbox">
		<div class="ps-checkbox">
			<input name="messages[]" type="checkbox" id="<?php echo $ID; ?>" value="<?php echo $ID; ?>" />
			<label for="<?php echo $ID; ?>"></label>
		</div>
	</div>

	<div class="ps-message__avatar">
		<div class="ps-avatar">
			<a href="<?php echo $PeepSoMessages->get_message_url();?>">
				<?php echo $PeepSoMessages->get_message_avatar(array('post_author' => $post_author, 'post_id' => $ID)); ?>
			</a>
		</div>
	</div>

	<div class="ps-message__body" onclick="window.location = '<?php echo $PeepSoMessages->get_message_url(); ?>'">
		<div class="ps-message__author">
		<!--<a href="<?php echo $PeepSoUser->get_profileurl(); ?>">-->
			<?php
			$args = array(
				'post_author' => $post_author, 'post_id' => $ID
			);
			$PeepSoMessages->get_recipient_name($args);
			?>
		<!--</a>-->
		</div>
		<div class="ps-message__excerpt">
			<span>
				<?php
				$PeepSoMessages->get_last_author_name($args);
				echo $PeepSoMessages->get_conversation_title(); ?>
			</span>
		</div>
	</div>
	<div class="ps-message__meta">
		<span>
			<i class="ps-icon-clock"></i> <?php $PeepSoActivity->post_age(); ?>
		</span>
	</div>
</div>
