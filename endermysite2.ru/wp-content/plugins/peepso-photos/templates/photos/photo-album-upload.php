<div class="ps-dialog-wrapper">
	<div class="ps-dialog-container">
		<div class="ps-dialog ps-dialog-wide">
			<div class="ps-dialog-header">
				<span><?php echo __('Upload Photo', 'picso'); ?></span>
				<a class="ps-dialog-close ps-js-cancel" href="#"><span class="ps-icon-remove"></span></a>
			</div>
			<div class="ps-dialog-body">
				<span><?php echo __('Photo privacy is inherited from the album', 'picso'); ?></span>
				<div class="ps-photos__upload ps-js-photos-container" style="display:none"></div>
				<div class="ps-photos__upload-area ps-js-photos-upload">
					<span class="ps-js-photos-upload-button">
						<i class="ps-icon-upload"></i>
						<?php echo __('Upload photos to album', 'picso'); ?>
					</span>
				</div>
				<span class="ps-text--danger ps-js-error-photo" style="display:none"><?php echo __('Please select at least one photo to be uploaded', 'picso'); ?></span>
			</div>
			<div class="ps-dialog-footer">
				<div>
					<?php wp_nonce_field('photo-add-to-album', '_wpnonce'); ?>
					<button type="button" class="ps-btn ps-btn-small ps-button-cancel ps-js-cancel"><?php echo __('Cancel', 'picso'); ?></button>
					<button type="button" class="ps-btn ps-btn-small ps-button-action ps-js-submit">
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" class="ps-js-loading" alt="loading" style="display:none" />
						<?php echo __('Add photos to album', 'picso'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
