<?php
$PeepSoActivityShortcode = PeepSoActivityShortcode::get_instance();
$PeepSoGroupUser = new PeepSoGroupUser($group->id);
$small_thumbnail = PeepSo::get_option('small_url_preview_thumbnail', 0);
?>
<div class="peepso ps-page--group">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>
	<?php //PeepSoTemplate::exec_template('general', 'register-panel'); ?>

	<?php if($PeepSoGroupUser->can('access')) { ?>

		<?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

		<section id="mainbody" class="ps-page-unstyled">
			<section id="component" role="article" class="ps-clearfix">
				<?php
				if ($PeepSoGroupUser->can('post')) {
					PeepSoTemplate::exec_template('general', 'postbox-legacy');
				} else {
					// default message for non-members
					$message = __('You must join the group to be able to participate.' ,'groupso');

                    if($group->is_readonly) {
                        $message = __('This is an announcement Group, only the Owners and Managers can create new posts.', 'groupso');
                    }

					// optional message for unpublished groups
					if(!$group->published) {
						$message = __('Currently group is unpublished.', 'groupso');
					}

					if(get_current_user_id()) {
					?>
					<div class="ps-box ps-box--message" >
						<div class="ps-box__body" >
							<?php echo $message;?>
						</div>
					</div>
					<?php
					} else {
						PeepSoTemplate::exec_template('general','login-profile-tab');
					}
				}

                if(PeepSo::is_admin() || $PeepSoGroupUser->is_member) {
                    PeepSoTemplate::exec_template('activity', 'activity-stream-filters-simple', array());
                }

				if(PeepSo::is_admin() || $group->is_open || $PeepSoGroupUser->is_member) {

                ?>

				<!-- stream activity -->
                <input type="hidden" id="peepso_context" value="group" />
				<div class="ps-stream-wrapper">
					<div id="ps-activitystream-recent" class="ps-stream-container <?php echo $small_thumbnail ? '' : 'ps-stream-container-narrow' ?>" style="display:none"></div>
                    <div id="ps-activitystream" class="ps-stream-container <?php echo $small_thumbnail ? '' : 'ps-stream-container-narrow' ?>" style="display:none"></div>

                    <div id="ps-activitystream-loading">
                        <?php PeepSoTemplate::exec_template('activity', 'activity-placeholder'); ?>
                    </div>

					<div id="ps-no-posts" class="ps-alert" style="display:none"><?php echo __('No posts found.', 'groupso'); ?></div>
					<div id="ps-no-posts-match" class="ps-alert" style="display:none"><?php echo __('No posts found.', 'groupso'); ?></div>
					<div id="ps-no-more-posts" class="ps-alert" style="display:none"><?php echo __('Nothing more to show.', 'groupso'); ?></div>

					<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
				</div>

                <?php } ?>
			</section>
		</section>
	<?php } ?>
</div><!--end row-->

<?php
if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity' ,'dialogs');
}
