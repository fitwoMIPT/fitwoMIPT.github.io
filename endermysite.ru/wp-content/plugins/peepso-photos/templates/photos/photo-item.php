<?php

$PeepSoSharePhotos = PeepSoSharePhotos::get_instance();
$PeepSoActivity = PeepSoActivity::get_instance();
$link = $PeepSoActivity->post_link(FALSE);
$is_gif = $PeepSoSharePhotos->is_gif_file($location);

?>
<a href="<?php echo $link; ?>" class="ps-media-photo ps-media-grid-item <?php $is_gif ? 'ps-media--gif' : ''; ?>"
		data-ps-grid-item onclick="<?php echo $onclick; ?>" style="display:none">
	<div class="ps-media-grid-padding">
		<div class="ps-media-grid-fitwidth">
			<img src="<?php echo $location; ?>" />
			<?php if ($is_gif) { ?>
			<div class="ps-media__indicator"><span><?php echo __('Gif', 'picso'); ?></span></div>
			<?php } ?>
			<?php if (isset($has_extra_photos) && $has_extra_photos > 1) { ?>
			<div class="ps-media-photo-counter" style="top:0; left:0; right:0; bottom:0;">
				<span>+<?php echo $has_extra_photos ?></span>
			</div>
			<?php } ?>
		</div>
	</div>
</a>
