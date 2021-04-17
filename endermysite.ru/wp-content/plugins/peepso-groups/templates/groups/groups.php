<?php

$categories_enabled = FALSE;
$categories_tab  = FALSE;

if(PeepSo::get_option('groups_categories_enabled', FALSE)) {

	$categories_enabled = TRUE;

	$PeepSoGroupCategories = new PeepSoGroupCategories(FALSE, NULL);
	$categories = $PeepSoGroupCategories->categories;
	if (!isset($_GET['category'])) {
		$categories_default_view = PeepSo::get_option('groups_categories_default_view', 0);
		$_GET['category'] = $categories_default_view;
	}

	if (!isset($_GET['category']) || (isset($_GET['category']) && intval($_GET['category'])==1)) {
		$categories_tab = TRUE;
	}
}
?>
<div class="peepso">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>
	<?php PeepSoTemplate::exec_template('general', 'register-panel'); ?>
	<?php if(get_current_user_id() || (get_current_user_id() == 0 && $allow_guest_access)) { ?>
		<section id="mainbody" class="ps-page-unstyled">
			<section id="component" role="article" class="ps-clearfix">
				<?php if(PeepSoGroupUser::can_create()) { ?>
				<div class="ps-page__actions-wrapper">
					<div class="ps-page__actions">
						<a class="ps-btn ps-btn-small" href="#" onclick="peepso.groups.dlgCreate(); return false;">
							<?php echo __('Create Group', 'groupso');?>
						</a>
					</div>
				</div>
				<?php } ?>

				<?php if(!$categories_tab) { ?>

				<form class="ps-form ps-form-search" role="form" name="form-peepso-search" onsubmit="return false;">
					<div class="ps-form-row">
						<input placeholder="<?php echo __('Start typing to search...', 'groupso');?>" type="text" class="ps-input full ps-js-groups-query" name="query" value="<?php echo esc_attr($search); ?>" />
					</div>
					<a href="#" class="ps-form-search-opt" onclick="return false;">
						<span class="ps-icon-cog"></span>
					</a>
				</form>
				<?php
				$default_sorting = '';
				if(!strlen(esc_attr($search)))
				{
                    $default_sorting = PeepSo::get_option('groups_default_sorting','id');
                    $default_sorting_order = PeepSo::get_option('groups_default_sorting_order','DESC');
				}

				?>
				<div class="ps-js-page-filters" style="<?php echo ($categories_enabled && !$categories_tab) ? "" : "display:none";?>">
					<div class="ps-filters">
						<div class="ps-filters__item">
							<label class="ps-filters__item-label"><?php echo __('Sort', 'groupso'); ?></label>
                            <select class="ps-select ps-js-groups-sortby">
                                <option value="id"><?php echo __('Recently added', 'groupso'); ?></option>
                                <option <?php echo ('post_title' == $default_sorting) ? ' selected="selected" ' : '';?> value="post_title"><?php echo __('Alphabetical', 'groupso'); ?></option>
                                <option <?php echo ('meta_members_count' == $default_sorting) ? ' selected="selected" ' : '';?>value="meta_members_count"><?php echo __('Members count', 'groupso'); ?></option>
                            </select>
						</div>

						<div class="ps-filters__item">
							<label class="ps-filters__item-label">&nbsp;</label>
                            <select class="ps-select ps-js-groups-sortby-order">
                                <option value="DESC"><?php echo __('Descending', 'groupso'); ?></option>
                                <option <?php echo ('ASC' == $default_sorting_order) ? ' selected="selected" ' : '';?> value="ASC"><?php echo __('Ascending', 'groupso'); ?></option>
                            </select>
						</div>


						<?php if($categories_enabled) { ?>

							<div class="ps-filters__item">
								<label class="ps-filters__item-label"><?php echo __('Category', 'groupso'); ?></label>
								<select class="ps-select ps-js-groups-category">
									<option value="0"><?php echo __('No filter', 'groupso'); ?></option>
									<?php
									if(count($categories)) {
										foreach($categories as $id=>$cat) {
										    $count = PeepSoGroupCategoriesGroups::update_stats_for_category($id);
											$selected = "";
											if($id==$category) {
												$selected = ' selected="selected"';
											}
											echo "<option value=\"$id\"{$selected}>{$cat->name} ($count)</option>";
										}
									}

									$count_uncategorized = PeepSoGroupCategoriesGroups::update_stats_for_category();
									if ($count_uncategorized > 0) {
										?>
										<option value="-1" <?php if(-1 == $category) { echo 'selected="selected"';}?>><?php echo __('Uncategorized', 'groupso'); ?></option>
										<?php
									}
									?>
								</select>
							</div>

						<?php } // ENDIF ?>

					</div>
				</div>
				<?php } ?>

				<?php if($categories_enabled) { ?>
				<div class="ps-tabs__wrapper">
					<div class="ps-tabs ps-tabs--arrows">
						<div class="ps-tabs__item <?php if(!$categories_tab) echo "current";?>"><a href="<?php echo PeepSo::get_page('groups').'/?category=0';?>"><?php echo __('Groups', 'groupso'); ?></a></div>
						<div class="ps-tabs__item <?php if($categories_tab) echo "current";?>"><a href="<?php echo PeepSo::get_page('groups').'/?category=1';?>"><?php echo __('Group Categories', 'groupso'); ?></a></div>
					</div>
				</div>
				<?php } ?>


				<?php  if($categories_tab) { ?>

				<!-- Categories -->
				<div class="ps-clearfix mb-20"></div>
				<div class="ps-accordion ps-js-group-cats" style="margin-bottom:10px"></div>
				<div class="ps-scroll ps-clearfix ps-js-group-cats-triggerscroll">
					<img class="post-ajax-loader ps-js-group-cats-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" style="display:none" />
				</div>

				<?php } else { ?>
				<?php $single_column = PeepSo::get_option( 'groups_single_column', 0 ); ?>
				<div class="ps-clearfix mb-20"></div>
				<div class="ps-groups <?php echo $single_column ? 'ps-groups--single-col' : '' ?> ps-clearfix ps-js-groups"></div>
				<div class="ps-scroll ps-clearfix ps-js-groups-triggerscroll">
					<img class="post-ajax-loader ps-js-groups-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
				</div>

				<?php } ?>

			</section>
		</section>
	<?php } ?>
</div><!--end row-->

<?php

if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity', 'dialogs');
}
