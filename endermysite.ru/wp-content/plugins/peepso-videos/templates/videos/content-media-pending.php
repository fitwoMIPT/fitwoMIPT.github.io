<?php
if(!isset($oembed_type) || (isset($oembed_type) && 'video' === $oembed_type) ) {

	$activity = new PeepSoActivity();
    $postvideo = $activity->get_post($vid_post_id);
    $url = PeepSo::get_page('activity');
    if (!empty($postvideo->post)) {
	    $url = PeepSo::get_page('activity_status') . $postvideo->post->post_title;
    }
?>
<div class="ps-media-video">
	<div class="ps-media-body ps-media--uploading video-description">
		<?php if ($vid_conversion_status == PeepSoVideosUpload::STATUS_PENDING) { ?>
			<?php if ($vid_upload_s3_status == PeepSoVideosUpload::STATUS_S3_WAITING) { ?>
				<i class="ps-icon-magic"></i> <?php echo __('Video is being uploaded to S3. It should be converted in a few minutes.', 'vidso'); ?>
			<?php } else { ?>
				<i class="ps-icon-magic"></i> <?php echo __('Video is being converted. It should be available in a few minutes.', 'vidso'); ?>
			<?php } ?>
		<?php } elseif ($vid_conversion_status == PeepSoVideosUpload::STATUS_PROCESSING) { ?>
			<i class="ps-icon-ok"></i> <?php echo __('It has now converted.'); ?>
		<?php } elseif ($vid_conversion_status == PeepSoVideosUpload::STATUS_FAILED) { ?>
			<?php echo __('Video failed to convert.', 'vidso'); ?>
		<?php } ?>
	</div>
</div>
<?php }
