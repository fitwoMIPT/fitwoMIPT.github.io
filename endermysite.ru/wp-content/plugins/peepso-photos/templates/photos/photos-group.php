<div class="peepso ps-page-profile ps-page--group">
    <?php PeepSoTemplate::exec_template('general','navbar'); ?>
    <?php //PeepSoTemplate::exec_template('general', 'register-panel'); ?>
    
    <?php $PeepSoGroupUser = new PeepSoGroupUser($group->id, get_current_user_id());?>
    <?php if($PeepSoGroupUser->can('access')) { ?>

    <?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">
            <?php if (! get_current_user_id()) { PeepSoTemplate::exec_template('general','login-profile-tab'); } ?>

            <?php
                if($PeepSoGroupUser->can('post')) {
                ?>
                <div class="ps-page__actions">
                    <a class="ps-btn ps-btn-small" href="#" onclick="peepso.photos.show_dialog_album(<?php echo get_current_user_id();?>, this); return false;"><i class="ps-icon-plus"></i><?php echo __('Create Album', 'picso'); ?></a>
                </div>
                <?php
                }
            ?>

            <h4 class="ps-page-title">
                <?php if('latest' === $current) echo __('Photos', 'picso'); if('album' === $current) echo __('Albums', 'picso'); ?>
            </h4>

            <div class="ps-tabs__wrapper">
                <div class="ps-tabs ps-tabs--arrows">
                    <div class="ps-tabs__item <?php if('latest' === $current) echo 'current' ?>"><a href="<?php echo PeepSoSharePhotos::get_group_url($view_group_id, 'latest'); ?>"><?php echo __('Photos', 'picso'); ?></a></div>
                    <div class="ps-tabs__item <?php if('album' === $current) echo 'current' ?>"><a href="<?php echo PeepSoSharePhotos::get_group_url($view_group_id, 'album'); ?>"><?php echo __('Albums', 'picso'); ?></a></div>
                </div>
            </div>

            <div class="ps-clearfix mb-20"></div>

            <div class="ps-page-filters" style="display:none;">
                <select class="ps-select ps-full ps-js-<?php echo $type?>-sortby ps-js-<?php echo $type?>-sortby--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                    <option value="desc"><?php echo __('Newest first', 'picso');?></option>
                    <option value="asc"><?php echo __('Oldest first', 'picso');?></option>
                </select>
            </div>

            <div class="ps-clearfix mb-20"></div>
            <div class="ps-<?php echo $type?> ps-js-<?php echo $type?> ps-js-<?php echo $type?>--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>"></div>
            <div class="ps-scroll ps-js-<?php echo $type?>-triggerscroll ps-js-<?php echo $type?>-triggerscroll--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                <img class="post-ajax-loader ps-js-<?php echo $type?>-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
            </div>
            <div class="ps-clearfix mb-20"></div>

        </section><!--end component-->
    </section><!--end mainbody-->

    <?php } ?>
</div><!--end row-->

<?php PeepSoTemplate::exec_template('activity','dialogs'); ?>
