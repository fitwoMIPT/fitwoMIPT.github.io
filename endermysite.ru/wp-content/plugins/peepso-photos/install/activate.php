<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');
/*
 * Performs installation process
 * @package PeepSoPhotos
 * @author PeepSo
 */
class PeepSoPhotosInstall extends PeepSoInstall
{
    protected $default_config = array(
        'photos_allowed_user_space' => '100',
        'photos_max_upload_size' => '20',
        'photos_max_user_photo' => '0',
        'photos_daily_photo_upload_limit' => '0',
        'photos_behavior' => '1',
        'photos_max_image_width' => '4000',
		'photos_max_image_height' => '3000',
		'photos_quality_full' => '85',
		'photos_quality_thumb' => '60',
		'photos_max_image_height' => '3000',
        'photos_enable_aws_s3' => '0',
        );

	/*
	 * called on plugin activation; performs all installation tasks
	 */
	public function plugin_activation( $is_core = FALSE )
	{
		$activated = parent::plugin_activation($is_core);
		return ($activated);

	}

	public static function get_table_data()
	{
		$aRet = array(
			'photos' => "
				CREATE TABLE photos (
					pho_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					pho_album_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
					pho_owner_id BIGINT(20) UNSIGNED NOT NULL,
					pho_post_id BIGINT(20) UNSIGNED NOT NULL,
					pho_acc TINYINT(1) UNSIGNED DEFAULT 0,
					pho_stored TINYINT(1) UNSIGNED DEFAULT 0,
					pho_file_name VARCHAR(100),
					pho_orig_name VARCHAR(100),
					pho_filesystem_name VARCHAR(100),
					pho_size INT(11) UNSIGNED,
					pho_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					pho_token VARCHAR(200) NULL,
					pho_thumbs TEXT,
					pho_ext VARCHAR(10),
					pho_module_id INT(11) UNSIGNED DEFAULT 0,
					PRIMARY KEY (pho_id),
					INDEX album_id (pho_album_id),
					INDEX post_id (pho_post_id),
					INDEX owner (pho_owner_id)
				) ENGINE=InnoDB",
			'photos_album' => "
				CREATE TABLE photos_album (
					pho_album_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					pho_owner_id BIGINT(20) UNSIGNED NOT NULL,
					pho_post_id BIGINT(20) UNSIGNED NOT NULL,
					pho_album_acc TINYINT(1) UNSIGNED DEFAULT 0,
					pho_album_name VARCHAR(100),
					pho_album_desc TEXT,
					pho_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					pho_system_album TINYINT(1) UNSIGNED DEFAULT 0,
					pho_module_id INT(11) UNSIGNED DEFAULT 0,
					PRIMARY KEY (pho_album_id),
					INDEX owner (pho_owner_id),
					INDEX post_id (pho_post_id)
				) ENGINE=InnoDB",
		);

		return $aRet;
	}

	/*
	 * return default email templates
	 */
	public function get_email_contents()
	{
		$emails = array(
			'email_like_avatar' => "Hello {userfirstname},

{fromfirstname} likes your profile avatar!

You can see all of your notifications here:
{permalink}

Thank you.",
			'email_like_cover' => "Hello {userfirstname},

{fromfirstname} likes your cover photo!

You can see all of your notifications here:
{permalink}

Thank you.",
			'email_like_album' => "Hello {userfirstname},

{fromfirstname} likes your photo album!

You can see all of your notifications here:
{permalink}

Thank you.",
			'email_like_photo' => "Hello {userfirstname},

{fromfirstname} likes your photo!

You can see all of your notifications here:
{permalink}

Thank you.",
			'email_user_comment_avatar' => "Hello {userfirstname},

{fromfirstname} had something to say about your avatar!

You can see the avatar here:
{permalink}

Thank you.",
			'email_user_comment_cover' => "Hello {userfirstname},

{fromfirstname} had something to say about your cover photo!

You can see the cover photo here:
{permalink}

Thank you.",
			'email_user_comment_album' => "Hello {userfirstname},

{fromfirstname} had something to say about your photo album!

You can see the photo album here:
{permalink}

Thank you.",
			'email_user_comment_photo' => "Hello {userfirstname},

{fromfirstname} had something to say about your photo!

You can see the photo here:
{permalink}

Thank you.",
			'email_share_avatar' => "Hello {userfirstname},

{fromfirstname} had shared your avatar!

You can see the post here:
{permalink}

Thank you.",
			'email_share_cover' => "Hello {userfirstname},

{fromfirstname} had shared your cover photo!

You can see the post here:
{permalink}

Thank you.",
			'email_share_album' => "Hello {userfirstname},

{fromfirstname} had shared your album!

You can see the post here:
{permalink}

Thank you.",
			'email_share_photo' => "Hello {userfirstname},

{fromfirstname} had shared your photo!

You can see the post here:
{permalink}

Thank you.");
		
		return ($emails);
	}
}
