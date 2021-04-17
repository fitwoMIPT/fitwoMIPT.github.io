import $ from 'jquery';
import _ from 'underscore';
import { browser, observer } from 'peepso';
import { supportsType } from './video';

const SUPPORT_WEBM = supportsType('webm') === 'probably';

/**
 * Initialize video thumbnail on activity stream.
 *
 * @param {jQuery} $video
 */
const initVideo = $video => {
	let $thumbnail = $video.find('.ps-video-thumbnail'),
		$play = $thumbnail.find('.ps-video-play'),
		$img = $thumbnail.find('img'),
		previewStill = $img.attr('src'),
		previewGif = $img.data('animated'),
		previewWebm = $img.data('animated-webm'),
		$preview;

	$thumbnail
		.on('mouseenter', () => {
			if (previewWebm && SUPPORT_WEBM) {
				if ($preview) {
					$img.hide();
					$preview.show();
					$preview.get(0).play();
				} else {
					$preview = $(`<video src="${previewWebm}" />`);
					$preview.get(0).addEventListener('loadeddata', () => {
						$img.hide();
						$preview.insertAfter($img);
						$preview.show();
						$preview.get(0).play();
					});
				}
			} else if (previewGif) {
				$img.attr('src', previewGif);
			}
		})
		.on('mouseleave', () => {
			if (previewWebm && SUPPORT_WEBM) {
				if ($preview) {
					$preview.hide();
					$preview.get(0).pause();
					$img.show();
				}
			} else if (previewGif) {
				$img.attr('src', previewStill);
			}
		});

	// Handle play button.
	$play.on('click', () => {
		let content = $video.children('script').text();
		$video.html(content);
	});
};

// Initialize video on every activity added to the stream.
$(document).on(
	'ps_activitystream_loaded',
	_.throttle(() => {
		let $activities = $('.ps-js-activity').not('[data-video-init]');
		$activities.each(function() {
			let $activity = $(this).attr('data-video-init', ''),
				$video = $activity.find('.ps-js-video');

			if ($video.length) {
				initVideo($video);
			}
		});
	}, 3000)
);

// Fix audio unplayable issue on iOS and Safari.
let ua = navigator.userAgent;
let isSafari = ua.indexOf('Safari') > -1 && ua.indexOf('Chrome') === -1;
if (browser.isIOS() || isSafari) {
	observer.addFilter(
		'peepso_activity',
		function($posts) {
			return $posts.each(function() {
				let $post = $(this),
					$audio = $post.find('audio.wp-audio-shortcode');

				if ($audio.length) {
					let $source = $audio.find('source');
					if ($source.length) {
						$audio.attr('src', $source.attr('src'));
						$source.remove();
					}
				}
			});
		},
		10,
		1
	);
}
