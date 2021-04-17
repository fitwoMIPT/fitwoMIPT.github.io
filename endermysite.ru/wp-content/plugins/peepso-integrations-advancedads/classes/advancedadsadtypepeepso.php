<?php

class PeepSoAdvancedAdsAdTypePeepSo extends Advanced_Ads_Ad_Type_Abstract
{
    public $ID = 'peepso';

    public static $allowed_html = '<a> <b> <hr> <i> <u>';

    public function __construct() {
        $this->title = __( 'PeepSo Stream Ad', 'peepso-advanced-ads' );
        $this->description = __( 'Ads that display in PeepSo stream.', 'peepso-advanced-ads' );

        $this->parameters = array(
            'content' => '',
            'avatar_id' => '',
            'image_id' => '',
        );
    }

    public function render_parameters($ad){
        $content =  isset($ad->content) ? $ad->content : '';
        $image_id = isset($ad->output['image_id']) ? $ad->output['image_id'] : '';
        $avatar_id = isset($ad->output['avatar_id']) ? $ad->output['avatar_id']: '';
        $url =	    ( isset( $ad->url ) ) ? esc_url( $ad->url ) : '';
        ?>
        <script type="text/javascript">
            jQuery(function( $ ) {
                $('#advanced-ads-ad-parameters-size').prev('span').hide();

            });
        </script>
        <style type="text/css">

            #advads-image-preview img, #advads-avatar-preview img{
                padding:2px;
                border:solid 1px #aaaaaa;
            }

            #advads-image-preview {
                width:500px;
            }

            #advads-avatar-preview img {
                height: auto;
                max-width: 128px;
                max-heigth:128px;
                width: 100%;
            }

            #advanced-ads-ad-parameters-size {
                display:none;
            }

            .description {
                color:#aaaaaa;
            }


        </style>


        <!-- Avatar -->
        <h1>
            <?php echo __('Avatar', 'peepso-advanced-ads');?>
            <button href="#" class="advads_avatar_upload button button-secondary" type="button" data-uploader-title="<?php
            echo __( 'Insert File', 'peepso-advanced-ads' ); ?>" data-uploader-button-text="<?php echo __( 'Insert', 'peepso-advanced-ads' ); ?>" onclick="return false;">
                <?php echo __( 'Change', 'peepso-advanced-ads' ); ?>
            </button>
        </h1>

        <div class="description">
            <?php
            echo __('The uploaded image should be square and at least 128x128 pixels.', 'peepso-advanced-ads');
            ?>
        </div>

        <div id="advads-avatar-preview">
            <?php echo $this->image_tag( $avatar_id ); ?>
        </div>

        <input type="hidden" name="advanced_ad[output][avatar_id]" value="<?php echo $avatar_id; ?>" id="advads-avatar-id"/>

        <br class="clear" />

        <hr>

        <!-- Content -->
        <h1>
            <?php echo __('Content', 'peepso-advanced-ads');?>
        </h1>
        <div class="description">
            <?php echo __('Supported HTML tags:','peepso-advanced-ads') . ' <pre style="display:inline-block;margin:0;">' . htmlspecialchars(PeepSoAdvancedAdsAdTypePeepSo::$allowed_html) . '</pre>';?>
        </div>

        <textarea id="advads-content-plain" cols="100" rows="10" name="advanced_ad[content]"><?php echo esc_textarea( $content ); ?></textarea>

        <br class="clear" />

        <hr>

        <!-- Image  -->
        <h1>
            <?php echo __('Image', 'peepso-advanced-ads');?>
            <button href="#" class="advads_image_upload button button-secondary" type="button" data-uploader-title="<?php
            echo __( 'Insert File', 'peepso-advanced-ads' ); ?>" data-uploader-button-text="<?php echo __( 'Insert', 'peepso-advanced-ads' ); ?>" onclick="return false;">
                <?php echo __( 'Change', 'peepso-advanced-ads' ); ?>
            </button>
        </h1>

        <div class="description">
            <?php echo __('Image will be displayed at full width of your Community stream (depending on theme, layout and screen size). For best results use an image at least 1000 pixels wide.', 'peepso-advanced-ads');?>
        </div>

        <div id="advads-image-preview">
            <?php echo $this->image_tag( $image_id); ?>
        </div>

        <input type="hidden" name="advanced_ad[output][image_id]" value="<?php echo $image_id; ?>" id="advads-image-id"/>

        <br class="clear" />

        <hr>

        <h1>
            <?php echo __( 'URL', 'peepso-advanced-ads' ); ?>
        </h1>
        <div class="description">
            <?php echo __('Clicking the image, avatar or title will open the link in a new window/tab. ', 'peepso-advanced-ads');?>
        </div>
        <input type="url" name="advanced_ad[url]" id="advads-url" value="<?php echo $url; ?>"/>

        <br class="clear" />

        <?php
    }

    public function prepare_output($ad){
        return PeepSoTemplate::exec_template('ads','ad-stream-type-peepso', array('ad'=>$ad), TRUE);
    }

    public function image_tag( $attachment_id ){

        $image = wp_get_attachment_image_src( $attachment_id, 'full' );
        if ( $image ) {
            return "<img src=\"{$image[0]}\" />";
        }

        return "<br/><span style=\"opacity:0.5\">(". __('No image', 'peepso-advanced-ads') .')</span>';
    }
}
