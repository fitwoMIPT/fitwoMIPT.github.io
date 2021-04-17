<?php
    echo $args['before_widget'];
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <?php
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
        ?>
    </div>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <div class="ps-widget--photos">
        <?php
            if(count($instance['list']))
            {
        ?>
            <div class="ps-widget__photos">
            <?php
                foreach ($instance['list'] as $photo)
                {
                    PeepSoTemplate::exec_template('photos', 'photo-item-widget', (array)$photo);
                }
            ?>
            </div>
        <?php
            }
            else
            {
                echo "<span class='ps-text--muted'>".__('No photos', 'picso')."</span>";
            }
        ?>
        </div>
    </div>
</div>

<?php

echo $args['after_widget'];

// EOF
