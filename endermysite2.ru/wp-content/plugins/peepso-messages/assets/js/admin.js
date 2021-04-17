jQuery(function($) {
	var $mode = $('select[name=messages_chat_restriction_mode]'),
		$disablePages = $('textarea[name=messages_chat_disable_on_pages]'),
		$enablePages = $('textarea[name=messages_chat_enable_on_pages]');

	// Toggle video configs.
	$mode.on('change', function() {
		var mode = +this.value,
			modeEnable = 1;

		if (mode === modeEnable) {
			$disablePages.closest('.form-group').hide();
			$enablePages.closest('.form-group').show();
		} else {
			$enablePages.closest('.form-group').hide();
			$disablePages.closest('.form-group').show();
		}
	});

	$mode.triggerHandler('change');
});
