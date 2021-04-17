<div class="ps-commentbox__addon ps-js-addon-photo" style="display:none">
	<div class="ps-popover__arrow ps-popover__arrow--up"></div>

	<img class="ps-js-img" alt="photo"
		src="<?php echo isset($thumb) ? $thumb : ''; ?>"
		data-id="<?php echo isset($id) ? $id : ''; ?>" />

	<div class="ps-loading ps-js-loading">
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" />
	</div>

	<div class="ps-commentbox__addon-remove ps-js-remove">
		<?php wp_nonce_field('remove-temp-files', '_wpnonce_remove_temp_comment_photos'); ?>
		<i class="ps-icon-remove"></i>
	</div>
</div>
