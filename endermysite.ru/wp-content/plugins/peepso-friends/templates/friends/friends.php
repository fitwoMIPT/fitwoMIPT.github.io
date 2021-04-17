<div class="peepso ps-page-profile">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>

	<?php PeepSoTemplate::exec_template('profile','focus', array('current'=>'friends')); ?>

	<section id="mainbody" class="ps-page-unstyled">
		<section id="component" role="article" class="ps-clearfix">

			<?php
            if(get_current_user_id()) {

                if ($view_user_id == get_current_user_id()) {
                    PeepSoTemplate::exec_template('friends', 'submenu', array('current'=>'friends'));
                }

                ?>
                <div class="ps-clearfix mb-20"></div>
                <div class="ps-clearfix ps-members ps-js-friends ps-js-friends--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"></div>
                <div class="ps-scroll ps-friends-scroll ps-js-friends-triggerscroll ps-js-friends-triggerscroll--<?php echo apply_filters('peepso_user_profile_id', 0); ?>">
                    <img class="post-ajax-loader ps-js-friends-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
                </div>
            <?php } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            } ?>
		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity','dialogs'); ?>
