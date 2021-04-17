<?php
$PeepSoPhotos = PeepSoPhotos::get_instance();
?>
<div class="ps-dialog-wrapper">
	<div class="ps-dialog-container">
		<div class="ps-dialog ps-dialog-wide">
			<div class="ps-dialog-header">
				<span><?php echo __('Create Album', 'picso'); ?></span>
				<a class="ps-dialog-close ps-js-cancel" href="#"><span class="ps-icon-remove"></span></a>
			</div>
			<div class="ps-dialog-body">
				<div class="ps-form ps-form--album">
					<div class="ps-form__container">
						<div class="ps-form__row ps-form__row--half">
							<label class="ps-form__label"><?php echo __('Album name', 'picso'); ?> <span class="ps-text--danger">*</span></label>
							<div class="ps-form__field">
								<input type="text" name="album_name" maxlength="50" class="ps-input" value="" />
							</div>
							<span class="ps-form__helper ps-text--danger ps-js-error-name" style="display:none"><?php echo __('Album name can\'t be empty', 'picso'); ?></span>
						</div>

					<?php
					$privacy = apply_filters('peepso_photos_create_album_privacy_hide', false);
					if(!$privacy) {
					?>
						<div class="ps-form__row ps-form__row--half">
							<label class="ps-form__label"><?php echo __('Album privacy', 'picso'); ?></label>
							<div class="ps-form__field">
								<select name="album_privacy" class="ps-select"><?php
									foreach ($access_settings as $key => $value) {
										echo '<option value="' . $key . '">' . $value['label'] . '</option>';
									}
								?></select>
							</div>
						</div>
					<?php
					}
					// adding capability to extends fields for other plugins
					$PeepSoPhotos->photo_album_extra_fields();
					?>
						<div class="ps-form__row">
							<label class="ps-form__label"><?php echo __('Album description', 'picso'); ?></label>
							<div class="ps-form__field"><textarea name="album_desc" class="ps-textarea"></textarea></div>
						</div>
					</div>
				</div>

				<div class="ps-clearfix"></div>
				<div class="ps-photos__upload ps-js-photos-container" style="display:none"></div>
				<div class="ps-photos__upload-area ps-js-photos-upload">
					<span class="ps-js-photos-upload-button">
						<i class="ps-icon-upload"></i>
						<?php echo __('Upload photos to album', 'picso'); ?>
					</span>
				</div>
				<span class="ps-js-error-photo ps-text--danger ps-form__helper" style="display:none"><?php echo __('Please select at least one photo to be uploaded', 'picso'); ?></span>
			</div>
			<div class="ps-dialog-footer">
				<div>
					<?php wp_nonce_field('photo-create-album', '_wpnonce'); ?>
					<button type="button" class="ps-btn ps-btn-small ps-button-cancel ps-js-cancel"><?php echo __('Cancel', 'picso'); ?></button>
					<button type="button" class="ps-btn ps-btn-small ps-button-action ps-js-submit">
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" class="ps-js-loading" alt="loading" style="margin-right:5px;display:none" />
						<?php echo __('Create Album', 'picso'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
