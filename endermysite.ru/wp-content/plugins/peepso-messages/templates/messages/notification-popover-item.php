<?php
$PeepSoMessages = PeepSoMessages::get_instance();
$PeepSoUser = PeepSoUser::get_instance($post_author);
?>
<div class="ps-messages-item <?php echo ($mrec_viewed) ? '' : 'unread'; ?>" data-id="<?php echo $PeepSoMessages->get_root_conversation();?>" data-url="<?php echo $PeepSoMessages->get_message_url(); ?>">
	<div class="ps-comment-item">
		<div class="ps-avatar-comment">

				<?php echo $PeepSoMessages->get_message_avatar(array('post_author' => $post_author, 'post_id' => $ID)); ?>

		</div>
		<div class="ps-comment-body">
			<span class="ps-comment-user">

					<?php
					$args = array(
						'post_author' => $post_author, 'post_id' => $ID
					);
					$PeepSoMessages->get_recipient_name($args);
					?>
				
			</span>
			<small class="ps-comment-time activity-post-age" data-timestamp="<?php echo strtotime($post_date); ?>">
				<?php #echo human_time_diff(strtotime($post_date), current_time('timestamp')) . ' ago'; ?>
                <?php echo sprintf(__('%s ago', 'peepso-core'), human_time_diff(strtotime($post_date), current_time('timestamp')));?>
			</small>
			<div class="ps-messages-title">
				<?php
				$PeepSoMessages->get_last_author_name($args);
				echo $PeepSoMessages->get_conversation_title(); ?>
			</div>
		</div>
	</div>
</div>