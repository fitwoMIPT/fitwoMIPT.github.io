<?php

class PeepSoGroupPrivacy
{
	public $settings = array();

	const PRIVACY_OPEN 		= 0;
	const PRIVACY_CLOSED 	= 1;
	const PRIVACY_SECRET 	= 2;

	public static function _($privacy = NULL)
	{
		$settings = array(

			self::PRIVACY_OPEN => array(
				'id' 	=> self::PRIVACY_OPEN,
				'icon'	=> 'ps-icon-globe',
                'name'	=> __('Open', 'groupso'),
                'notif'	=> __('open', 'groupso'),
				'desc'	=> __('Anyone can join this group and participate.', 'groupso') . PHP_EOL . PHP_EOL . __('Non-members can see everything in the group, but they can\'t post, comment etc.','groupso'),
			),

            self::PRIVACY_CLOSED => array(
                'id'	=> self::PRIVACY_CLOSED,
                'icon'	=> 'ps-icon-lock',
                'name'	=> __('Closed', 'groupso'),
                'notif'	=> __('closed', 'groupso'),
                'desc'	=> __('Users need to be invited or request group membership and be accepted.', 'groupso') . PHP_EOL . PHP_EOL . htmlspecialchars(__('Non-members can only see the group\'s "about" page.','groupso')),
            ),
            self::PRIVACY_SECRET=> array(
                'id'	=> self::PRIVACY_SECRET,
                'icon'	=> 'ps-icon-shield',
                'name'	=> __('Secret', 'groupso'),
                'notif' => __('secret', 'groupso'),
                'desc'	=> __('Users need to be invited.','groupso') . PHP_EOL . PHP_EOL .  __('Non-members can\'t see the group at all.', 'groupso'),
            ),
		);

		// Return a single privacy setting if requested
		if(NULL !== $privacy) {
			return $settings[$privacy];
		}

		// Otherwise return everything
		return $settings;
	}

    /**
     * Displays the privacy options in an unordered list.
     * @param string $callback Javascript callback
     */
    public static function render_dropdown($callback = '')
    {
        ob_start();

        echo '<div class="ps-dropdown__menu ps-js-dropdown-menu">';

        $options = self::_();

        foreach ($options as $key => $option) {
            printf('<a href="#" class="ps-dropdown__group" data-option-value="%d" onclick="%s; return false;">%s</a>',
                $key, $callback, '<div class="ps-dropdown__group-title"><i class="' . $option['icon'] . '"></i><span>' . $option['name'] . '</span></div><div class="ps-dropdown__group-desc">' . nl2br($option['desc']) .'</div>'
            );
        }
        echo '</div>';

        return ob_get_clean();
    }
}
