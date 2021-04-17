<?php
/**
 * Plugin Name: PeepSo Core: Polls
 * Plugin URI: https://peepso.com
 * Description: Post a question and let users vote
 * Author: PeepSo
 * Author URI: https://peepso.com
 * Version: 2.8.0.0
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-polls
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */


class PeepSoPolls
{
	private static $_instance = NULL;

	const PLUGIN_NAME	 = 'Core: Polls';
	const PLUGIN_VERSION = '2.8.0.0';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
	const MODULE_ID = 30;
	const PLUGIN_EDD = 102823;
	const PLUGIN_SLUG = 'polls';

	const ICON = 'https://www.peepso.com/wp-content/plugins/peepso.com-checkout/assets/icons/polls_icon.svg';

    private static function ready() {
        if(class_exists('PeepSo')) {
            $plugin_version = explode('.', self::PLUGIN_VERSION);
            $peepso_version = explode('.', PeepSo::PLUGIN_VERSION);

            if(4==count($plugin_version)) {
                array_pop($plugin_version);
            }

            if(4==count($peepso_version)) {
                array_pop($peepso_version);
            }

            $plugin_version = implode('.', $plugin_version);
            $peepso_version = implode('.', $peepso_version);

            return($peepso_version == $plugin_version);
        }

        return FALSE;
    }


