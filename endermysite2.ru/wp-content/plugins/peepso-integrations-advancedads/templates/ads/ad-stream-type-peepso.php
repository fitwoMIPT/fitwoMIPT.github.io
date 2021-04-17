<?php

$target="target=_\"blank\"";

$image_url = NULL;

if($image = wp_get_attachment_image_src( $ad->output['image_id'], 'full' )) {
    $image_url = $image[0];
}

$avatar_url = "";

if($avatar = wp_get_attachment_image_src( $ad->output['avatar_id'], 'full' )) {
    $avatar_url = $avatar[0];
}

$url =	    ( isset( $ad->url ) ) ? esc_url( $ad->url ) : '#';
?>
<div class="ps-stream ps-stream--advads ps-js-activity">

    <div class="ps-stream-header">

        <?php if($avatar_url) { ?>
        <div class="pa-avatar ps-avatar-stream">
            <a <?php echo $target;?>  href="<?php echo $url;?>">
                <img src="<?php echo $avatar_url;?>" />
            </a>
        </div>
        <?php } ?>


        <div class="ps-stream-meta">
            <div class="reset-gap">
                <a <?php echo $target;?> class="ps-stream-user" href="<?php echo $url;?>">
                    <?php echo $ad->title;?>
                </a>
            </div>
            <small class="ps-stream-time" >
                <?php if(PeepSo::get_option('advancedads_stream_sponsored_mark', 0)) {
                    echo '<p><small>' . PeepSo::get_option('advancedads_stream_sponsored_text') . '</small></p>';
                }
                ?>
            </small>
        </div>
    </div>

    <div class="ps-stream-body">

           <?php echo wpautop(strip_tags($ad->content, PeepSoAdvancedAdsAdTypePeepSo::$allowed_html));?>
        

        <?php if($image_url) { ?>
        <a <?php echo $target;?> class="ps-advads__image" href="<?php echo $url;?>">
            <img src="<?php echo $image_url;?>" />
        </a>
        <?php } ?>
    </div>
</div>
