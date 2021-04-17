<?php
    echo $args['before_widget'];
    $owner = PeepSoUser::get_instance($instance['user_id']);
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <a href="<?php echo $owner->get_profileurl();?><?php echo PeepSoVideos::profile_menu_slug();?>"><?php
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
        ?></a>
    </div>
    <?php
    if(count($instance['list']))
    {
    ?>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <div class="ps-widget--videos">
            <div class="ps-widget__videos">
            <?php
                foreach ($instance['list'] as $video)
                {
                    $video = (array) $video;
                    echo PeepSoTemplate::exec_template('videos', 'video-item-widget', $video);
                }
            ?>
            </div>
        </div>
    </div>
    <?php
        // @TODO add template tag for "total"
    ?>
    <div class="ps-widget__footer<?php echo $instance['class_suffix'];?>">
        <a href="<?php echo $owner->get_profileurl();?><?php echo PeepSoVideos::profile_menu_slug();?>">
            <span><?php echo __('View All', 'vidso');?></span>
            <span> (<?php echo $instance['total'];?>)</span>
        </a>
    </div>
    <?php } else { ?>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <?php
        if ($instance['media_type'] == 'all') {
            echo "<span class='ps-text--muted'>".__('No media', 'vidso')."</span>";
        } else {
            if($instance['media_type'] == 'audio') {
                echo "<span class='ps-text--muted'>".__('No audio', 'vidso')."</span>";
            } elseif ($instance['media_type'] == 'video') {
                echo "<span class='ps-text--muted'>".__('No video', 'vidso')."</span>";
            } 
        }
        ?>
    </div>
    <?php } ?>
</div>

<?php

echo $args['after_widget'];

// EOF
