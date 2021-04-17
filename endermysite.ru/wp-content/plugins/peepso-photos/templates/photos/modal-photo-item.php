<?php
$PeepSoSharePhotos = PeepSoSharePhotos::get_instance();
$is_gif = $PeepSoSharePhotos->is_gif_file($location);
?>
<img class="<?php echo $is_gif ? 'ps-js-photo-gif' : ''; ?>" src="<?php echo $location; ?>" alt="" />
<?php if ($is_gif) { ?>
<div class="ps-icon-play ps-lightbox-play ps-js-btn-gif" style="display:block"></div>
<?php } ?>

<?php if (intval($pho_owner_id) === get_current_user_id()) {
	$params = array();
	$set_avatar_onclick = apply_filters('peepso_photos_set_as_avatar', 'peepso.photos.set_as_avatar();', $pho_id, $params);
	$set_cover_onclick = apply_filters('peepso_photos_set_as_cover', 'peepso.photos.set_as_cover();', $pho_id, $params);
	?>
	<div class="ps-lightbox-toolbar--options">
		<div class="ps-dropdown ps-js-dropdown" id="picso-photo-setting">
			<a class="ps-dropdown__toggle ps-js-dropdown-toggle" data-value="">
				<span class="ps-icon-cog"></span> <?php echo __('Options', 'picso'); ?>
			</a>
			<div class="ps-dropdown__menu ps-js-dropdown-menu">
				<a href="#" onclick="<?php echo $set_avatar_onclick;?>; return false;"><?php echo __('Set as avatar', 'picso'); ?></a>
				<a href="#" onclick="<?php echo $set_cover_onclick;?>; return false;"><?php echo __('Set as cover', 'picso'); ?></a>
			</div>
		</div>
	</div>

	<input type="hidden" name="photoid_tobe_photo_profile" id="photoid_tobe_photo_profile" value="<?php echo $pho_id ?>" />
	<?php
	wp_nonce_field('profile-set-photo-profile', '_photoprofilenonce');
	wp_nonce_field('photo-delete-album', '_delete_album_nonce');
}
?>

