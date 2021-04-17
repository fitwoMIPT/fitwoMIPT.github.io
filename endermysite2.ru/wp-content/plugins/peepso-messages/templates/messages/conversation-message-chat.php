<?php
$PeepSoActivity = PeepSoActivity::get_instance();
$PeepSoUser		= PeepSoUser::get_instance($post_author);
?>
<div class="ps-conversation-item ps-js-message ps-js-message-<?php echo $ID ?> <?php echo $post_author == get_current_user_id() ? 'my-message' : '' ?>" data-id="<?php echo $ID ?>">
    <div class="ps-conversation-avatar">
        <a class="ps-avatar" href="<?php echo $PeepSoUser->get_profileurl(); ?>">
            <img width="32" src="<?php echo $PeepSoUser->get_avatar(); ?>" alt="">
        </a>
    </div>
    <div class="ps-conversation-body">
        <div class="ps-conversation-user">
            <a href="<?php echo $PeepSoUser->get_profileurl(); ?>"><?php
            
            //[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
            do_action('peepso_action_render_user_name_before', $PeepSoUser->get_id());

            echo $PeepSoUser->get_fullname(); 

            //[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
            do_action('peepso_action_render_user_name_after', $PeepSoUser->get_id());

            ?></a>
        </div>

        <?php
        $content_extra = apply_filters('peepso_post_extras', array());
        ?>

        <div class="ps-conversation-content"><?php if(count($content_extra)) { echo '<span style="font-size:10px;font-style:italic;">'.implode('<br/>', $content_extra)."</span> ";}?><?php $PeepSoActivity->content(); ?></div>

        <div class="ps-conversation-attachment"><?php $PeepSoActivity->post_attachment(); ?></div>

        <div class="ps-conversation-time">
            <small>
                <?php $PeepSoActivity->post_age(); ?>
                <?php if (( 1 === intval(PeepSo::get_option('messages_read_notification', 1)) ) && ( $post_author == get_current_user_id() )) { ?>
                <span class="ps-icon-ok"></span>
                <?php } ?>
            </small>
        </div>
    </div>
</div>
