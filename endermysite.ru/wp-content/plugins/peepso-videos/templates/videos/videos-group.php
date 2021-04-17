<div class="peepso ps-page-profile ps-page--group">
    <?php PeepSoTemplate::exec_template('general','navbar'); ?>
    <?php //PeepSoTemplate::exec_template('general', 'register-panel'); ?>

    <?php $PeepSoGroupUser = new PeepSoGroupUser($group->id, get_current_user_id());?>
    <?php if($PeepSoGroupUser->can('access')) { ?>

    <?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">
            <?php if (! get_current_user_id()) { PeepSoTemplate::exec_template('general','login-profile-tab'); } ?>

            <div class="ps-page-filters">
                <select class="ps-select ps-full ps-js-videos-sortby">
                    <option value="desc"><?php echo __('Newest first', 'vidso');?></option>
                    <option value="asc"><?php echo __('Oldest first', 'vidso');?></option>
                </select>
            </div>

            <div class="ps-video mb-20"></div>
            <div class="ps-video ps-js-videos"></div> &nbsp;
            <div class="ps-video ps-js-videos-triggerscroll">
                <img class="post-ajax-loader ps-js-videos-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
            </div>
        </section><!--end component-->
    </section><!--end mainbody-->

    <?php } ?>

</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
