<?php $PeepSoGroupUser = new PeepSoGroupUser($group->id); ?>
<div class="peepso ps-page--group">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>
	<?php //PeepSoTemplate::exec_template('general', 'register-panel'); ?>

	<?php if($PeepSoGroupUser->can('access')) { ?>

		<?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

		<section id="mainbody" class="ps-page-unstyled">
			<section id="component" role="article" class="ps-clearfix">

				<?php if (! get_current_user_id()) { PeepSoTemplate::exec_template('general','login-profile-tab'); } ?>

				<?php
                $PeepSoGroupUser = new PeepSoGroupUser($group->id, get_current_user_id());
                PeepSoTemplate::exec_template('groups', 'group-members-tabs', array('tab' => FALSE, 'PeepSoGroupUser' => $PeepSoGroupUser, 'group' => $group));

                PeepSoTemplate::exec_template('groups', 'group-members-search-form', array());

                ?>

				<div class="ps-clearfix mb-20"></div>
				<div class="ps-members ps-clearfix ps-js-group-members"></div>
				<div class="ps-scroll ps-clearfix ps-js-group-members-triggerscroll">
					<img class="post-ajax-loader ps-js-group-members-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
				</div>
			</section>
		</section>
	<?php } ?>
</div><!--end row-->

<?php

if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity' ,'dialogs');
}
