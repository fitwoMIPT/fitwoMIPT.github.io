<?php
$show_result = FALSE;
global $post;

if(PeepSo::get_option('polls_show_result_before_vote', FALSE) ||  $is_voted || PeepSo::is_admin() || $post->post_author==get_current_user_id()) {
    $show_result = TRUE;
}
?>
<div class="ps-poll ps-js-poll-item">
	<?php foreach ($options as $key => $value) : ?>
		<?php
		// Percent math

		if ($show_result) {
		    if(0==$total_user_poll) {
		        $percent = __('(no votes yet)', 'peepso-polls');
            } else {
                $percent = ($value['total_user_poll'] / $total_user_poll) * 100;
            }
		}
		?>
		<div class="ps-poll__item">
			<?php if ($show_result) : ?>
				<div class="ps-poll__fill" style="width: <?php echo $percent . '%'; ?>"></div>

				<span class="ps-poll__votes">
					<?php echo '(' . $value['total_user_poll'] . ' ' . __('of', 'peepso-polls') . ' ' . $total_user_poll . ')'; ?>
				</span>
			<?php endif; ?>

			<div class="ps-checkbox ps-checkbox--poll">
				<?php if ( is_user_logged_in()) { ?>
					<input type="<?php echo $type; ?>" name="options_<?php echo $id; ?>[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" class="ace ace-switch ace-switch-2 ps-js-poll-item-option" <?php echo $is_voted || !$enabled ? 'disabled' : ''; ?> <?php echo in_array($key, $user_polls) ? 'checked' : ''; ?> />
				<?php } ?>

				<label class="lbl" for="<?php echo $key; ?>"><?php echo $value['label']; ?></label>

				<?php if ($show_result) : ?>
				<span class="ps-poll__percent">
					<?php echo (is_numeric($percent)) ? number_format($percent, 0, '.', ',') . '%' : $percent; ?>
				</span>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

	<?php if ($enabled && !$is_voted && count($options) > 1) { ?>
		<?php $has_vote = isset($user_polls) && count($user_polls) > 0; ?>

		<button class="ps-btn ps-btn-small ps-button-action ps-js-poll-item-submit" data-id="<?php echo $id; ?>" disabled="disabled"
				<?php echo $has_vote ? '' : ' disabled="disabled"' ?>
				onclick="peepso.polls.submit_vote(<?php echo $id ?>, this);">
			<?php if ($has_vote) { ?>
			<?php echo __('Change Vote', 'peepso-polls'); ?>
			<?php } else { ?>
			<?php echo __('Submit', 'peepso-polls'); ?>
			<?php } ?>
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" class="ps-js-loading" alt="loading" style="display:none" />
		</button>

		<?php if ($has_vote) { ?>
		<button class="ps-btn ps-btn-small ps-btn-danger ps-js-poll-item-unvote" data-id="<?php echo $id; ?>"
				onclick="peepso.polls.unvote(<?php echo $id ?>, this);">
			<?php echo __('Unvote', 'peepso-polls'); ?>
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="display:none" />
		</button>
		<?php } ?>

	<?php } ?>

	<?php if ( ! is_user_logged_in()) { ?>
		<div class="ps-poll__message"><?php echo __('Login to cast your vote and to see results.', 'peepso-polls'); ?></div>
	<?php } ?>
</div>
