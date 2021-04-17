<div class="ps-dialog-wrapper" xmlns="http://www.w3.org/1999/html">
	<div class="ps-dialog-container">
		<div class="ps-dialog ps-dialog-wide">
			<div class="ps-dialog-header">
				<span><?php echo __('Create Group', 'groupso'); ?></span>
				<a class="ps-dialog-close ps-js-cancel" href="#"><span class="ps-icon-remove"></span></a>
			</div>
			<div class="ps-dialog-body">
				<div class="ps-form--group">
					<div class="ps-form__row">
						<label class="ps-form__label"><?php echo __('Name', 'groupso'); ?> <span class="ps-text--danger">*</span></label>
						<div class="ps-form__field ps-form__field--limit">
							<input type="text" name="group_name" class="ps-input ps-full ps-js-name-input" value=""
								   placeholder="<?php echo __("Enter your group's name...", 'groupso'); ?>" data-maxlength="<?php echo PeepSoGroup::$validation['name']['maxlength'];?>" />
							<div class="ps-form__limit"><span class="ps-js-limit"><?php echo PeepSoGroup::$validation['name']['maxlength'];?></span> <?php echo __('Characters left', 'groupso'); ?></div>
							<div class="ps-form__helper ps-text--danger ps-js-error-name" style="display:none"></div>
						</div>
					</div>
					<div class="ps-form__row">
						<label class="ps-form__label"><?php echo __('Description', 'groupso'); ?> <span class="ps-text--danger">*</span></label>
						<div class="ps-form__field ps-form__field--limit">
							<textarea name="group_desc" class="ps-textarea ps-js-desc-input"
									  placeholder="<?php echo __("Enter your group's description...", 'groupso'); ?>" data-maxlength="<?php echo PeepSoGroup::$validation['description']['maxlength'];?>"></textarea>
							<div class="ps-form__limit"><span class="ps-js-limit"><?php echo PeepSoGroup::$validation['description']['maxlength'];?></span> <?php echo __('Characters left', 'groupso'); ?></div>
							<div class="ps-form__helper ps-text--danger ps-js-error-desc" style="display:none"></div>
						</div>
					</div>

					<?php
					if(PeepSo::get_option('groups_categories_enabled', FALSE)) {

						$multiple_enabled = PeepSo::get_option('groups_categories_multiple_enabled', FALSE);
						$input_type = ($multiple_enabled) ?  'checkbox' : 'radio';

						$PeepSoGroupCategories = new PeepSoGroupCategories(FALSE, TRUE);
						$categories = $PeepSoGroupCategories->categories;
						?>
						<div class="ps-form__row">
							<label class="ps-form__label"><?php echo __('Category', 'groupso'); ?> <span class="ps-text--danger">*</span></label>
							<div class="ps-form__field ps-form__field--half ps-form__field--limit">
								<?php
								if(count($categories)) {
									foreach($categories as $id=>$category) {
										echo sprintf('<div class="ps-form__item"><div class="ps-checkbox"><input type="%s" id="category_'.$id.'" name="category_id" value="%d"><label for="category_'.$id.'">%s</label></div></div>', $input_type, $id, $category->name);
									}
								}
								?>
								<p style="clear:both;"></p>
								<div class="ps-form__helper ps-text--danger ps-js-error-category_id" style="display:none"></div>
							</div>
						</div>
					<?php } // ENDIF ?>


					<div class="ps-form__row">
						<label class="ps-form__label"><?php echo __('Privacy', 'groupso'); ?></label>
						<div class="ps-form__field">
							<?php
							$privacySettings = PeepSoGroupPrivacy::_();
							$privacyDefaultValue = PeepSoGroupPrivacy::PRIVACY_OPEN;
							$privacyDefaultSetting = $privacySettings[ $privacyDefaultValue ];
							?>
							<span class="ps-dropdown ps-dropdown--group-privacy ps-js-dropdown ps-js-dropdown--privacy">
								<button data-value="" class="ps-btn ps-btn-small ps-dropdown__toggle ps-js-dropdown-toggle">
                                    <span class="dropdown-value">
                                        <i class="<?php echo $privacyDefaultSetting['icon']; ?>"></i>
                                        <span><?php echo $privacyDefaultSetting['name']; ?></span>
                                    </span>
                                </button>
                                <input type="hidden" name="group_privacy" value="<?php echo $privacyDefaultValue; ?>" />
                                <?php echo PeepSoGroupPrivacy::render_dropdown(); ?>
							</span>

						</div>
					</div>
				</div>
			</div>
			<div class="ps-dialog-footer">
				<div>
					<?php wp_nonce_field('group-create-album', '_wpnonce'); ?>
					<button type="button" class="ps-btn ps-btn-small ps-button-cancel ps-js-cancel"><?php echo __('Cancel', 'groupso'); ?></button>
					<button type="button" class="ps-btn ps-btn-small ps-button-action ps-js-submit">
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" class="ps-js-loading" alt="loading" style="display:none" />
						<?php echo __('Create Group', 'groupso'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
