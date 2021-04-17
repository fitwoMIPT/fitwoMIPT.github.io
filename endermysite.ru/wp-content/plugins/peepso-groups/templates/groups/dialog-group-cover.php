<div id="dialog-upload-cover">
	<div id="dialog-upload-cover-title" class="hidden"><?php echo __('Change Cover Photo', 'groupso'); ?></div>
	<div id="dialog-upload-cover-content">
		<div class="ps-loading-image" style="display: none;">
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
			<div> </div>
		</div>
		<ul class="ps-list <?php if ($PeepSoGroup->has_cover()) { echo 'ps-list-half'; } ?> upload-cover">
			<li class="ps-list-item">
				<span class="ps-btn ps-full fileinput-button">
					<?php echo __('Upload Photo', 'groupso'); ?>
					<input class="fileupload" type="file" name="filedata" />
				</span>
			</li>

			<?php if ($PeepSoGroup->has_cover()) { ?>
			<li class="ps-list-item">
				<a href="#" onclick="ps_group.remove_cover_photo(<?php echo $PeepSoGroup->get('id'); ?>); return false;" class="ps-btn ps-btn-danger ps-full"><?php echo __('Remove Cover Photo', 'groupso'); ?></a>
			</li>
			<?php } ?>

			<?php wp_nonce_field('cover-photo', '_covernonce'); ?>

			<div class="errors error-container ps-text-danger ps-js-error"></div>
		</ul>
	</div>
</div>
<div style="display: none;">
	<div id="profile-cover-error-filetype"><?php echo __('The file type you uploaded is not allowed. Only JPEG/PNG allowed.', 'groupso'); ?></div>
	<div id="profile-cover-error-filesize"><?php printf(__('The file size you uploaded is too big. The maximum file size is %s.', 'groupso'), '<strong>' . $PeepSoGroup->upload_size() . '</strong>'); ?></div>
</div>
