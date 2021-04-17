<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoPollsInstall extends PeepSoInstall
{

	protected $default_config = array(
		'polls_multiselect' => 1
	);
		
	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);
		return (TRUE);
	}

	// optional DB table creation
	public static function get_table_data()
	{
		$aRet = array(
			'polls_user_answers' => "
				CREATE TABLE polls_user_answers (
					pu_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					pu_poll_id BIGINT(20) UNSIGNED NULL DEFAULT '0',
					pu_user_id BIGINT(20) UNSIGNED NULL DEFAULT '0',
					pu_value TEXT NOT NULL,
					PRIMARY KEY (pu_id),
					INDEX pu_poll_id (pu_poll_id),
					INDEX pu_user_id (pu_user_id)
				) ENGINE=InnoDB",
		);

		return $aRet;
	}


}