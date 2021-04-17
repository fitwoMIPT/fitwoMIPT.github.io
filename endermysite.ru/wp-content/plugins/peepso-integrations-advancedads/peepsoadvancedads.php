<?php
/**
 * Plugin Name: PeepSo Monetization: Advanced Ads
 * Plugin URI: https://peepso.com
 * Description: <strong>Requires the Advanced Ads plugin.</strong> Targeted ads throughout Your Community.
 * Author: PeepSo
 * Author URI: https://peepso.com
 * Version: 2.8.0.0
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-advanced-ads
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoAdvancedAdsPlugin
{
    private static $_instance = NULL;

    const PLUGIN_NAME	 = 'Monetization: Advanced Ads';
    const PLUGIN_VERSION = '2.8.0.0';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const PLUGIN_EDD = 1096836;
    const PLUGIN_SLUG = 'advads';

    const ICON = 'https://www.peepso.com/wp-content/plugins/peepso.com-checkout/assets/icons/advanced-ads_icon.svg';

    private static function ready_thirdparty() {
        $result = TRUE;

        if(!class_exists('Advanced_Ads')) {
            $result = FALSE;
        }

        return $result;
    }

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

            return(self::ready_thirdparty() && $peepso_version == $plugin_version);
        }

        return FALSE;
    }

    private function __construct()
    {
        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'dependency_check'));
            add_filter('peepso_license_config', function($list) {

                $list[] = array(
                    'plugin_slug' => self::PLUGIN_SLUG,
                    'plugin_name' => self::PLUGIN_NAME,
                    'plugin_edd' => self::PLUGIN_EDD,
                    'plugin_version' => self::PLUGIN_VERSION
                );

                return $list;

            });
        }

        // Compatibility
        add_filter('peepso_all_plugins', function($plugins) {
            $plugins[plugin_basename(__FILE__)] = get_class($this);
            return $plugins;

        });



        // Activation
        register_activation_hook(__FILE__, array(&$this, 'activate'));

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            add_action('peepso_init', array(&$this, 'init'));
        }

        add_filter( 'advanced-ads-ad-types', array( $this, 'filter_advanced_ads_ad_types' ), 99);

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
        if (is_admin()) {

            add_action('admin_init', array(&$this, 'dependency_check'));

            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));

            add_action('advanced-ads-display-conditions-after', function() {
                echo "<hr><p class=\"description\">".__('Display conditions are not supported in PeepSo Stream placement','peepso-advanced-ads')."</p>";
            });

            add_action('advanced-ads-output-metabox-after', function() {
                echo "<hr><p class=\"description\">".__('"Position" and "show only once" might not work properly when in PeepSo Stream placement','peepso-advanced-ads')."</p>";
            });

            add_action('advanced-ads-visitor-conditions-after', function() {
                echo "<hr><p class=\"description\">".__('Some conditions might not work properly when in PeepSo Stream placement','peepso-advanced-ads')."</p>";
            });

            add_filter('advanced-ads-ad-edit-allowed-metaboxes', function($whitelist) {
                $whitelist[] = 'peepso_advanced_ads';
                return $whitelist;
            });

            add_filter('advanced-ads-list-ad-size', function($size, $ad) {
                return ('peepso' == $ad->type) ? null : $size;
            },-1,2);

            add_action('advanced-ads-ad-list-details-column-after', function($ad) {
                if('peepso' == $ad->type) {
                    $image_id = isset($ad->output['image_id']) ? $ad->output['image_id'] : NULL;
                    $avatar_id = isset($ad->output['avatar_id']) ? $ad->output['avatar_id']: NULL;

                    if($avatar_id) {
                        $image = wp_get_attachment_image_src( $avatar_id, 'full' );
                        if ( $image ) {
                            echo "<img src=\"{$image[0]}\" height=\"100\"/>";
                        }
                    }

                    if($image_id) {
                        $image = wp_get_attachment_image_src( $image_id, 'full' );
                        if ( $image ) {
                            echo "<img src=\"{$image[0]}\" height=\"100\"/>";
                        }
                    }
                }
            });

            add_filter('advanced-ads-placement-types', function($types) {
                $types['peepso_stream'] = array(
                    'title' => __( 'PeepSo Stream', 'peepso-advanced-ads' ),
                    'description' => __( 'Display this ad in PeepSo Stream', 'advanced-ads' ),
                    'image' => plugin_dir_url( __FILE__ )  . '/assets/images/peepso-stream-placement.png'
                );

                return $types;
            });

            add_action('admin_enqueue_scripts', function() {
                $screen = get_current_screen();
                if ($screen->id === 'advanced_ads') {
                    wp_enqueue_script('peepso-advanced-ads', plugin_dir_url(__FILE__) . 'assets/js/admin.js', NULL, self::PLUGIN_VERSION, TRUE);
                }
            });

        } else {

            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) { return; }

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            add_action('peepso_action_before_posts_per_page', array(&$this, 'action_before_posts_per_page'),1,1);

            add_filter('advanced-ads-can-inject-into-content', function() {
                global $post;
                if(stristr($post->post_type, 'peepso')) {
                    return FALSE;
                }

                return TRUE;
            });
        }

        add_filter( 'advanced-ads-visitor-conditions', array( $this, 'filter_advads_visitor_conditions' ) );

        PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
        PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));
    }

    /**
     * Renders ad inside the stream
     * @param $PeepSoActivity
     */
    public function action_before_posts_per_page($PeepSoActivity)
    {
        $input = new PeepSoInput();

        // 1 - after first item, 2 - after second item etc
        $page = $input->int('page', 2) - 1;

        // PeepSo config controlling ad repetition
        $first_ad = PeepSo::get_option('advanced_ads_stream_first_ad', 0);
        $repeat_ad = PeepSo::get_option('advanced_ads_stream_repeat_ad', 0);

        
        if (
            (0 == $first_ad) // EXIT if stream ads disabled
            || (0 == $page) // EXIT if first page
            || (0 == $repeat_ad && $page != $first_ad) // EXIT if ads already displayed and not repeated
            || ($repeat_ad > 0 && $page < $first_ad) // EXIT if page less than first ads
            || ($page != $first_ad && ($repeat_ad > 0 && (($page - $first_ad) % $repeat_ad))) // EXIT if neither first or repeated position (beware of zero division)
            ) {
            return;
        }

        // Get all placements that are of type peepso-stream
        $trans_placements = 'peepso_ads_placement_ids';
        $placement_ids = get_transient($trans_placements);

        if (!is_array($placement_ids)) {

            $placements = Advanced_Ads::get_instance()->get_model()->get_ad_placements_array();
            $placement_ids = array();

            // EXIT if no ad placements
            if (!count($placements)) {
                return;
            }

            foreach ($placements as $id => $placement) {
                if ('peepso_stream' != $placement['type']) {
                    continue;
                }

                $placement_ids[] = $id;
            }
        }

        // EXIT if no valid placement ids
        if (!count($placement_ids)) {
            new PeepSoError('No placement ids', 'message', 'advads_stream');
            return;
        }

        // Randomize placement ids
        #shuffle($placement_ids);

        // Loop through placements and check if they produce any output
        foreach($placement_ids as $id) {
            $content = get_ad_placement($id);
            $content = stristr($content, 'ps-stream') ? $content : PeepSoTemplate::exec_template('ads','ad-stream-wrapper', array('content'=>$content), TRUE);
            echo $content;
        }

        #set_transient($trans_placements, $placement_ids, 30);
    }

    /**
     * Register a PeepSo ad type
     *
     * @param $types
     * @return mixed
     */
    public function filter_advanced_ads_ad_types($types)
    {
        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'advancedadsadtypepeepso.php');
        $types['peepso'] = new PeepSoAdvancedAdsAdTypePeepSo();
        return $types;
    }

    /**
     * Register PeepSo Visitor Conditions
     */
    public function filter_advads_visitor_conditions($conditions)
    {
        // Special case - User Age
        $field = PeepSoField::get_field_by_id('birthdate');
        $required = ($field->prop('meta','validation','required'))  ? '*' : '';
        $unpublished = ($field->prop('published')) ? '' : ' '.__('(unpublished)', 'peepso-advanced-ads');
        $label = sprintf(__('PeepSo Profile: %s%s%s', 'peepso-advanced-ads'), __('Age','peepso-advanced-ads'), $required, $unpublished);

        $conditions['peepso_age'] = array(
            'label' => $label,
            'description' => __('PeepSo User Age', 'peepso-advanced-ads'),
            'metabox' => array('PeepSoAdvancedAdsTargeting', 'field_int_metabox'), // callback to generate the metabox
            'check' => array('PeepSoAdvancedAdsTargeting', 'field_int_check') // callback for frontend check
        );


        // Other valid field types
        $PeepSoUser = PeepSoUser::get_instance(0);
        $PeepSoUser->profile_fields->load_fields();
        foreach($PeepSoUser->profile_fields->profile_fields as $field) {

            if(!$field instanceof PeepSoFieldSelectSingle) {
                continue;
            }

            $required = ($field->prop('meta','validation','required'))  ? '*' : '';
            $unpublished = ($field->prop('published')) ? '' :' '.__('(unpublished)', 'peepso-advanced-ads');
            $label = sprintf(__('PeepSo Profile: %s%s%s', 'peepso-advanced-ads'), $field->title, $required, $unpublished);

            $conditions['peepso_field_'.$field->id] = array(
                'label' => $label,
                'description' => 'PeepSo - '.$field->title,
                'metabox' => array( 'PeepSoAdvancedAdsTargeting', 'field_select_metabox'), // callback to generate the metabox
                'check' => array( 'PeepSoAdvancedAdsTargeting', 'field_select_check' ) // callback for frontend check
            );
        }

        // Friend count

        if(class_exists('PeepSoFriendsPlugin')) {
            $conditions['peepso_friends_count'] = array(
                'label' => __('PeepSo Friends: Amount Of Friends', 'peepso-advanced-ads'),
                'description' => __('PeepSo Friends: Amount Of Friends', 'peepso-advanced-ads'),
                'metabox' => array('PeepSoAdvancedAdsTargeting', 'field_int_metabox'), // callback to generate the metabox
                'check' => array('PeepSoAdvancedAdsTargeting', 'friend_count_check') // callback for frontend check
            );
        }

        // Group membership

        if(class_exists('PeepSoGroupsPlugin')) {
            $conditions['peepso_group_member'] = array(
                'label' => __('PeepSo Group Membership', 'peepso-advanced-ads'),
                'description' => __('PeepSo Group Membership', 'peepso-advanced-ads'),
                'metabox' => array('PeepSoAdvancedAdsTargeting', 'group_member_metabox'), // callback to generate the metabox
                'check' => array('PeepSoAdvancedAdsTargeting', 'group_member_check') // callback for frontend check
            );
        }

        // VIP Icon

        if(class_exists('PeepSoVipIconsModel')) {
            $conditions['peepso_vip'] = array(
                'label' => __('PeepSo VIP Icon', 'peepso-advanced-ads'),
                'description' => __('PeepSo VIP Icon', 'peepso-advanced-ads'),
                'metabox' => array('PeepSoAdvancedAdsTargeting', 'vip_metabox'), // callback to generate the metabox
                'check' => array('PeepSoAdvancedAdsTargeting', 'vip_check') // callback for frontend check
            );
        }

        return $conditions;
    }

    /********** Plugin basics, activation, dependency check, licensing, updates **********/

    public function activate()
    {
        if (!$this->dependency_check()) {
            return (FALSE);
        }

        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
        $install = new PeepSoAdvancedAdsInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);

        return (TRUE);
    }

    public function dependency_check()
    {
        $success = TRUE;

        if (!class_exists('PeepSo')) {
            add_action('admin_notices', array(&$this, 'peepso_disabled_notice'));
            unset($_GET['activate']);
            $success = FALSE;
        }

        if (!self::ready_thirdparty()) {

            add_action('admin_notices', function() {

                ?>
                <div class="error peepso">
                    <?php echo sprintf(__('PeepSo %s requires the Advanced Ads plugin.', 'peepso-advanced-ads'), self::PLUGIN_NAME);?>
                </div>
                <?php
            }, 999);
        }

        if(!$success) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins(plugin_basename(__FILE__));
            return (FALSE);
        }

        // PeepSo.com license check
        if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
            add_action('admin_notices', function() {

                PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);

            });
        }

        if (isset($_GET['page']) && 'peepso_config' == $_GET['page'] && !isset($_GET['tab'])) {
            add_action('admin_notices', function() {

                PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);

            });
        }

        // PeepSo.com new version check
        // since 1.7.6
        if(method_exists('PeepSoLicense', 'check_updates_new')) {
            PeepSoLicense::check_updates_new(self::PLUGIN_EDD, self::PLUGIN_SLUG, self::PLUGIN_VERSION, __FILE__);
        }

        return (TRUE);
    }

    public function peepso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-advanced-ads'), self::PLUGIN_NAME);?>
				<a href="plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291" class="thickbox">
					<?php echo __('Get it now!', 'peepso-advanced-ads');?>
				</a>
            </strong>
        </div>
        <?php
    }

    public function advancedads_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php echo sprintf(__('The %s plugin requires the Advanced Ads plugin to be installed and activated.', 'peepso-advanced-ads'), self::PLUGIN_NAME);?>
                <a href="plugin-install.php?tab=plugin-information&amp;plugin=advanced-ads&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">
					<?php echo __('Get it now!', 'peepso-advanced-ads');?>
				</a>
            </strong>
        </div>
        <?php
    }

    public function enqueue_scripts(){}

    public function admin_config_tabs( $tabs )
    {
        $tabs['advads'] = array(
            'label' => __('Advanced Ads', 'peepso-advanced-ads'),
            'icon' => self::ICON,
            'tab' => 'advads',
            'description' => __('Advanced Ads', 'peepso-advanced-ads'),
            'function' => 'PeepSoConfigSectionAdvancedAds',
            'cat'   => 'integrations',
        );

        return $tabs;
    }

}

PeepSoAdvancedAdsPlugin::get_instance();
// EOF
