<div class="ps-poll__form ps-js-polls">
	<div class="ps-postbox-fetched"></div>
	<div style="position:relative">
		<div style="position:relative">
			<div class="ui-sortable">
				<div class="ps-poll__option">
					<a class="ps-btn ps-js-handle ui-sortable-handle" title="<?php echo __('Move', 'peepso-polls'); ?>" href="#"><i class="ps-icon-move"></i></a>
					<input class="ps-input" type="text" placeholder="<?php echo __('Option 1', 'peepso-polls'); ?>">
					<a id="ps-delete-option" class="ps-btn ps-btn--delete" title="<?php echo __('Delete', 'peepso-polls'); ?>" href="#"><i class="ps-icon-trash"></i></a>
				</div>
				<div class="ps-poll__option">
					<a class="ps-btn ps-js-handle ui-sortable-handle" title="<?php echo __('Move', 'peepso-polls'); ?>" href="#"><i class="ps-icon-move"></i></a>
					<input class="ps-input" type="text" placeholder="<?php echo __('Option 2', 'peepso-polls'); ?>">
					<a id="ps-delete-option" class="ps-btn ps-btn--delete" title="<?php echo __('Delete', 'peepso-polls'); ?>" href="#"><i class="ps-icon-trash"></i></a>
				</div>
			</div>

			<div class="ps-poll__actions">
				<button class="ps-btn ps-btn-small ps-button-action" id="ps-add-new-option"><?php echo __('Add new option', 'peepso-polls');?></button>

				<?php if (isset($multiselect) && $multiselect) : ?>
					<div class="ps-checkbox">
						<input type="checkbox" id="allow-multiple" class="ace ace-switch ace-switch-2 allow-multiple" />
						<label class="lbl" for="allow-multiple"><?php echo __('Allow multiple options selection', 'peepso-polls'); ?></label>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
