<label id="advanced-ads-selling-setup-ad-details-peepso-avatar-label" class="advanced-ads-selling-setup-ad-details-content" for="advanced-ads-selling-setup-ad-details-peepso-avatar-input"><?php echo __( 'Avatar', 'peepso-advanced-ads' ); ?></label>
<div id="advanced-ads-selling-setup-ad-details-peepso-avatar-upload" class="advanced-ads-selling-setup-ad-details-content">
    <input id="advanced-ads-selling-setup-ad-details-peepso-avatar-input" type="file" name="advads_selling_ad_avatar"/>
    <span class="advanced-ads-selling-dile-upload-instrct"><?php echo __( 'Max File Size : 1Mb' ); ?></span>
</div>


<label id="advanced-ads-selling-setup-ad-details-peepso-image-label" class="advanced-ads-selling-setup-ad-details-content" for="advanced-ads-selling-setup-ad-details-peepso-image-input"><?php echo __( 'Image', 'peepso-advanced-ads' ); ?></label>
<div id="advanced-ads-selling-setup-ad-details-peepso-image-upload" class="advanced-ads-selling-setup-ad-details-content">
    <input id="advanced-ads-selling-setup-ad-details-peepso-image-input" type="file" name="advads_selling_ad_image"/>
    <span class="advanced-ads-selling-dile-upload-instrct"><?php echo __( 'Max File Size : 1Mb' ); ?></span>
</div>

<label id="advanced-ads-selling-setup-ad-details-peepso-url-label" class="advanced-ads-selling-setup-ad-details-content" for="advanced-ads-selling-setup-ad-details-peepso-url-input"><?php echo __( 'URL', 'peepso-advanced-ads' ); ?></label>
<input id="advanced-ads-selling-setup-ad-details-peepso-url-input" class="advanced-ads-selling-setup-ad-details-content" type="url" name="advads_selling_ad_url"/>


<script type="text/javascript">
    jQuery( document ).ready(function( $ ) {

        function peepso_advads_selling_toggle_details_section() {

            // get active sections
            var active = $('.advanced-ads-selling-setup-ad-type:checked').val();

            // choose active sections
            if ('peepso' === active) {
                $('#advanced-ads-selling-setup-ad-details-upload-label, #advanced-ads-selling-setup-ad-details-image-upload, #advanced-ads-selling-setup-ad-details-url, #advanced-ads-selling-setup-ad-details-url-input').hide();

                $('#advanced-ads-selling-setup-ad-details-peepso-avatar-label').show();
                $('#advanced-ads-selling-setup-ad-details-peepso-avatar-upload').show();

                $('#advanced-ads-selling-setup-ad-details-peepso-image-label').show();
                $('#advanced-ads-selling-setup-ad-details-peepso-image-upload').show();

                $('#advanced-ads-selling-setup-ad-details-peepso-url-label').show();
                $('#advanced-ads-selling-setup-ad-details-peepso-url-input').show();

            } else {

                $('#advanced-ads-selling-setup-ad-details-peepso-avatar-label').hide();
                $('#advanced-ads-selling-setup-ad-details-peepso-avatar-upload').hide();

                $('#advanced-ads-selling-setup-ad-details-peepso-image-label').hide();
                $('#advanced-ads-selling-setup-ad-details-peepso-image-upload').hide();

                $('#advanced-ads-selling-setup-ad-details-peepso-url-label').hide();
                $('#advanced-ads-selling-setup-ad-details-peepso-url-input').hide();
            }
        }

        peepso_advads_selling_toggle_details_section();

        // trigger, when selection is changed
        $('.advanced-ads-selling-setup-ad-type').click(peepso_advads_selling_toggle_details_section);
    });
</script>