<?php

/**
 * Class PeepSoGroupAjax
 * Handle operations on a single group object only
 * Group ID is inside $this->_group_id
 * Interfaces with PeepSoGroupModel via $this->_model
 */
class PeepSoGroupAjax extends PeepSoGroupAjaxAbstract
{
    protected function __construct()
    {
        parent::__construct();

        if($this->_group_id > 0) {
            $this->_model= new PeepSoGroup($this->_group_id);
        }
    }

    /**
     * Called from PeepSoAjaxHandler
     * Declare methods that don't need auth to run
     * @return array
     */
    public function ajax_auth_exceptions()
    {
        return array(
            'group',
        );
    }

    /**
     * GET
     * @param PeepSoAjaxResponse $resp
     */
    public function group(PeepSoAjaxResponse $resp)
    {
        // SQL dsafe
        $keys = $this->_input->value('keys', 'id', FALSE);
        $group = PeepSoGroupAjaxAbstract::format_response($this->_model, PeepSoGroupAjaxAbstract::parse_keys('group', $keys), $this->_group_id);

        $resp->success(TRUE);
        $resp->set('group', $group);
    }

    /**
     * POST
     * Create a group
     * @param PeepSoAjaxResponse $resp
     * @return void
     */
    public function create(PeepSoAjaxResponse $resp)
    {
        $group_data = array(
            'name'			=> $this->_input->value('name', '', FALSE), // SQL safe
            'description'	=> $this->_input->value('description', '', FALSE), // SQL safe,

            'owner_id'		=> get_current_user_id(),
            'meta'			=> $this->_input->value('meta', array(), FALSE), // SQL safe,
        );

        if(PeepSo::get_option('groups_categories_enabled', FALSE)) {
            $group_data['category_id'] = $this->_input->value('category_id', array(), FALSE);
        }

        $errors = PeepSoGroup::validate($group_data);

        if(count($errors)){
            $resp->error($errors);
            return( FALSE );
        }

        // respect line breaks
        $description = $this->_input->value('description', '', FALSE); // SQL Safe
        $description = htmlspecialchars($description);
        $group_data['description'] = trim(PeepSoSecurity::strip_content($description));

        $group = new PeepSoGroup(NULL, $group_data);

        $resp->success(1);
        $resp->set('redirect', $group->get_url());
    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_property(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-property')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified', 'groupso'));
        } else {

            $prop_name = $this->_input->value('property_name', '', FALSE); // SQL Safe
            $prop_value = $this->_input->value('property_value', '', FALSE); // SQL Safe

            $group_data = array(
                $prop_name => $prop_value,
            );

            $errors = PeepSoGroup::validate($group_data);

            if (count($errors)) {
                $resp->success(FALSE);
                foreach ($errors as $key => $error) {
                    $resp->error($error);
                }
                return;
            }

            $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

            if ($PeepSoGroupUser->can('manage_group')) {
                $PeepSoGroup = new PeepSoGroup($this->_group_id);
                $PeepSoGroup->update($group_data);

                $resp->success(TRUE);
                $resp->set('msg', __('Setting saved', 'groupso'));
            } else {
                $resp->success(FALSE);
                $resp->error(__('Insufficient permissions', 'groupso'));
            }
        }
    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_name(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-name')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {

            $name = $this->_input->value('name', '', FALSE); // SQL Safe

            $group_data = array(
                'name' => $name,
            );

            $errors = PeepSoGroup::validate($group_data);

            if (count($errors)) {
                $resp->success(FALSE);
                foreach ($errors as $key => $error) {
                    $resp->error($error);
                }
                return;
            }

            $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

            if ($PeepSoGroupUser->can('manage_group')) {
                $PeepSoGroup = new PeepSoGroup($this->_group_id);
                $PeepSoGroup->update($group_data);

                if ($PeepSoGroup->published) {
                    do_action('peepso_action_group_rename', $this->_group_id, get_current_user_id());
                }

                // if slug should update automatically
                if(1 == PeepSo::get_option('groups_slug_edit', 0)) {

                    // Set an empty slug...
                    $PeepSoGroup->update(array('slug'=>''));

                    // ...and let the constructor figure it out
                    $PeepSoGroup = new PeepSoGroup($this->_group_id);

                    $resp->set('redirect', $PeepSoGroup->get_url().'settings/');
                }

                $resp->success(TRUE);
                $resp->set('msg', __('Group name saved.', 'groupso'));
            } else {
                $resp->success(FALSE);
                $resp->error(__('You are not authorized to change this group name.', 'groupso'));
            }
        }
    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_slug(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-slug')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {

            $slug = $this->_input->value('slug', '', FALSE); // SQL Safe

            $slug = wp_unique_post_slug(sanitize_title_with_dashes($slug), $this->_group_id, 'any', 'peepso-group', 0);

            $group_data = array(
                'slug' => $slug,
            );

            $errors = PeepSoGroup::validate($group_data);

            if (count($errors)) {
                $resp->success(FALSE);
                foreach ($errors as $key => $error) {
                    $resp->error($error);
                }
                return;
            }

            $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

            if ($PeepSoGroupUser->can('manage_group')) {
                $PeepSoGroup = new PeepSoGroup($this->_group_id);
                $PeepSoGroup->update($group_data);

                if ($PeepSoGroup->published) {
                    do_action('peepso_action_group_slug_change', $this->_group_id, get_current_user_id());
                }

                // let the constructor double check everything
                $PeepSoGroup = new PeepSoGroup($this->_group_id);

                $resp->set('redirect', $PeepSoGroup->get_url().'settings/');

                $resp->success(TRUE);
                $resp->set('msg', __('Group name saved.', 'groupso'));
            } else {
                $resp->success(FALSE);
                $resp->error(__('You are not authorized to manage this group\'s settings', 'groupso'));
            }
        }
    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_privacy(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-privacy')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
            return;
        }

        $privacy = $this->_input->int('privacy');

        $group_data = array(
            'privacy' => $privacy,
        );

        $PeepSoGroupPrivacy = PeepSoGroupPrivacy::_();


        if (!isset($PeepSoGroupPrivacy[$privacy])) {
            $resp->success(FALSE);
            $resp->error(__('Invalid Group Privacy') . ": $privacy");
            return;
        }

        $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

        if ($PeepSoGroupUser->can('manage_group')) {
            $PeepSoGroup = new PeepSoGroup($this->_group_id);
            $PeepSoGroup->update($group_data);

            if ($PeepSoGroup->published) {
                do_action('peepso_action_group_privacy_change', $this->_group_id, get_current_user_id());
            }

            $resp->success(TRUE);
            $resp->set('msg', __('Group privacy changed.', 'groupso'));
            $resp->set('new_privacy', PeepSoGroupPrivacy::_($privacy));
        } else {
            $resp->success(FALSE);
            $resp->error(__('You are not authorized to change this group name.', 'groupso'));
        }

    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_description(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-description')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {
            $description = $this->_input->value('description', '', FALSE); // SQL Safe
            $description = htmlspecialchars($description);
            $description = trim(PeepSoSecurity::strip_content($description));

            $group_data = array(
                'description' => $description,
            );

            $errors = PeepSoGroup::validate($group_data);

            if (count($errors)) {
                $resp->success(FALSE);
                foreach ($errors as $key => $error) {
                    $resp->error($error);
                }
                return;
            }

            $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

            if ($PeepSoGroupUser->can('manage_group')) {

                $PeepSoGroup = new PeepSoGroup($this->_group_id);
                $PeepSoGroup->update($group_data);

                $resp->success(TRUE);
                $resp->set('msg', __('Group description saved.', 'groupso'));
            } else {
                $resp->success(FALSE);
                $resp->error(__('You are not authorized to change this group description.', 'groupso'));
            }
        }
    }

    /**
     * POST
     * @param PeepSoAjaxResponse $resp
     * return void
     */
    public function set_group_categories(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-group-categories')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {
            $group_data = array(
                'category_id' => $this->_input->value('category_id', array(), FALSE),
            );

            $errors = PeepSoGroup::validate($group_data);

            if (count($errors)) {
                $resp->success(FALSE);
                foreach ($errors as $key => $error) {
                    $resp->error($error);
                }
                return;
            }

            $PeepSoGroupUser = new PeepSoGroupUser($this->_group_id);

            if ($PeepSoGroupUser->can('manage_group')) {

                PeepSoGroupCategoriesGroups::add_group_to_categories($this->_group_id, $this->_input->value('category_id', array(), FALSE));

                $resp->success(TRUE);
                $resp->set('msg', __('Group categories saved.', 'groupso'));
                $resp->set('category_id', PeepSoGroupCategoriesGroups::get_categories_id_for_group($this->_group_id));

            } else {
                $resp->success(FALSE);
                $resp->error(__('You are not authorized to change this group categories.', 'groupso'));
            }
        }
    }

    /** GROUP IMAGES (COVER/AVATAR) **/

    /*
     * POST
     * Called from AjaxHandler when an image crop request is performed
     */
    public function avatar_crop(PeepSoAjaxResponse $resp)
    {
        $group_id = $this->_input->int('u');

        $x = $this->_input->int('x');
        $y = $this->_input->int('y');
        $x2 = $this->_input->int('x2');
        $y2 = $this->_input->int('y2');
        $width = $this->_input->int('width');
        $height = $this->_input->int('height');
        $tmp = $this->_input->int('tmp');

        $group_user = new PeepSoGroupUser($group_id);
        $group = new PeepSoGroup($group_id);

        // can-manage_content is called on PeepSoGroupUser
        // SQL safe, WP sanitizes it
        if (wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo') && $group_user->can('manage_content')) {
            $avatar_hash = '';

            // get avatar hash value if exist
            if (!$tmp) {
                $avatar_hash = get_post_meta($group->id, 'peepso_group_avatar_hash', TRUE);
                if($avatar_hash) {
                    $avatar_hash = $avatar_hash . '-';
                }
            }

            $src_file = $group->get_image_dir() . $avatar_hash . 'avatar-orig' . ($tmp ? '-tmp' : '') . '.jpg';
            $dest_file = $group->get_image_dir() . $avatar_hash  . 'avatar-full' . ($tmp ? '-tmp' : '') . '.jpg';

            $si = new PeepSoSimpleImage();
            $si->load($src_file);
            // Resize image as edited on the screen, we do this because getting x and y coordinates
            // are unreliable when we are cropping from the edit avatar page; the dimensions on the edit
            // avatar page is not the same as the original image dimensions.
            if (isset($width) && isset($height) && $width > 0 && $height > 0) {
                $si->resize($width, $height);
            }

            $new_image = imagecreatetruecolor(PeepSo::get_option('avatar_size', 100), PeepSo::get_option('avatar_size', 100));
            imagecopyresampled($new_image, $si->image,
                0, 0, $x, $y,
                PeepSo::get_option('avatar_size', 100), PeepSo::get_option('avatar_size', 100), $x2 - $x, $y2 - $y);
            imagejpeg($new_image, $dest_file, 75);

            // re-crop thumbnailavatar image
            $dest_file = $group->get_image_dir() . $avatar_hash . 'avatar' . ($tmp ? '-tmp' : '') . '.jpg';

            // create a new instance of PeepSoSimpleImage - just in case
            $_si = new PeepSoSimpleImage();
            $_si->load($src_file);
            $new_image = imagecreatetruecolor(PeepSoUser::THUMB_WIDTH, PeepSoUser::THUMB_WIDTH);
            imagecopyresampled($new_image, $si->image, // Resize from cropeed image "$si"
                0, 0, $x, $y,
                PeepSoUser::THUMB_WIDTH, PeepSoUser::THUMB_WIDTH, $x2 - $x, $y2 - $y);
            imagejpeg($new_image, $dest_file, 75);

            $image_url = $tmp ? $group->get_tmp_avatar() : $group->get_avatar_url();
            $resp->set('image_url', $image_url);
            $resp->success(TRUE);
        } else {
            $resp->success(FALSE);
            $resp->error(__('Invalid access', 'groupso'));
        }
    }

    /*
     * POST
     * Called from AjaxHandler when an avatar upload request is performed
     */
    public function avatar_upload(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {

            $group_id = $this->_input->int('group_id');

            $group_user = new PeepSoGroupUser($group_id);
            $group = new PeepSoGroup($group_id);

            // can-manage_content is called on PeepSoGroupUser
            if(!$group_user->can('manage_content')) {
                $resp->error(__('You do not have enough permissions.', 'groupso'));
                $resp->success(FALSE);
                return;
            } else {

                if (isset($_FILES['filedata'])) {
                    $allowed_mime_types = apply_filters(
                        'peepso_group_avatar_mime_types',
                        array(
                            'image/jpeg',
                            'image/png'
                        )
                    );

                    if (empty($_FILES['filedata']['tmp_name'])) {
                        $resp->error(__('The file you uploaded is either missing or too large.', 'groupso'));
                        $resp->success(FALSE);
                        return;
                    }

                    if (!in_array($_FILES['filedata']['type'], $allowed_mime_types)) {
                        $resp->error(__('The file type you uploaded is not allowed.', 'groupso'));
                        $resp->success(FALSE);
                        return;
                    }

                    $group->move_avatar_file($_FILES['filedata']['tmp_name']);

                    $image_url = $group->get_tmp_avatar();
                    $full_image_url = $group->get_tmp_avatar(TRUE);
                    $orig_image_url = str_replace('-full', '-orig', $full_image_url);

                    // check image dimension
                    $si = new PeepSoSimpleImage();
                    $orig_image_path = $group->get_image_dir() . 'avatar-orig-tmp.jpg';
                    $si->load($orig_image_path);
                    $width = $si->getWidth();
                    $height = $si->getHeight();
                    $avatar_size = PeepSo::get_option('avatar_size','100');

                    if (($width < $avatar_size) || ($height < $avatar_size)) {
                        $resp->success(FALSE);
                        $resp->set('width', $width);
                        $resp->set('height', $height);
                        $resp->error(sprintf(__('Minimum avatar resolution is %d x %d pixels.', 'groupso'), $avatar_size, $avatar_size));
                        return;
                    }

                    $resp->set('image_url', $image_url);
                    $resp->set('orig_image_url', $orig_image_url);
                    $resp->set('orig_image_path', $orig_image_path);
                    $resp->set('html', PeepSoTemplate::exec_template('groups', 'dialog-group-avatar', array('PeepSoGroup' => $group), TRUE));
                    $resp->success(TRUE);

                    return;
                } else {
                    $resp->error(__('No file uploaded.', 'groupso'));
                    $resp->success(FALSE);
                    return;
                }
            }
        }
    }

    /*
     * POST
     * Called from AjaxHandler when an avatar upload is finalized
     */
    public function avatar_confirm(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {
            $group_id = $this->_input->int('group_id');

            $group_user = new PeepSoGroupUser($group_id);
            $group = new PeepSoGroup($group_id);

            // can-manage_content is called on PeepSoGroupUser
            if($group_user->can('manage_content')) {

                $add_to_stream = PeepSo::get_option('photos_groups_enable_post_updates_group_avatar', 1);
                $group->finalize_move_avatar_file($add_to_stream);

                $resp->success(TRUE);
            } else {
                $resp->error(__('You do not have enough permissions.', 'groupso'));
                $resp->success(FALSE);
            }
        }
    }

    /**
     * POST
     * Deletes a group's avatar
     */
    public function avatar_delete(PeepSoAjaxResponse $resp)
    {
        $group_id = $this->_input->int('group_id');

        $group_user = new PeepSoGroupUser($group_id);
        $group = new PeepSoGroup($group_id);

        // can-manage_content is called on PeepSoGroupUser
        // SQL safe, WP sanitizes it
        if($group_user->can('manage_content') && wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $group->delete_avatar();
            $resp->success(TRUE);
        } else {
            $resp->success(FALSE);
        }
    }

    /*
     * POST
     * Called from AjaxHandler when a cover photo upload request is performed
     * @param object PeepSoAjaxResponse $resp
     */
    public function cover_upload(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {
            $group_id = $this->_input->int('group_id');

            $group_user = new PeepSoGroupUser($group_id);
            $group = new PeepSoGroup($group_id);

            // can-manage_content is called on PeepSoGroupUser
            if($group_user->can('manage_content')) {

                if (isset($_FILES['filedata'])) {
                    $allowed_mime_types = apply_filters(
                        'peepso_group_cover_mime_types',
                        array(
                            'image/jpeg',
                            'image/png'
                        )
                    );

                    if (!in_array($_FILES['filedata']['type'], $allowed_mime_types)) {
                        $resp->error(__('The file type you uploaded is not allowed.', 'groupso'));
                        $resp->success(FALSE);
                        return;
                    }

                    if (empty($_FILES['filedata']['tmp_name'])) {
                        $resp->error(__('The file you uploaded is either missing or too large.', 'groupso'));
                        $resp->success(FALSE);
                        return;
                    }

                    $add_to_stream = PeepSo::get_option('photos_groups_enable_post_updates_group_cover', 1);
                    $group->move_cover_file($_FILES['filedata']['tmp_name'], $add_to_stream);

                    $resp->set('image_url', $group->get_cover_url());
                    $resp->set('html', PeepSoTemplate::exec_template('groups', 'dialog-group-cover', array('PeepSoGroup' => $group), TRUE));
                    $resp->success(TRUE);
                } else {
                    $resp->error(__('No file uploaded.', 'groupso'));
                    $resp->success(FALSE);
                }
            } else {
                $resp->success(FALSE);
                $resp->error(__('You do not have enough permissions.', 'groupso'));
            }
        }
    }

    /*
     * POST
     * Called from AjaxHandler when a cover photo repositoin request is performed
     */
    public function cover_reposition(PeepSoAjaxResponse $resp)
    {
        $group_id = $this->_input->int('group_id');

        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'groupso'));
        } else {
            $group_user = new PeepSoGroupUser($group_id);
            $group = new PeepSoGroup($group_id);

            // can-manage_content is called on PeepSoGroupUser
            if($group_user->can('manage_content')) {

                $x = $this->_input->int('x', 0);
                $y = $this->_input->int('y', 0);

                update_post_meta($group_id, 'peepso_cover_position_x', $x);
                update_post_meta($group_id, 'peepso_cover_position_y', $y);

                $resp->notice(__('Changes saved.', 'groupso'));
                $resp->success(TRUE);
            } else {
                $resp->success(FALSE);
                $resp->error(__('You do not have enough permissions.', 'groupso'));
            }
        }
    }

    /**
     * POST
     * Deletes a group's cover photo
     */
    public function cover_delete(PeepSoAjaxResponse $resp)
    {
        $group_id = $this->_input->int('group_id');
        $group_user = new PeepSoGroupUser($group_id);
        $group = new PeepSoGroup($group_id);

        // SQL safe, WP sanitizes it
        if ($group_user->can('manage_content') && wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'cover-photo')) {
            $resp->success($group->delete_cover_photo());
        } else {
            $resp->success(FALSE);
        }
    }

}
