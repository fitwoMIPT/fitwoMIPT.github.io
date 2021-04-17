<?php

$PeepSoSharePhotos = PeepSoSharePhotos::get_instance();
$photo_thumb = isset( $photo_thumbs['m'] ) ? $photo_thumbs['m'] : $photo_thumbs['m_s'];
$is_gif = $PeepSoSharePhotos->is_gif_file( $photo_url );

$alt= '';
$preview = '';
global $post;
if($post instanceof WP_Post) {
    $PeepSoUser = PeepSoUser::get_instance($post->post_author);
    $alt = sprintf(__('%s uploaded a photo','picso'), $PeepSoUser->get_fullname());
    $preview = __('Uploaded a photo','picso');
}
?>
<a class="ps-media-photo <?php if ($is_gif) { echo 'ps-media--gif'; } ?> ps-js-photo" data-id="<?php echo $act_id; ?>" href="<?php echo $photo_url; ?>" onclick="<?php echo $onclick; ?>" rel="post-<?php echo $act_id; ?>">
	<img src="" data-src="<?php echo $photo_thumb; ?>" title="<?php echo $title; ?>" alt="<?php echo $alt;?>" data-preview="<?php echo $preview;?>"/>
	<?php
	if ($is_gif) {
		echo '<div class="ps-media__indicator"><span>' , __('Gif', 'picso') , '</span></div>';
	}
	?>
</a>
