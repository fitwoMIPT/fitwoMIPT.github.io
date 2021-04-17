<?php
$PeepSoActivity = PeepSoActivity::get_instance();
$PeepSoUser		= PeepSoUser::get_instance($post_author);
$PeepSoMessages = PeepSoMessages::get_instance();
$content_extra  = apply_filters('peepso_post_extras', array());
?>

<div class="ps-chat__item ps-js-message ps-js-message-<?php echo $ID ?> <?php echo $post_author == get_current_user_id() ? 'ps-chat__item--author' : '' ?>" data-id="<?php echo $ID ?>">
    <div class="ps-chat__item-inner">
        <div class="ps-chat__avatar">
            <div class="ps-avatar ps-avatar--chat">
                <a href="<?php echo $PeepSoUser->get_profileurl(); ?>">
                    <img src="<?php echo $PeepSoUser->get_avatar(); ?>" alt="">
                </a>
            </div>
        </div>

        <div class="ps-chat__message">
            <div class="ps-chat__meta">
                <div class="ps-chat__user">
                    <a href="<?php echo $PeepSoUser->get_profileurl(); ?>"><?php
                    
                    //[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
                    do_action('peepso_action_render_user_name_before', $PeepSoUser->get_id());

                    echo $PeepSoUser->get_fullname(); 

                    //[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
                    do_action('peepso_action_render_user_name_after', $PeepSoUser->get_id());

                    ?></a>
                </div>
                <div class="ps-chat__time">
                    <?php $PeepSoActivity->post_age(); ?>
                </div>
                <?php if (( 1 === intval(PeepSo::get_option('messages_read_notification', 1)) ) && ( $post_author == get_current_user_id() )) : ?>
                    <span class="ps-chat__status">
                        <i class="ps-icon-ok"></i>
                    </span>
                <?php endif; ?>
            </div>

            <div class="ps-chat__bubble-wrapper">
                <div class="ps-chat__reaction"><?php echo implode(' ', $content_extra); ?></div>
                
                <div class="ps-chat__bubble"><?php $PeepSoActivity->content(); ?></div>

                <div class="ps-chat__actions">
                    <a href="<?php echo $PeepSoMessages->get_delete_message_url();?>" onclick="return ps_messages.delete_single_message('<?php echo $ID ?>');">
                        <i class="ps-icon-trash"></i>
                    </a>
                </div>
            </div>

            <div class="ps-chat__attachments"><?php $PeepSoActivity->post_attachment(); ?></div>
        </div>
    </div>
</div>
