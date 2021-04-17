<div class="peepso ps-page-profile">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>

	<?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'groups')); ?>

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

            <?php if(get_current_user_id()) { ?>
                <?php $single_column = PeepSo::get_option( 'groups_single_column', 0 ); ?>
                <div class="ps-clearfix ps-groups <?php echo $single_column ? 'ps-groups--single-col' : '' ?> ps-js-groups ps-js-groups--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"></div>
                <div class="ps-scroll ps-groups-scroll ps-js-groups-triggerscroll ps-js-groups-triggerscroll--<?php echo apply_filters('peepso_user_profile_id', 0); ?>">
                    <img class="post-ajax-loader ps-js-groups-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
                </div>
            <?php
            } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }?>
		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
