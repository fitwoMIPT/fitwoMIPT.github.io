<div id="dialog-upload-avatar">
	<div id="dialog-upload-avatar-title"><?php echo __('Change Avatar', 'groupso'); ?></div>
	<div id="dialog-upload-avatar-content">
		<div class="ps-loading-image" style="display: none;">
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
			<div> </div>
		</div>

		<div class="ps-page-split">
			<div class="ps-page-half upload-avatar">
				<a class="ps-btn ps-btn-small ps-full-mobile fileinput-button" href="#" onclick="return false;">
					<?php echo __('Upload Photo', 'groupso'); ?>
					<input class="fileupload" type="file" name="filedata" />
				</a>
				<a id="div-remove-avatar"
				style="<?php if (!$PeepSoGroup->has_avatar()) { ?>display:none;<?php } ?> overflow:hidden;"
				href="#" onclick="ps_group.remove_avatar(<?php echo $PeepSoGroup->get('id'); ?>); return false;"
				class="ps-btn ps-btn-danger ps-btn-small ps-full-mobile">
					<?php echo __('Remove Photo', 'groupso'); ?>
				</a>
				<div class="ps-gap"></div>

				<div class="ps-js-has-avatar" <?php echo $PeepSoGroup->has_avatar() ? '' : 'style="display:none"' ?>>
					<h5 class="ps-page-title"><?php echo __('Uploaded Photo', 'groupso'); ?></h5>
					<div id="imagePreview" class="imagePreview" style="position:relative">
						<img src="<?php echo $PeepSoGroup->get_avatar_url_orig(); ?>?<?php echo time();?>" alt="<?php echo __('Automatically Generated. (Maximum width: 160px)', 'groupso'); ?>"
							class="ps-image-preview large-profile-pic ps-name-tips" xwidth="100%"/>
					</div>
					<div class="ps-page-footer">
						<a href="#" onclick="groupsavatar.updateThumbnail(); return false;" id="" class="update-thumbnail ps-btn ps-btn-small ps-full-mobile ps-avatar-crop ps-js-crop-avatar"><?php echo __('Crop Image', 'groupso'); ?></a>
						<a href="#" onclick="groupsavatar.saveThumbnail(); return false;" id="" class="update-thumbnail-save ps-btn ps-btn-small ps-btn-primary ps-full-mobile" style="display: none;"><?php echo __('Save Thumbnail', 'groupso'); ?></a>
					</div>
				</div>

				<div class="ps-js-no-avatar" <?php echo $PeepSoGroup->has_avatar() ? 'style="display:none"' : '' ?>>
					<div class="ps-alert"><?php echo __('No avatar uploaded. Use the button above to select and upload one.' ,'groupso'); ?></div>
				</div>

			</div>

			<div class="ps-page-half ps-text--center show-avatar show-thumbnail">
				<h5 class="ps-page-title"><?php echo __('Avatar Preview', 'groupso'); ?></h5>

				<div class="ps-avatar js-focus-avatar">
					<img src="<?php echo $PeepSoGroup->get_avatar_url(); ?>?<?php echo time();?>" alt="" title="">
				</div>
				<div class="ps-gap"></div>
				<p class="reset-gap ps-text--muted"><?php echo __('This is how your Avatar will appear throughout the entire community.', 'groupso'); ?></p>
			</div>
		</div>

		<div class="errors error-container ps-text--danger ps-js-error"></div>
	</div>

	<div class="dialog-action">
		<button class="ps-btn ps-btn-small ps-btn-primary" type="button" name="rep_submit" onclick="ps_group.confirm_avatar(this); return false;"><?php echo __('Done', 'groupso'); ?></button>
	</div>
</div>
<div style="display:none">
	<div id="profile-avatar-error-filetype"><?php echo __('The file type you uploaded is not allowed. Only JPEG/PNG allowed.', 'groupso'); ?></div>
	<div id="profile-avatar-error-filesize"><?php printf(__('The file size you uploaded is too big. The maximum file size is %s.', 'groupso'), '<strong>' . $PeepSoGroup->upload_size() . '</strong>'); ?></div>
	<iframe id="ps-profile-avatar-iframe" src="<?php echo $PeepSoGroup->get_avatar_url(); ?>"></iframe>
</div>
