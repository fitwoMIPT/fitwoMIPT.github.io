<div class="peepso ps-page-profile">
    <?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

    <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'videos')); ?>

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">
            <?php if(get_current_user_id()) { ?>
            <div class="ps-page-filters">
                <select class="ps-select ps-full ps-js-videos-sortby ps-js-videos-sortby--<?php echo apply_filters('peepso_user_profile_id', 0); ?>">
                    <option value="desc"><?php echo __('Newest first', 'vidso');?></option>
                    <option value="asc"><?php echo __('Oldest first', 'vidso');?></option>
                </select>
            </div>

            <div class="ps-video mb-20"></div>
            <div class="ps-video ps-js-videos ps-js-videos--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"></div> &nbsp;
            <div class="ps-video ps-js-videos-triggerscroll ps-js-videos-triggerscroll--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                <img class="post-ajax-loader ps-js-videos-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
            </div>
            <?php } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }?>
        </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
