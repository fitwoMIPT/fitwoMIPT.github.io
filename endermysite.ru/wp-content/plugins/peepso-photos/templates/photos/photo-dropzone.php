<div>
	<input type="file" name="filedata[]" accept=".gif,.jpg,.jpeg,.png,.tif,.tiff" multiple style="display:none" />
	<?php wp_nonce_field('remove-temp-files', '_wpnonce_remove_temp_files'); ?>
	<ul class="ps-list ps-clearfix ps-js-photos">
		<li class="ps-postbox-photo-item-add ps-js-upload">
			<span><i class="ps-icon-plus"></i></span>
		</li>
	</ul>
</div>
