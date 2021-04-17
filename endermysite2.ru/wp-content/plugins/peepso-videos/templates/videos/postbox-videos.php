<div class="ps-videos__postbox ps-postbox-videos">
	<div class="ps-postbox-input ps-inputbox">

		<!-- Video embed -->
		<div class="ps-videos__postbox-embed ps-js-video-embed">
			<input class="ps-textarea ps-videos-url input ps-js-url"
				placeholder="<?php echo __('Enter video URL here', 'vidso'); ?>" />
			<div class="ps-postbox-loading ps-js-loading" style="display:none">
				<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>">
			</div>
		</div>

		<?php if (PeepSo::get_option('videos_upload_enable', 0) === 1) { ?>
		<!-- Separator -->
		<div class="ps-postbox__separator ps-js-video-separator">
			<span>
				<?php echo __('or', 'vidso'); ?>
			</span>
		</div>

		<!-- Video upload -->
		<div class="ps-videos__postbox-upload ps-js-video-upload">

			<!-- Video upload button -->
			<div class="ps-videos__postbox-action ps-js-btn">
				<i class="ps-icon-upload"></i>
				<strong><?php echo __('Upload', 'vidso'); ?></strong>

				<?php if ( isset($video_size) ) { ?>
					<span><?php echo sprintf( __('Max file size: %1$sMB', 'vidso'), $video_size['max_size'] ); ?></span>
				<?php } ?>
			</div>

			<div class="ps-videos__postbox-file">
				<input type="file" name="filedata[]" class="ps-js-file"
					accept=".avi,.flv,.mkv,.mov,.mp4,.mpeg,.mpg,.swf,.webm,.wmv" style="display:none" />
			</div>

			<!-- Video upload form -->
			<div class="ps-videos__postbox-form ps-js-form">
				<i class="ps-icon-upload"></i>
				<div class="ps-videos__postbox-progress-wrapper ps-js-progress">
					<div class="ps-videos__postbox-progress">
						<div class="ps-videos__postbox-progressbar"></div>
					</div>
					<div class="ps-videos__postbox-percentage ps-js-percent"></div>
				</div>
				<div class="ps-videos__postbox-progress-done ps-js-done">
					<span class="ps-icon-ok">
						<?php echo __( 'Done', 'vidso' ); ?>
					</span>
				</div>
				<div class="ps-videos__postbox-progress-done ps-js-failed">
					<span class="ps-text--danger">
						<span class="ps-icon-cancel">
							<?php echo __( 'Upload failed: ', 'vidso' ); ?>
							<span class="ps-js-failed-message"></span>
						</span>
					</span>
				</div>
				<input class="ps-textarea ps-js-title"
					placeholder="<?php echo __('Enter the title...', 'vidso'); ?>" />
			</div>

			<!-- Video upload success notice -->
			<div class="ps-videos__postbox-notice ps-js-success">
				<i class="ps-icon-upload"></i>
				<?php

					$notice = __( "Your video was uploaded successfully!\nIt's been added to the queue, we will notify you when your video is published.", 'vidso' );
					echo nl2br( $notice );

				?>
			</div>

		</div>

		<?php } ?>

		<!-- Video preview -->
		<div class="ps-videos__postbox-preview ps-js-video-preview"></div>
	</div>
</div>
