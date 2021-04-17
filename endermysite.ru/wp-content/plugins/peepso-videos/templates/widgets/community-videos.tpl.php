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
        <div class="ps-widget--videos">
        <?php
            if(count($instance['list']))
            {
        ?>
            <div class="ps-widget__videos">
            <?php
                foreach ($instance['list'] as $video)
                {
                    $video = (array) $video;
                    echo PeepSoTemplate::exec_template('videos', 'video-item-widget', $video);
                }
            ?>
            </div>
        <?php
            }
            else
            {
                if ($instance['media_type'] == 'all') {
                    echo "<span class='ps-text--muted'>".__('No media', 'vidso')."</span>";
                } else {
                    if($instance['media_type'] == 'audio') {
                        echo "<span class='ps-text--muted'>".__('No audio', 'vidso')."</span>";
                    } elseif ($instance['media_type'] == 'video') {
                        echo "<span class='ps-text--muted'>".__('No video', 'vidso')."</span>";
                    } 
                }
            }
        ?>
        </div>
    </div>
</div>

<?php

echo $args['after_widget'];

// EOF