    private function __construct()
	{
        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_license_config', function($list){
                $data = array(
                    'plugin_slug' => self::PLUGIN_SLUG,
                    'plugin_name' => self::PLUGIN_NAME,
                    'plugin_edd' => self::PLUGIN_EDD,
                    'plugin_version' => self::PLUGIN_VERSION
                );
                $list[] = $data;
                return ($list);
            });
        }

        // Compatibility
        add_filter('peepso_all_plugins', array($this, 'filter_all_plugins'));

		// Translations
		add_action('plugins_loaded', function(){
            $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
            load_plugin_textdomain('peepso-polls', FALSE, $path);
        });

        // Activation
        register_activation_hook(__FILE__, array(&$this, 'activate'));

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            add_action('peepso_init', array(&$this, 'init'));
        }


	}

	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}


	public function init()
	{
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'peepso_check'));
			add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
		} else {

			if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
			}

			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

			// postbox
			add_filter('peepso_post_types', array(&$this, 'post_types'), 30, 2);
			add_filter('peepso_postbox_tabs', array(&$this, 'postbox_tabs'), 120);
			add_filter('peepso_postbox_interactions', array(&$this, 'postbox_interactions'), 110, 2);
			add_filter('peepso_permissions_polls_upload', array(&$this, 'permissions_polls_upload'));

			// save additional data
			add_filter('peepso_activity_insert_data', array(&$this, 'activity_insert_data'));
            add_action('peepso_activity_after_add_post', array(&$this, 'after_add_post'));
            add_action('peepso_activity_after_save_post', array(&$this, 'after_add_post'), 10, 1);

			// attach poll to post
			add_action('peepso_activity_post_attachment', array(&$this, 'attach_poll'), 30, 1);

			// disable repost
			add_filter('peepso_activity_post_actions', array(&$this, 'activity_post_actions'), 100);

			// stream title
			add_filter('peepso_activity_stream_action', array(&$this, 'activity_stream_action'), 10, 2);

			// post actions filter
			add_filter('peepso_post_filters', array(&$this, 'post_filters'), 20,1);
		}

		// Compare last version stored in transient with current version
		if( $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE != get_transient($trans = 'peepso_'.$this::PLUGIN_SLUG.'_version')) {
			set_transient($trans, $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE);
			$this->activate();
		}
	}

    /**
     * Adds the license key information to the config metabox
     * @param array $list The list of license key config items
     * @return array The modified list of license key items
     */
    public function add_license_info($list)
    {
        $data = array(
            'plugin_slug' => self::PLUGIN_SLUG,
            'plugin_name' => self::PLUGIN_NAME,
            'plugin_edd' => self::PLUGIN_EDD,
            'plugin_version' => self::PLUGIN_VERSION
        );
        $list[] = $data;
        return ($list);
    }

    public function license_notice()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);
    }

    public function license_notice_forced()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);
    }

    /**
     * Check if PeepSo class is present (ie the PeepSo plugin is installed and activated)
     * If there is no PeepSo, immediately disable the plugin and display a warning
     * Run license and new version checks against PeepSo.com
     * @return bool
     */
    public function peepso_check()
    {
        if (!class_exists('PeepSo')) {
            add_action('admin_notices', array(&$this, 'peepso_disabled_notice'));
            unset($_GET['activate']);
            deactivate_plugins(plugin_basename(__FILE__));
            return (FALSE);
        }

        // PeepSo.com license check
        if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
            add_action('admin_notices', array(&$this, 'license_notice'));
        }

        if (isset($_GET['page']) && 'peepso_config' == $_GET['page'] && !isset($_GET['tab'])) {
            add_action('admin_notices', array(&$this, 'license_notice_forced'));
        }

        // PeepSo.com new version check
        // since 1.7.6
        if(method_exists('PeepSoLicense', 'check_updates_new')) {
            PeepSoLicense::check_updates_new(self::PLUGIN_EDD, self::PLUGIN_SLUG, self::PLUGIN_VERSION, __FILE__);
        }

        return (TRUE);
    }

	/**
	 * Plugin activation
	 * Check PeepSo
	 * @return bool
	 */
	public function activate()
	{
		if (!$this->peepso_check()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoPollsInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}

		return (TRUE);
	}

	/**
	 * Display a message about PeepSo not present
	 */
	public function peepso_disabled_notice()
	{
		?>
		<div class="error peepso">
			<strong>
				<?php echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-polls'), self::PLUGIN_NAME);?>
				<a href="plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291" class="thickbox">
					<?php echo __('Get it now!', 'peepso-polls');?>
				</a>
			</strong>
		</div>
		<?php
	}

	/**
	 * Hooks into PeepSo for compatibility checks
	 * @param $plugins
	 * @return mixed
	 */
	public function filter_all_plugins($plugins)
	{
		$plugins[plugin_basename(__FILE__)] = get_class($this);
		return $plugins;
	}

	/**
	 * Registers a tab in the PeepSo Config Toolbar
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_config_tabs($tabs)
	{
		$tabs['polls'] = array(
			'label' => __('Polls', 'peepso-polls'),
            'icon' => self::ICON,
			'tab' => 'polls',
			'description' => __('PeepSo Polls', 'peepso-polls'),
			'function' => 'PeepSoConfigSectionPolls',
            'cat'   => 'core',
		);

		return $tabs;
	}

    /**
     * Adds the Polls tab to the available post type options
     * @param  array $post_types
     * @param  array $params
     * @return array
     */
    public function post_types($post_types, $params = array())
    {

		if (!apply_filters('peepso_permissions_polls_upload', TRUE)) {
            return ($post_types);
        }

        $post_types['polls'] = array(
            'icon' => 'list-bullet',
            'name' => __('Poll', 'peepso-polls'),
            'class' => 'ps-postbox__menu-item',
        );

        return ($post_types);
    }

	 /**
     * Displays the UI for the polls post type
     * @return string The input html
     */
    public function postbox_tabs($tabs)
    {

		if (!apply_filters('peepso_permissions_polls_upload', TRUE)) {
			return $tabs;
		}

		$data = array(
			'multiselect' => PeepSo::get_option('polls_multiselect', TRUE)
		);

        $tabs['polls'] = PeepSoTemplate::exec_template('polls', 'postbox-polls', $data, TRUE);

        return ($tabs);
    }

    /**
     * This function inserts the polls options on the post box
     * @param array $interactions is the formated html code that get inserted in the postbox
     * @param array $params
     */
    public function postbox_interactions($interactions, $params = array())
    {
        if (isset($params['is_current_user']) && $params['is_current_user'] === FALSE) {
            return ($interactions);
        }

        if (!apply_filters('peepso_permissions_polls_upload', TRUE)) {
            return ($interactions);
        }

        $interactions['poll'] = array(
            'icon' => 'list-bullet',
            'id' => 'poll-post',
            'class' => 'ps-postbox__menu-item',
            'click' => 'return;',
            'label' => '',
            'title' => __('Poll', 'peepso-polls'),
            'style' => 'display:none'
        );

        return ($interactions);
    }

	/*
     * enqueue scripts for polls
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('peepsopolls', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js', array('jquery', 'jquery-ui-sortable', 'peepso', 'peepso-postbox'), self::PLUGIN_VERSION, TRUE);
        add_filter('peepso_data', function($data) {
            $data['polls'] = array(
                'textPostboxPlaceholder' => __('Say something about this poll...', 'peepso-polls'),
                'textOptionPlaceholder' => __('Option %d', 'peepso-polls')
            );
            return $data;
        });
    }

	/**
     * Change the activity stream item action string
     * @param  string $action The default action string
     * @param  object $post   The activity post object
     * @return string
     */
    public function activity_stream_action($action, $post)
    {
        if (self::MODULE_ID === intval($post->act_module_id)) {
            $action = __(' asked a question', 'peepso-polls');
		}
        return ($action);
    }


	/**
	* Sets the activity's module ID to the plugin's module ID
	* @param  array $activity
	* @return array
	*/
    public function activity_insert_data($activity)
    {
        $input = new PeepSoInput();

        // SQL safe
        $type = $input->value('type','',FALSE);

        if ('poll' === $type) {
            $activity['act_module_id'] = self::MODULE_ID;
		}

        return ($activity);
    }

    /**
     * Adds the postmeta to the post, only called when submitting from the polls tab
     * @param  int $post_id The post ID
     */
    public function after_add_post($post_id)
    {
        global $wpdb;

        $input = new PeepSoInput();
        $options = $input->value('options', array(), FALSE); // SQL safe, add_post_meta
        $allow_multiple = $input->int('allow_multiple');

		if (empty($options) || count($options) < 2) {
            return;
		}

		$post_meta = array();
		foreach ($options as $option) {
			$key = substr(md5($option),0,6);
			$post_meta[$key] = array(
				'label' => $option,
				'total_user_poll' => 0
			);
		}

		if ($allow_multiple === 1 && PeepSo::get_option('polls_multiselect', TRUE)) {
			$max_answers = 0;
		} else {
			$max_answers = 1;
		}

		add_post_meta($post_id, 'select_options', serialize($post_meta));
		add_post_meta($post_id, 'total_user_poll', 0);
		add_post_meta($post_id, 'max_answers', $max_answers);
    }


    /**
     * Attach the poll to the post display
     * @param  object $post The post
     */
    public function attach_poll($post)
    {
		if ($post->act_module_id != self::MODULE_ID) {
			return;
		}

		$user_polls = array();
		$is_voted = FALSE;
		if ( get_current_user_id() ) {
			$polls_model = new PeepSoPollsModel();
			$user_polls = $polls_model->get_user_polls(get_current_user_id(), $post->ID);
			$is_voted = $polls_model->is_voted(get_current_user_id(), $post->ID);
		}

		$max_answers = (int) get_post_meta($post->ID, 'max_answers', TRUE);
		$options = @unserialize(get_post_meta($post->ID, 'select_options', TRUE));
		$total_user_poll = get_post_meta($post->ID, 'total_user_poll', TRUE);

		$data = array(
			'id' => $post->ID,
			'options' => (is_array($options) && count($options) > 1) ? $options : array(),
			'type' => $max_answers === 0 ? 'checkbox' : 'radio',
			'enabled' => !get_current_user_id() ? FALSE : TRUE,
			'is_voted' => $is_voted,
			'total_user_poll' => $total_user_poll ? $total_user_poll : 0,
			'user_polls' => $user_polls
		);

		PeepSoTemplate::exec_template('polls', 'content-media', $data);
    }

	public function permissions_polls_upload($permission)
	{
		$url = PeepSoUrlSegments::get_instance();

        $user_id = get_current_user_id();

		if ($url->get(1)) {
			if ($viewed_user = get_user_by('slug', $url->get(1))) {
				$user_id = $viewed_user->ID;
			}
		}

		// only on own profile
		if ($url->get(0) == 'peepso_profile' && $user_id !== get_current_user_id()) {
		    $permission = FALSE;
		}

		// if in group view and group integration is disabled
		if($url->get(0) == 'peepso_groups' && PeepSo::get_option('polls_group', 0) === 0) {
		    $permission = FALSE;
		}

        return apply_filters('peepso_permissions_polls_create', $permission);
	}

	/**
     * Disable repost on polls
     * @param array $actions The default options per post
     * @return  array
     */
	public function activity_post_actions($actions) {
		if ($actions['post']->act_module_id == self::MODULE_ID) {
			unset($actions['acts']['repost']);
		}
		return $actions;
	}

    /**
     *
     * @param array $options
     * @return array $options
     */
    public function post_filters($options) {
        global $post;

        if ( isset($post->act_module_id) && (int) $post->act_module_id === self::MODULE_ID ) {
        	if ( PeepSo::is_admin() || $post->post_author==get_current_user_id() || PeepSo::get_option('polls_changevote', FALSE) ) {
        		$polls_model = new PeepSoPollsModel();
        		$is_voted = $polls_model->is_voted(get_current_user_id(), $post->ID);
        		// Check if already voting.
	            $options['changevote'] = array(
	            	'li-class' => 'ps-js-poll-option-changevote',
	                'label' => __('Change Vote', 'peepso-polls'),
	                'icon' => 'check',
	                'click' => 'peepso.polls.change_vote(' . $post->ID . ', this); return false;',
	                'extra' => $is_voted ? '' : ' style="display:none"'
	            );
        	}
        }

        return $options;
    }
}

PeepSoPolls::get_instance();

// EOF
