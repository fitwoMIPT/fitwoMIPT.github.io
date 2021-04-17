<?php

class PeepSoConfigSectionGroups extends PeepSoConfigSectionAbstract
{
// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
        $this->_group_general();
        $this->new_members();
        $this->_group_listing();

		$this->context='right';
        $this->_group_categories();
        $this->_group_seo();
        $this->_group_permissions();
        $this->_group_advanced();
	}

    private function _group_seo()
    {
        $this->args('default', 1);
        $this->args('descript', __('Enabled: /groups/my-amazing-group/','groupso') . '<br>'. __('Disabled: /groups/1234/','groupso'));
        $this->set_field(
            'groups_urls_slugs_enable',
            __('Use slugs in group URLs', 'groupso'),
            'yesno_switch'
        );

        $options = array(
            0 => __('never','groupso'),
            1 => __('when group name is changed','groupso'),
            2 => __('by the group owner','groupso'),
        );
        $this->args('options', $options);
        $this->args('default', 'on');
        $this->args('descript', __('Option 1: group slug will stay the same as the original group name.','groupso') .'<br>'.__('Option 2: new group slug will be generated upon group name change.','groupso') . '<br>' . __('Option 3: the group owner can change the slug manually.','groupso'));
        $this->set_field(
            'groups_slug_edit',
            __('Group slug changes', 'groupso'),
            'select'
        );

        $this->set_group(
            'opengraph',
            __('SEO', 'groupso')
        );
    }

    private function _group_listing(){
        # Show Group Owner on Groups listing
        $this->args('default', 1);
        $this->args('descript', __('Show or hide the groups owner(s) in the groups listing','groupso'));
        $this->set_field(
            'groups_listing_show_group_owner',
            __('Show owner(s)','groupso'),
            'yesno_switch'
        );

        # Show Group Creation date on Groups listing
        $this->args('default', 1);
        $this->args('descript', __('Show or hide the groups creation dates in the groups listing','groupso'));
        $this->set_field(
            'groups_listing_show_group_creation_date',
            __('Show creation date','groupso'),
            'yesno_switch'
        );

        # Allow guest access to Groups listing
        $this->args('descript', __('Show or hide the groups listing from visitors who are not logged in','groupso'));
        $this->set_field(
            'groups_allow_guest_access_to_groups_listing',
            __('Allow guest access','groupso'),
            'yesno_switch'
        );

        $options = array(
            'id' => __('Recently added (default)','groupso'),
            'post_title' => __('Alphabetical','groupso'),
            'meta_members_count' => __('Members count','groupso'),
        );
        $this->args('options', $options);

        $this->set_field(
            'groups_default_sorting',
            __('Default sort', 'groupso'),
            'select'
        );

        $options = array(
            'DESC' => __('Descending (default)','groupso'),
            'ASC' => __('Ascending','groupso'),
        );

        $this->args('options', $options);

        $this->set_field(
            'groups_default_sorting_order',
            __('Default sort direction', 'groupso'),
            'select'
        );

        // Single column view.
        $this->args( 'descript', __( 'Enable or disable single column view on the group listing.', 'groupso' ) );
        $this->set_field(
            'groups_single_column',
            __( 'Single column view', 'groupso' ),
            'yesno_switch'
        );

        $this->set_group(
            'opengraph',
            __('Group listings', 'groupso')
        );
    }

	private function _group_general()
	{
		# Enable Group Creation
		$this->args('default', 1);
		$this->args('descript', __('Enabled: all site members can create groups','groupso') .'<br>' .__('Disabled: only site admins can create groups','groupso'));
		$this->set_field(
			'groups_creation_enabled',
			__('Enable group creation', 'groupso'),
			'yesno_switch'
		);


		$general_config = apply_filters('peepso_groups_general_config', array());

		if(count($general_config) > 0 ) {

			foreach ($general_config as $option) {
				if(isset($option['descript'])) {
					$this->args('descript', $option['descript']);
				}
				if(isset($option['int'])) {
					$this->args('int', $option['int']);
				}
				if(isset($option['default'])) {
					$this->args('default', $option['default']);
				}

				$this->set_field($option['name'], $option['label'], $option['type']);
			}
		}

        // Build Group
		$this->set_group(
			'general',
			__('General', 'groupso')
		);
	}

	private function new_members() {

        $this->set_field(
            'groups_post_to_stream_separator',
            __('Post to stream when joining', 'groupso'),
            'separator'
        );

        # Post to stream when joining
        $this->args('descript', __('Enabled: automatically post on user\'s stream when they join a group. These posts are deleted automatically when the user leaves or is removed/banned from the group.','groupso'));
        $this->set_field(
            'groups_join_post_to_stream',
            __('Enabled','groupso'),
            'yesno_switch'
        );

        $this->args('descript', __('For example "joined this group". Leave empty for default. Applies to old posts too.','groupso'));
        $this->set_field(
            'groups_join_post_action_text_group',
            __('Action text (on group stream)','groupso'),
            'text'
        );

        $this->args('descript', __('For example "joined a group:". Leave empty for default. Applies to old posts too.','groupso'));
        $this->set_field(
            'groups_join_post_action_text_other',
            __('Action text (on other streams)','groupso'),
            'text'
        );

        $this->set_field(
          'groups_notifications_separator',
          __('Notifications', 'groupso'),
          'separator'
        );

        # Default onsite subscription status
        $this->args('default', 1);
        $this->args('descript', __('Enabled: new group members will automatically be subscribed to receive new posts notifications (on-site)','groupso'));
        $this->set_field(
            'groups_notify_default',
            __('Automatically subscribe new members to notifications', 'groupso'),
            'yesno_switch'
        );

        # Default e-mail subscription status
        $this->args('default', 1);
        $this->args('descript', __('Enabled: new group members will automatically be subscribed to receive new posts e-mail notifications','groupso'));
        $this->set_field(
            'groups_notify_email_default',
            __('Automatically subscribe new members to e-mails', 'groupso'),
            'yesno_switch'
        );

        // Build Group
        $this->set_group(
            'newmembers',
            __('New members', 'groupso')
        );
    }

	private function _group_categories()
	{
		# Enable Group Categories
		$this->args('default', 0);
		$this->args('descript', __('Users will be able to assign groups to categories.','groupso'));
		$this->set_field(
			'groups_categories_enabled',
			__('Enable group categories', 'groupso'),
			'yesno_switch'
		);

        # Set Group Categories as default view
        $this->args('default', 0);
        $this->set_field(
            'groups_categories_default_view',
            __('Set group categories as default view.', 'groupso'),
            'yesno_switch'
        );


        # Enable Multiple Categories Per Group
        $this->args('default', 0);
        $this->args('descript', __('Users will be able to assign a group to multiple categories','groupso'));
        $this->set_field(
            'groups_categories_multiple_enabled',
            __('Allow multiple categories per group', 'groupso'),
            'yesno_switch'
        );

        $this->set_field(
            'groups_category_list',
            __('Category listing', 'groupso'),

            'separator'
        );

        # Groups per category
        $this->args('default', 4);

        $ints = array(2,4,6,8,10,12,14,16,18,20,22,24,26,28,30);

        $options = array();

        foreach($ints as $i) {
            $options[$i] = sprintf(__('%d groups','groupso'), $i);
        }

        $options[4].=' ('.__('default', 'groupso').')';
        $this->args('options', $options);




        $this->args('descript', __('Only this many groups will show in each category before offering a link to the full category listing.','groupso'));

        $this->set_field(
            'groups_categories_group_count',
            __('Each category loads', 'groupso'),
            'select'
        );


        # Expand All Cateogires
        $this->args('descript', __('Disabled: only the first category shows a preview of the group listing.','groupso'));
        $this->set_field(
            'groups_categories_expand_all',
            __('Expand all categories', 'groupso'),
            'yesno_switch'
        );

        # Hide Empty Categories
        $this->args('default', 0);
        $this->args('descript', __('Hide categories which don\'t have any groups assigned to them.','groupso'));
        $this->set_field(
            'groups_categories_hide_empty',
            __('Hide empty categories', 'groupso'),
            'yesno_switch'
        );

        # Show Group Count
        $this->args('default', 0);
        $this->args('descript', __('The count will not be accurate if a category contains unpublished or secret groups.','groupso'));
        $this->set_field(
            'groups_categories_show_count',
            __('Show group count', 'groupso'),
            'yesno_switch'
        );

		// Build Group
		$this->set_group(
			'categories',
			__('Categories', 'groupso')
		);
	}

    private function _group_permissions()
    {

        $this->args('descript', __('Enabled: group owners can edit posts and comments in respective groups', 'groupso') . '<br/>' . __('Disabled: only admin can edit posts and comments', 'groupso'));
        $this->args('default', 1);
        $this->set_field(
            'groups_post_edits_owner',
            __('By group owners', 'groupso'),
            'yesno_switch'
        );

        $this->args('descript', __('Enabled: group managers can edit posts and comments in respective groups', 'groupso') . '<br/>' . __('Disabled: only admin can edit posts and comments', 'groupso'));
        $this->args('default', 1);
        $this->set_field(
            'groups_post_edits_manager',
            __('By group managers', 'groupso'),
            'yesno_switch'
        );

        $this->args('descript', __('Enabled: group moderators can edit posts and comments in respective groups', 'groupso') . '<br/>' . __('Disabled: only admin can edit posts and comments', 'groupso'));
        $this->args('default', 1);
        $this->set_field(
            'groups_post_edits_moderator',
            __('By group moderators', 'groupso'),
            'yesno_switch'
        );

        // Build Group
        $this->set_group(
            'permissions',
            __('Editing group posts and comments', 'groupso')
        );
    }

    private function _group_advanced()
    {
        # Don't pin on main stream
        $this->args('descript', __('Enabled: pinned group posts will be pinned to top only in group views','groupso').'<br/>'.__('Disabled: pinned group posts will be also pinned to top on author profile and main activity','groupso'));
        $this->set_field(
            'groups_pin_group_only',
            __('Pin to top only inside groups', 'groupso'),
            'yesno_switch'
        );


        # Replace "invite" with "add" for admins
        $this->args('default', 0);
        $this->args('descript', __('Site and Community Administrators will add users to group without the need to confirm.','groupso'));
        $this->set_field(
            'groups_add_by_admin_directly',
            __('Replace "invite" with "add" for Admins', 'groupso'),
            'yesno_switch'
        );

        # Email admins about new groups
        $this->args('default', 0);
        $this->args('descript', __('Users with Administrator role will receive an e-mail when a new group is created','groupso'));
        $this->set_field(
            'groups_create_notify_admin',
            __('E-mail Admins when a new group is created', 'groupso'),
            'yesno_switch'
        );

        # Group Meta in Stream items
        $this->args('default', 0);
        $this->args('descript', __('When enabled, hovering over group names on stream will display membership and following summary. This feature is likely to slow down stream performance.','groupso'));
        $this->set_field(
            'groups_meta_in_stream',
            __('Show membership summary for group names on stream', 'groupso'),
            'yesno_switch'
        );

        // Build Group
        $this->set_group(
            'advanced',
            __('Advanced', 'groupso')
        );
    }

}