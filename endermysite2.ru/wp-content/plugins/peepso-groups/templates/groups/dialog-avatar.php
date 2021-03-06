<div class="ps-dialog-wrapper ps-js-dialog-avatar-group">
	<div class="ps-dialog-container">
		<div class="ps-dialog ps-dialog-wide">
			<div class="ps-dialog-header">
				<span><?php echo __('Change Avatar', 'groupso'); ?></span>
				<a href="#" class="ps-dialog-close ps-js-btn-close"><span class="ps-icon-remove"></span></a>
			</div>
			<div class="ps-dialog-body ps-js-body" style="position:relative">
				<div class="ps-alert ps-alert-danger ps-js-error"></div>
				<div class="ps-page-split">
					<div class="ps-page-half">
						<a href="#" class="fileinput-button ps-btn ps-btn-small ps-full-mobile ps-js-btn-upload">
							<?php echo __('Upload Photo', 'groupso'); ?>
							<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="padding-left:5px; display:none" />
						</a>
						<a href="#" class="ps-btn ps-btn-danger ps-btn-small ps-full-mobile ps-js-btn-remove" style="overflow:hidden; {{= data.imgOriginal ? '' : 'display:none' }}">
							<?php echo __('Remove Photo', 'groupso'); ?>
							<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="padding-left:5px; display:none" />
						</a>
						<div class="ps-gap"></div>
						<div class="ps-js-has-avatar" style="{{= data.imgOriginal ? '' : 'display:none' }}">
							<h5 class="ps-page-title"><?php echo __('Uploaded Photo', 'groupso'); ?></h5>
							<div class="ps-js-preview" style="position:relative; user-select:none">
								<img src="{{= data.imgOriginal || '' }}" alt="<?php echo __('Automatically Generated. (Maximum width: 160px)', 'groupso'); ?>"
									class="ps-image-preview ps-name-tips">
							</div>
							<div class="ps-page-footer">
								<a href="#" class="ps-btn ps-btn-small ps-full-mobile ps-avatar-crop ps-js-btn-crop"><?php echo __('Crop Image', 'groupso'); ?></a>
								<a href="#" class="ps-btn ps-btn-small ps-full-mobile ps-avatar-crop ps-js-btn-crop-cancel" style="display:none"><?php echo __('Cancel Cropping', 'groupso'); ?></a>
								<a href="#" class="ps-btn ps-btn-small ps-btn-primary ps-full-mobile ps-js-btn-crop-save" style="display:none">
									<?php echo __('Save Thumbnail', 'groupso'); ?>
									<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="padding-left:5px; display:none" />
								</a>
							</div>
						</div>
						<div class="ps-js-no-avatar" style="{{= data.imgOriginal ? 'display:none' : '' }}">
							<div class="ps-alert"><?php echo __('No avatar uploaded. Use the button above to select and upload one.', 'groupso'); ?></div>
						</div>
						<div class="ps-gap"></div>
					</div>
					<div class="ps-page-half ps-text--center show-avatar show-thumbnail">
						<h5 class="ps-page-title"><?php echo __('Avatar Preview', 'groupso'); ?></h5>
						<div class="ps-avatar ps-js-avatar">
							<img src="{{= data.imgAvatar || '' }}" alt="<?php echo __('Avatar Preview', 'groupso'); ?>">
						</div>
						<div class="ps-gap"></div>
						<p class="reset-gap ps-text--muted">{{
							var textPreview = <?php echo json_encode( __('This is how <strong>%s</strong> Avatar will appear throughout the entire community.', 'groupso') ); ?>;
							textPreview = textPreview.replace('%s', data.name );
						}}{{= textPreview }}
						</p>
					</div>
				</div>
				<!-- Avatar uploader element -->
				<div style="position:relative; width:1px; height:1px; overflow:hidden">
					<input type="file" name="filedata" accept="image/*" />
				</div>
				<!-- Form disabler and loading -->
				<div class="ps-dialog-disabler ps-js-disabler">
					<div class="ps-dialog-spinner">
						<span class="ps-icon-spinner"></span>
					</div>
				</div>
			</div>
			<div class="ps-dialog-footer">
				<button class="ps-btn ps-btn-primary ps-js-btn-finalize" disabled="disabled">
					<?php echo __('Done', 'groupso'); ?>
					<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="padding-left:5px; display:none" />
				</button>
			</div>
		</div>
	</div>
</div>
