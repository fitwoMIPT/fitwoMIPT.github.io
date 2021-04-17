<?php

class PeepSoGroupsShortcode
{
	const SHORTCODE	= 'peepso_groups';

	private static $_instance 	= NULL;
	public $url 				= NULL;
	public $group				= NULL;
	public $group_id 			= NULL;
	public $group_segment_id 	= NULL;

	private function __construct()
    {
		add_filter('peepso_page_title', array(&$this,'peepso_page_title'));
		add_action('peepso_group_segment_settings', array(&$this, 'filter_group_segment_settings'));
		add_action('peepso_group_segment_members', array(&$this, 'filter_group_segment_members'));
		add_filter('peepso_page_title_check', array(&$this, 'peepso_page_title_check'));
	}

	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}


	public function set_page( $url )
	{
		if(!$url instanceof PeepSoUrlSegments) {
            return;
        }

        add_action('wp_enqueue_scripts', array(PeepSoGroupsPlugin::get_instance(), 'enqueue_scripts'));
        $this->url = $url;

		$group_id = $url->get(1);
		// Attempting a single group view
		// A group ID can be numeric or a string (unique URL identifier)
		if( strlen($group_id) ) {

			// Assume error: not found / unpublished / otherwise not accessible
			$this->group_id = FALSE;

			$this->group = new PeepSoGroup($group_id);

			// Group found
			if( $this->group->id ) {
				$this->group_id = $this->group->id;
				$this->group_segment_id = $url->get(2);

				$group_user = new PeepSoGroupUser($this->group_id);

				// Can current user access this group?
				if($group_user->can('access')) {

					// unregister "groups" listing
					remove_shortcode(self::SHORTCODE);
					// replace with "group" view
					add_shortcode(self::SHORTCODE, array(self::get_instance(), 'shortcode_group'));
				} else {
					// if user doesn't have an access just display groups not found
					$this->group_id = FALSE;
				}
			}
		}
	}

    public function peepso_page_title( $title )
    {
        if(self::SHORTCODE == $title['title'] || $title['title'] == 'peepso_activity') {

			$title['newtitle'] = __('Groups', 'groupso');

			if( $this->group_id ) {
				$title['newtitle'] = $this->group->name;
			}
		}

        return $title;
    }


    public static function description() {
	    return __('Displays the Groups listing and single Group view.','groupso');
    }

    public static function post_state() {
        return _x('PeepSo', 'Page listing', 'groupso') . ' - ' . __('Groups', 'groupso');
    }

	/**
	 * Registers the callback function for the peepso_messages shortcode.
	 */
	public static function register_shortcodes()
	{
		add_shortcode(self::SHORTCODE, array(self::get_instance(), 'shortcode_groups'));
	}


	public function shortcode_group()
	{
		$group_user = new PeepSoGroupUser($this->group_id);
		// Can current user access a particular group segment?
		if(!$group_user->can('access_segment', $this->group_segment_id)) {

			// Show content unavailable when guest visit any type of group
			if(!get_current_user_id()) {
				$ret = PeepSoTemplate::get_before_markup();
				$ret .= PeepSoTemplate::do_404();
				$ret .= PeepSoTemplate::get_after_markup();

				return ($ret);
			}

			// Force activity screen redirect if the segment is inaccessible
			PeepSo::redirect($this->group->get('url'));
			exit();
		}

		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		$args = array(
			'group' => $this->group,
			'group_segment' => $this->group_segment_id
		);

		if(!isset($this->url) || !($this->url instanceof PeepSoUrlSegments)) {
            $this->url = PeepSoUrlSegments::get_instance();
        }

        // avatar dialog
        wp_enqueue_script('peepso-groups-avatar-dialog');

        add_action('peepso_activity_dialogs', array(&$this, 'upload_dialogs'));

		if(strlen($this->group_segment_id)) {
			ob_start();
			do_action('peepso_group_segment_'.$this->group_segment_id, $args, $this->url);
			$ret .= ob_get_clean();
		} else {

			// activity filters & hooks
			add_filter('peepso_activity_meta_query_args', array(&$this, 'activity_meta_query_args'), 10, 2);

			$ret .= PeepSoTemplate::exec_template('groups', 'group', $args,TRUE);
		}


		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

	public function activity_meta_query_args($args, $module_id) {
		if($module_id === PeepSoGroupsPlugin::MODULE_ID) {
			array_push($args['meta_query'],
				array(
					'compare' => '=',
					'key' => 'peepso_group_id',
					'value' => $this->group->id,
					)
				);
		}

		return $args;
	}

	public function filter_group_segment_settings($args)
	{
	    $PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
	    if($tab = $PeepSoUrlSegments->get(3)) {
            PeepSoTemplate::exec_template('groups', 'group-settings-'.$tab, $args);
            return;
        }

		echo PeepSoTemplate::exec_template('groups', 'group-settings', $args);
	}

	public function filter_group_segment_members($args)
	{
		wp_enqueue_script('peepso-page-group-members', plugin_dir_url(__FILE__) . '../../assets/js/page-group-members.js',
			array('peepso', 'peepso-page-autoload'), PeepSo::PLUGIN_VERSION, TRUE);

		$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();

		$tab = $PeepSoUrlSegments->get(3);

		if('pending' == $tab) {
			echo PeepSoTemplate::exec_template('groups', 'group-members-pending', $args);
			return;
		}

		if('invited' == $tab) {
			echo PeepSoTemplate::exec_template('groups', 'group-members-invited', $args);
			return;
		}

		if('banned' == $tab) {
			echo PeepSoTemplate::exec_template('groups', 'group-members-banned', $args);
			return;
		}

		if('management' == $tab) {
			echo PeepSoTemplate::exec_template('groups', 'group-members-management', $args);
			return;
		}

		echo PeepSoTemplate::exec_template('groups', 'group-members', $args);
	}

	public function shortcode_groups()
	{
		$allow_guest_access = PeepSo::get_option('groups_allow_guest_access_to_groups_listing', 0);

		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		// list / search groups
		$input = new PeepSoInput();
		$search = $input->value('search', NULL, FALSE); // SQL Safe
		$category = $input->int('category', 0);

		$num_results = 0;

		// special case - 404, group hidden, you've been banned etc
		if( FALSE === $this->group_id ) {
			$ret .= PeepSoTemplate::do_404();
		} else {
			$ret .= PeepSoTemplate::exec_template('groups', 'groups', array('search' => $search, 'num_results' => $num_results, 'category' => $category, 'allow_guest_access' => $allow_guest_access), TRUE);
		}

		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

    /**
     * callback - peepso_activity_dialogs
     * Renders the dialog boxes for uploading profile and cover photo.
     */
    public function upload_dialogs()
    {
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_style('peepso-fileupload');
        PeepSoTemplate::exec_template('groups', 'dialog-group-avatar', array('PeepSoGroup' => $this->group));
        PeepSoTemplate::exec_template('groups', 'dialog-group-cover', array('PeepSoGroup' => $this->group));
    }

	/**
	 * todo:docblock
	 */
	public function peepso_page_title_check($post) {
		$url = PeepSoUrlSegments::get_instance();
		$post_slug = $url->get(1);
		$group_id = '';

		if (!empty($post_slug)) {
			$args = array(
				'name'        => $post_slug,
				'post_type'   => PeepSoGroup::POST_TYPE,
				'post_status' => 'publish',
				'numberposts' => 1
			);

			$groups = get_posts($args);

			if (count($groups) == 1) {
				$this->group_id = $group_id = $groups[0]->ID;
				$this->group = new PeepSoGroup($group_id);
			}
		}

		if (((isset($post->post_content) && strpos($post->post_content, '[peepso_groups]') !== FALSE) && !empty($group_id)) && !is_front_page()) {
			return TRUE;
		}

		return $post;
	}

}

// EOF
