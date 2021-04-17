<?php
$PeepSoPhotos = PeepSoPhotos::get_instance();
?>
<div class="cstream-attachment photo-attachment">
	<div class="ps-media-photos photo-container photo-container-placeholder ps-clearfix ps-js-photos">
		<?php $PeepSoPhotos->show_photo_comments($photo); ?>
		<div class="ps-media-loading ps-js-loading">
			<div class="ps-spinner">
				<div class="ps-spinner-bounce1"></div>
				<div class="ps-spinner-bounce2"></div>
				<div class="ps-spinner-bounce3"></div>
			</div>
		</div>
	</div>
</div>
