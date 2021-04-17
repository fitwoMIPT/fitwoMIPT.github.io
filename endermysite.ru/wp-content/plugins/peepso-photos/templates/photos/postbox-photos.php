<div class="ps-postbox-photos">
	<div class="ps-postbox-fetched"></div>
	<div style="position:relative">
		<div id="ps-upload-container" class="ps-postbox-input ps-inputbox">
			<div class="ps-postbox-photo-upload">
				<div class="ps-postbox-info">
					<i class="ps-icon-picture"></i>
					<span><?php echo __('Click here to start uploading photos', 'picso'); ?></span>
				</div>
				<?php if (isset($photo_size)) { ?>
				<span><?php echo sprintf(__('Max photo dimensions: %1$s x %2$spx | Max file size: %3$sMB', 'picso'), $photo_size['max_width'], $photo_size['max_height'], $photo_size['max_size']); ?></span>
				<?php } ?>
			</div>
			<div class="ps-postbox-preview" style="display:none;">
				<div class="ps-js-photos-container"></div>
			</div>
		</div>
	</div>
</div>

<div id="photo-supported-format" style="display:none;"><?php echo __('Supported formats are: gif, jpg, jpeg, tiff, and png.', 'picso'); ?></div>
<div id="photo-comment-label" style="display:none;"><?php echo __('Say something about this photo...', 'picso'); ?></div>
