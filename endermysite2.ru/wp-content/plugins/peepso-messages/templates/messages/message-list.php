<?php
$PeepSoMessages = PeepSoMessages::get_instance();
?>

<form action="" class="ps-form ps-messages__search ps-js-messages-search-form" role="form" onsubmit="return false;">
	<div class="ps-messages__search-inner">
		<input type="text" class="ps-input search-query" name="query" aria-describedby="queryStatus" value="<?php echo esc_attr($query);?>" placeholder="<?php echo esc_attr(__('Search by content, or user', 'msgso')); ?>" />
		<button type="submit" class="ps-btn"><i class="ps-icon-search"></i></button>
	</div>
	<div class="ps-messages__search-results ps-js-loading" style="display: none;">
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" />
	</div>
</form>

<?php if ($total <= 0) : ?>
	<?php if (class_exists('PeepSoMessagesPlugin')) : ?>
	<div class="ps-messages__intro">
		<p><?php echo __('No messages found.' ,'msgso'); ?></p>
		<?php do_action('peepso_messages_list_header'); ?>
	</div>
	<?php else : ?>
	<div class="ps-alert">
		<?php echo __('No messages found.' ,'msgso'); ?>
	</div>
	<?php endif; ?>
<?php else : ?>
	<form class="ps-form ps-messages__inbox-form" action="<?php PeepSo::get_page('messages');?>" method="post">
		<?php wp_nonce_field('messages-bulk-action', '_messages_nonce'); ?>
		<div class="ps-messages__inbox-actions">
			<div class="ps-checkbox">
				<input type="checkbox" id="messages-check" onclick="ps_messages.toggle_checkboxes(this)" value="" />
				<label for="messages-check"></label>
			</div>
			<div class="ps-select__wrapper">
				<?php $PeepSoMessages->display_bulk_actions($type); ?>
				<button type="button" class="ps-btn ps-btn-small ps-js-bulk-actions"><?php echo __('Apply', 'msgso')?></button>
			</div>
			<?php do_action('peepso_messages_list_header'); ?>
		</div>
		<div class="ps-messages__inbox-list">
			<?php
				while ($message = $PeepSoMessages->get_next_message()) {
					$PeepSoMessages->show_message($message);
				}
			?>
		</div>
	</form>

	<div class="ps-pagination">
		<div class="ps-pagination__inner">
			<a href="javascript:" class="ps-pagination__item ps-pagination__item--prev ps-js-prev">
				<i class="ps-icon-caret-left"></i>
			</a>
			<span class="ps-pagination__item ps-pagination__item--total">
				<?php $PeepSoMessages->display_totals();?>
			</span>
			<a href="javascript:" class="ps-pagination__item ps-pagination__item--next ps-js-next">
				<i class="ps-icon-caret-right"></i>
			</a>
		</div>
	</div>
<?php endif; ?>
