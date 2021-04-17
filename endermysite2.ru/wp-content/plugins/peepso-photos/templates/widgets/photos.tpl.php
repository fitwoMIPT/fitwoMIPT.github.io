<?php
    echo $args['before_widget'];
    $owner = PeepSoUser::get_instance($instance['user_id']);
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <a href="<?php echo $owner->get_profileurl();?>photos"><?php
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
        <div class="ps-widget--photos">
            <div class="ps-widget__photos">
            <?php
                foreach ($instance['list'] as $photo)
                {
                    PeepSoTemplate::exec_template('photos', 'photo-item-widget', (array)$photo);
                }
            ?>
            </div>
        </div>
    </div>
    <?php
        // @TODO add template tag for "total"
    ?>
    <div class="ps-widget__footer<?php echo $instance['class_suffix'];?>">
        <a href="<?php echo $owner->get_profileurl();?>photos">
            <span><?php echo __('View All', 'picso');?></span>
            <span> (<?php echo $instance['total'];?>)</span>
        </a>
    </div>
    <?php } else { ?>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <span class="ps-text--muted"><?php echo __('No photos', 'picso');?></span>
    </div>
    <?php } ?>
</div>

<?php

echo $args['after_widget'];

// EOF
