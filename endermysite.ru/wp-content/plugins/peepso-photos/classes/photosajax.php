<?php

class PeepSoPhotosAjax extends PeepSoAjaxCallback
{

    /**
     * Called from PeepSoAjaxHandler
     * Declare methods that don't need auth to run
     * @return array
     */
    public function ajax_auth_exceptions()
    {
        return apply_filters('peepso_photos_ajax_auth_exceptions', array('get_user_photos_album'));;
    }

    /**
     * Saves the uploaded photo to the USER_ID/photos/tmp folder and returns the unique filename
     * also performs validation
     * @param  PeepSoAjaxResponse $resp
     */
    public function upload_photo(PeepSoAjaxResponse $resp)
    {
        if (count($_FILES) > 0 && isset($_FILES['filedata'])) {

            $user = PeepSoUser::get_instance(get_current_user_id());

            $photos_model = new PeepSoPhotosModel();

            if (!$photos_model->photo_size_can_fit($user->get_id(), $this->_input->int('size', 0))) {
                $resp->error(__('Maximum file upload quota reached. Delete posts with photos to free some space', 'picso'));
                $resp->success(FALSE);
                return;
            }

            $image_dir = $photos_model->get_photo_dir();
            if (!is_dir($image_dir))
                mkdir($image_dir, 0755, TRUE);

            $max_upload_size = PeepSo::get_option('photos_max_upload_size');

            // use WP max upload size if it is smaller than PeepSo max upload size
            $wp_max_size = max(wp_max_upload_size(), 0);
            $wp_max_size /= pow(1024, 2);
            if ($wp_max_size < $max_upload_size) {
                $max_upload_size = $wp_max_size;
            }

            $orientations = array();
            $files = array();
            // 1 means Resize
            $photos_behavior = PeepSo::get_option('photos_behavior');

			foreach ($_FILES['filedata']['tmp_name'] as $key => $value) {
                if ($_FILES['filedata']['size'][$key] >= $max_upload_size * 1048576) {
                    $resp->error(sprintf(__('Only files up to %1$dMB are allowed', 'picso'), $max_upload_size));
                    $resp->success(FALSE);
                    return;
                }

				if ($_FILES['filedata']['type'][$key] == 'image/png')
                {
                    $colorRgb = array('red' => 255, 'green' => 255, 'blue' => 255);  //background color

                    $img = @imagecreatefrompng($value);
                    $width  = imagesx($img);
                    $height = imagesy($img);

                    //create new image and fill with background color
                    $backgroundImg = @imagecreatetruecolor($width, $height);
                    $color = imagecolorallocate($backgroundImg, $colorRgb['red'], $colorRgb['green'], $colorRgb['blue']);
                    imagefill($backgroundImg, 0, 0, $color);

                    //copy original image to background
                    imagecopy($backgroundImg, $img, 0, 0, 0, 0, $width, $height);

                    //save as png
                    imagepng($backgroundImg, $value, 0);
                }

                $image = wp_get_image_editor($value);

				if (is_wp_error($image)) {
                    $resp->error($image->get_error_message());
                    $resp->success(FALSE);
                    return;
                }

                // TODO: move these initializations outside of the loop
                $max_width = intval(PeepSo::get_option('photos_max_image_width'));
                $max_height = intval(PeepSo::get_option('photos_max_image_height'));
                if (0 !== $max_width || 0 !== $max_height) {
                    $dimension = $image->get_size();
                    $error_message = NULL;
                    if (0 !== $max_width && $dimension['width'] > $max_width)
                        $error_message = sprintf(__('Only photos with a maximum of %1$d pixel width are allowed, please resize.', 'picso'), $max_width);
                    else if (0 !== $max_height && $dimension['height'] > $max_height)
                        $error_message = sprintf(__('Only photos with a maximum of %1$d pixel height are allowed, please resize.', 'picso'), $max_height);

                    // returns an error if uploaded photo is larger than the max size and config setting behavior is NOT Resize, otherwise photo should be resized
                    if (NULL !== $error_message && 1 != PeepSo::get_option('photos_behavior')) {
                        $error_message .= '<br/>' . sprintf(__('Image exceeds maximum photo size of %1$d x %2$d.', 'picso'), $max_width, $max_height);
                        $resp->error($error_message);
                        $resp->success(FALSE);
                        return;
                    }
                }
				
				$photos_model->fix_image_orientation($image, $_FILES['filedata']['tmp_name'][$key]);
                $orientations[$key] = $photos_model->last_orientation;

                if (1 == $photos_behavior) {
                    $image->resize($max_width, $max_height);
                }

                $tmp_file = $photos_model->get_tmp_file($_FILES['filedata']['name'][$key]);

                $filetype = wp_check_filetype($tmp_file['path']);

                while(true) {
                    $filehash = md5($tmp_file['name'] . time());
                    $filename =  $filehash.'.jpg';// . $filetype['ext'];
                    $tmp_file['path'] = str_replace($tmp_file['name'], $filename, $tmp_file['path']);
                    $tmp_file['name'] = $filename;
                    if(!file_exists($tmp_file['path'])) {
                        break;
                    }
                }

                $files[$key] = $filename;

                $new_thumbs = $photos_model->generate_thumbs( $filename, 'jpg', $image, NULL);

                foreach($new_thumbs as $thumb) {
                    $photos_model->imagick_strip($thumb);
                }

                $thumb = str_replace($photos_model->get_thumbs_dir(), '', $new_thumbs['s_s']);

                $thumbs[$key] = $photos_model->get_photo_thumbs_url($thumb);

				// save with compression
                $image->set_quality(PeepSo::get_option('photos_quality_full', 95));
                $image->save($tmp_file['path'],'image/jpeg');

                $photos_model->imagick_strip($tmp_file['path']);
                /* EOF THUMB */
				
				if ($_FILES['filedata']['type'][$key] == 'image/gif') {
					copy($_FILES['filedata']['tmp_name'][$key], PeepSo::get_peepso_dir() . 'users/'.$user->get_id() .'/photos/tmp/'. $filehash . '.gif');
				} 
            }

            $resp->set('orientations', $orientations);
            $resp->set('files', $files);
            $resp->set('thumbs', $thumbs);
            $resp->success(TRUE);
        }
    }

    /**
     * Called before uploading a photo to the tmp directory
     * @param  PeepSoAjaxResponse $resp
     */
    public function validate_photo_upload(PeepSoAjaxResponse $resp)
    {
        $user = PeepSoUser::get_instance(get_current_user_id());

        $max_upload_size = intval(PeepSo::get_option('photos_max_upload_size'));
        $daily_limit = intval(PeepSo::get_option('photos_daily_photo_upload_limit'));
        $max_upload_limit = intval(PeepSo::get_option('photos_max_user_photo'));

        // use WP max upload size if it is smaller than PeepSo max upload size
        $wp_max_size = max(wp_max_upload_size(), 0);
        $wp_max_size /= pow(1024, 2);
        if ($wp_max_size < $max_upload_size) {
            $max_upload_size = $wp_max_size;
        }

        $photos_model = new PeepSoPhotosModel();
        $photos = $this->_input->int('photos', 1);
        $photos_count_today = $photos_model->count_author_post($user->get_id(), TRUE) + $photos;
        $error = NULL;

        if ($photos_count_today > $daily_limit && 0 !== $daily_limit && !PeepSo::is_admin())
            $error = __('Maximum daily photo upload quota reached. Delete posts with photos to free some space.', 'picso');

        $photos_count = $photos_model->count_author_post($user->get_id()) + $photos;
        if ($photos_count >= $max_upload_limit && 0 != $max_upload_limit)
            $error = __('Maximum photo upload quota reached. Delete posts with photos to free some space.', 'picso');
        else if ($this->_input->int('filesize', 0) >= $max_upload_size * 1048576)
            $error = sprintf(__('Only files up to %1$dMB are allowed.', 'picso'), $max_upload_size);
        else if (!$photos_model->photo_size_can_fit($user->get_id(), $this->_input->int('size', 0)))
            $error = __('Maximum file upload quota reached. Delete posts with photos to free some space.', 'picso');

        $resp->success(TRUE);
        if (NULL !== $error) {
            $resp->error($error);
            $resp->success(FALSE);
        }
    }

    public function get_user_photos(PeepSoAjaxResponse $resp)
    {
        $limit = $this->_input->int('limit', 1);

        $page = $this->_input->int('page', 1);
        $sort = $this->_input->value('sort', 'desc', array('desc','asc'));

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $owner = $this->_input->int('user_id');
        $module_id = $this->_input->int('module_id',0);

        $photos_model = new PeepSoPhotosModel();
        $photos = $photos_model->get_user_photos($owner, $offset, $limit, $sort, $module_id);

        ob_start();

        if (count($photos)) {
            foreach ($photos as $photo) {
                // checking batch upload
                if($photos_model->count_post_photos($photo->pho_post_id) == 1) {
                    $new_act_id = $photos_model->get_photo_activity($photo->pho_post_id);
                    if($new_act_id) {
                        $photo->act_id = $new_act_id;
                    }
                }
                $params = array('user' => $photo->pho_owner_id);
                $photo->onclick = apply_filters('peepso_photos_photo_item_click', 'ps_comments.open(' . $photo->pho_id . ', \'photo\', 0, { user: ' . $photo->pho_owner_id . ' }); return false;', $photo, $params);

                echo PeepSoTemplate::exec_template('photos', 'photo-item-page', (array)$photo);
            }
            $resp->success(1);
            $resp->set('found_photos', count($photos));
            $resp->set('photos', ob_get_clean());
        }else {
            $resp->success(FALSE);

            $owner_name = PeepSoUser::get_instance($owner)->get_firstname();
            if ($module_id != 0) {
                $owner_name = apply_filters('peepso_photos_filter_owner_name', $owner);
            }

            $message = (($module_id == 0) && (get_current_user_id() == $owner)) ? __('You don\'t have any photos yet', 'picso') : sprintf(__('%s doesn\'t have any photos yet', 'picso'), $owner_name);

            $resp->error(PeepSoTemplate::exec_template('profile','no-results-ajax', array('message' => $message), TRUE));
        }
    }

    /**
     * todo:docblock
     */
    public function get_list_albums(PeepSoAjaxResponse $resp)
    {
        $limit = $this->_input->int('limit', 1);

        $page = $this->_input->int('page', 1);
        $sort = $this->_input->value('sort', 'desc', array('asc','desc'));

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $owner = $this->_input->int('user_id');

        // module, extends for groups / page / events
        $module_id = $this->_input->int('module_id', 0);

        if($module_id === 0 && $owner !== 0) {
            $user = PeepSoUser::get_instance($owner);

            $album_owner = $owner;
            $profile_url = $user->get_profileurl();

        } else {

            $album_owner = apply_filters('peepso_photos_filter_owner_album', $owner);
            $profile_url = apply_filters('peepso_photos_album_owner_profile_url', '');
        }

        $photos_album_model = new PeepSoPhotosAlbumModel();
        $albums = $photos_album_model->get_user_photos_album($album_owner, $offset, $limit, $sort, $module_id);

        ob_start();

        if (count($albums)) {
            foreach ($albums as $album) {
                # code...
                $album->num_photo   = $photos_album_model->get_num_photos_by_album($owner, $album->pho_album_id, $module_id);
                $cover              = $photos_album_model->get_album_photo($owner, $album->pho_album_id, 0, 1, 'desc', $module_id);
                if(count($cover)>0){
                    $album->cover_photo       = $cover[0];
                }

                $template_item = apply_filters('peepso_photos_ajax_template_item_album', 'photo-item-album');

                echo PeepSoTemplate::exec_template('photos', $template_item, array(
                    'album' => $album,
                    'profile_url' => $profile_url));
            }
        }

        $resp->success(1);
        $resp->set('found_albums', count($albums));
        $resp->set('albums', ob_get_clean());
    }


    /**
     * function get user photo album
     */
    public function get_user_photos_album(PeepSoAjaxResponse $resp)
    {
        $limit = $this->_input->int('limit', 1);

        $page = $this->_input->int('page', 1);
        $sort = $this->_input->value('sort', 'desc', array('asc','desc'));

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $owner = $this->_input->int('user_id');
        $album_id = $this->_input->int('album_id');
        $module_id = $this->_input->int('module_id', 0);

        $photos_model = new PeepSoPhotosModel();
        $photos = $photos_model->get_user_photos_by_album($owner, $album_id, $offset, $limit, $sort, $module_id);

        ob_start();

        if (count($photos)) {
            foreach ($photos as $photo) {

                // checking batch upload
                if($photos_model->count_post_photos($photo->pho_post_id) == 1) {
                    $new_act_id = $photos_model->get_photo_activity($photo->pho_post_id);
                    if($new_act_id) {
                        $photo->act_id = $new_act_id;
                    }
                }
                $params = array('user' => $photo->pho_owner_id, 'album' => $photo->pho_album_id);
                $photo->onclick = apply_filters('peepso_photos_photo_item_click', 'ps_comments.open(' . $photo->pho_id . ', \'photo\', 0, { user: ' . $photo->pho_owner_id . ', album: ' . $photo->pho_album_id . ' }); return false;', $photo, $params);

                echo PeepSoTemplate::exec_template('photos', 'photo-album-item-page', (array)$photo);
            }
        }

        $resp->success(1);
        $resp->set('found_photos', count($photos));
        $resp->set('photos', ob_get_clean());
    }

    /**
     * function for set photo as avatar
     */
	public function set_photo_as_avatar(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'profile-set-photo-profile')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('user_id');
            $photo_id = $this->_input->int('photo_id');
            $module_id = $this->_input->int('module_id', 0);

            $photos_model = new PeepSoPhotosModel();
            $photos = $photos_model->get_photo($photo_id);

            if (($photos != null) && count((array)$photos) > 0) {
                
                // if $owner == 0, set for current user id
                $owner = ($owner == 0) ? get_current_user_id() : $owner;

                if ((int) $photos->pho_owner_id === $owner || $module_id != 0) {
                    $user = PeepSoUser::get_instance($owner);
                    delete_user_meta($user->get_id(), 'peepso_use_gravatar');

                    // copy photo to avatar file
                    $src_avatar = $photos_model->get_photo_dir() . $photos->pho_file_name;

                    $user->move_avatar_file($src_avatar);

                    // do not post to stream when set avatar directly from any photos
                    $add_to_stream=FALSE;
                    $user->finalize_move_avatar_file($add_to_stream);

                    $resp->success(TRUE);
                    $resp->set('msg', __('Your avatar has been changed.', 'picso'));
                }
                else
                {
                    $resp->success(FALSE);
                    $resp->set('msg', __('You are not authorized to use this photo as avatar.', 'picso'));
                }
            }
            else
            {
                $resp->success(FALSE);
                $resp->set('msg', __('Photo not found', 'picso'));
            }
        }
    }

    /**
     * function for set photo as cover
     */
    public function set_photo_as_cover(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'profile-set-photo-profile')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('user_id');
            $photo_id = $this->_input->int('photo_id');
            $module_id = $this->_input->int('module_id', 0);

            $photos_model = new PeepSoPhotosModel();
            $photos = $photos_model->get_photo($photo_id);

            if (($photos != null) && count((array)$photos) > 0) {

                // if $owner == 0, set for current user id
                $owner = ($owner == 0) ? get_current_user_id() : $owner;

                if ((int) $photos->pho_owner_id === $owner || $module_id != 0) {
                    $user = PeepSoUser::get_instance($owner);

                    // copy photo to avatar file
                    $src_avatar = $photos_model->get_photo_dir() . $photos->pho_file_name;

                    // do not post to stream when set cover directly from any photos
                    $add_to_stream=FALSE;
                    $user->move_cover_file($src_avatar,$add_to_stream);

                    $resp->success(TRUE);
                    $resp->set('msg', __('Your profile cover has been changed.', 'picso'));
                }
                else
                {
                    $resp->success(FALSE);
                    $resp->set('msg', __('You are not authorized to use this photo as cover.', 'picso'));
                }
            }
            else
            {
                $resp->success(FALSE);
                $resp->set('msg', __('Photo not found', 'picso'));
            }
        }
    }

    /**
     * function create album
     */
    public function create_album(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'photo-create-album')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('user_id');
            $name = $this->_input->value('name', '', FALSE); // SQL safe
            $privacy = $this->_input->int('privacy', PeepSo::ACCESS_PUBLIC); // default privacy is public
            $description = $this->_input->raw('description');
            $files = $this->_input->value('photo', array(), FALSE); // SQL safe

            // module, extends for groups / page / events
            $module_id = $this->_input->int('module_id', 0);

            if (count($files) > 0) {

                if(get_current_user_id() === intval($owner) || intval($module_id) !== 0) {
                    // save photo and stream post to database
                    $_photos_model = new PeepSoPhotosModel();
                    $photos_album_model = new PeepSoPhotosAlbumModel();

                    // add capability for other plugins to override privacy of album
                    $privacy = apply_filters('peepso_photos_ajax_create_album_privacy', $privacy);

                    // create post
                    $content = $description;
                    $extra = array(
                        'module_id' => PeepSoSharePhotos::MODULE_ID,
                        'act_access' => $privacy,
                    );

                    // create post
                    $peepso_activity = PeepSoActivity::get_instance();
                    $post_id = $peepso_activity->add_post($owner, $owner, $content, $extra);
                    add_post_meta($post_id, PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE, PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE_ALBUM, true);
                    add_post_meta($post_id, PeepSoSharePhotos::POST_META_KEY_PHOTO_COUNT, count($files), true);

                    $author_id = get_current_user_id();
                    if($module_id !== 0) {
                        $author_id = apply_filters('peepso_photos_filter_owner_album', get_current_user_id());
                    }

                    // get album_id
                    $album_id = $photos_album_model->get_photo_album_id($author_id, PeepSoSharePhotos::ALBUM_CUSTOM, $post_id, $module_id);

                    $resp->success(TRUE);
                    $resp->set('album_id', $album_id);
                    $resp->set('msg', __('Album '.$name.' has been created.', 'picso'));
                }
                else
                {
                    $resp->success(FALSE);
                    $resp->set('msg', __('You are not authorized to create album for this user.', 'picso'));
                }
            }
            else
            {
                $resp->success(FALSE);
                $resp->set('msg', __('Photo not found', 'picso'));
            }
        }
    }

    /**
     * ‘Cancel’ button which removes the photos
     * Will delete temporary files
     */
    public function remove_temp_files(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'remove-temp-files')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {
            // remove temporary files
            $files = $this->_input->value('photo', array(), FALSE); // SQL safe

            $_photos_model = new PeepSoPhotosModel();
            $tmp_folder = $_photos_model->get_tmp_dir();

            $thumbs_folder = $_photos_model->get_thumbs_dir();
            $filetype = 'jpg'; // make sure thumbnail is always .jpg extensions

            foreach ($files as $file) {

                // Filesystem protection
                if(strstr($file,'..')) { continue; }

                $tmp_file = $tmp_folder . $file;
                if(file_exists($tmp_file)) {
                    unlink($tmp_file);
                }

                // remove thumbnails
                $filename = str_replace('.' . $filetype, '', $file);
                $thumb_settings = $_photos_model->get_thumb_settings();
                foreach($thumb_settings as $key => $settings) {

                    $thumb_file = $thumbs_folder . $filename ."_". $key . '.' . $filetype;

                    if(file_exists($thumb_file)) {
                        unlink($thumb_file);
                    }

                }
            }

            $resp->success(TRUE);
            $resp->set('msg', _n( 'Temporary file has been deleted', 'Temporary files has been deleted', count($files), 'picso' ));
        }
    }

    /**
     * Do add photos to album
     */
    public function add_photos_to_album(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'photo-add-to-album')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {
			$photos_album_model = new PeepSoPhotosAlbumModel();

            $owner = $this->_input->int('user_id');
            $files = $this->_input->value('photo', array(), FALSE); // SQL safe
			$album_id = $this->_input->int('album_id');

            // module, extends for groups / page / events
            $module_id = $this->_input->int('module_id', 0);

            $album_owner = apply_filters('peepso_photos_filter_owner_album', $owner);

			$album = $photos_album_model->get_photo_album($album_owner, $album_id, 0, $module_id);

            if (count($files) > 0 && ($album !== NULL)) {

                if(get_current_user_id() === intval($owner) || intval($module_id) !== 0) {

                    $privacy = $album[0]->pho_album_acc;
                    $post_id = $album[0]->pho_post_id;

                    // use existing post, don't create new post
                    $activity = new PeepSoActivity();
                    $_photos_model = new PeepSoPhotosModel();
                    $_post = $activity->get_activity_data($post_id, PeepSoSharePhotos::MODULE_ID);

                    if($_post !== NULL) {

                        // - update information of number uploaded photos for stream update
                        // - update post_date so the updates can bumped to the top

                        $_photos_model->save_images($files, $post_id, $_post->act_id, $album_id);
                        update_post_meta($post_id, PeepSoSharePhotos::POST_META_KEY_PHOTO_COUNT, count($files));

                        // Update post 37
                        $my_post = array(
                            'ID'           => $post_id,
                            'post_date'    => current_time('Y-m-d H:i:s',0),
                            'post_date_gmt' => current_time('Y-m-d H:i:s',1),
                            'post_status'  => 'publish'
                        );

                        // Update the post into the database
                        wp_update_post( $my_post );

                        $resp->success(TRUE);
                        $resp->set('msg', __('New photos added', 'picso'));
                    } else {
                        $resp->success(FALSE);
                        $resp->set('msg', __('Activity is not found.', 'picso'));
                    }
                }
                else
                {
                    $resp->success(FALSE);
                    $resp->set('msg', __('You are not authorized to add photos.', 'picso'));
                }
            }
            else
            {
                $resp->success(FALSE);
                $resp->set('msg', __('Photo not found', 'picso'));
            }
        }
    }

    /**
     * Do cancel add photos to album
     */
    public function canncel_add_photos_to_album(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'photo-add-to-album')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {
            // remove temporary files
            $files = $this->_input->value('photo', array(), FALSE); // SQL safe

            $_photos_model = new PeepSoPhotosModel();
            $tmp_folder = $_photos_model->get_tmp_dir();
            foreach ($files as $file) {
                // Filesystem protection
                if(strstr($file,'..')) { continue; }

                $tmp_file = $tmp_folder . $file;
                unlink($tmp_file);
            }

            $resp->success(TRUE);
            $resp->set('msg', _n( 'Temporary file has been deleted', 'Temporary files has been deleted', count($files), 'picso' ));
        }
    }

    /**
     * function delete album
     */
    public function delete_album(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'photo-delete-album')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('uid');
            $album_id = $this->_input->int('album_id');

            // module, extends for groups / page / events
            $module_id = $this->_input->int('module_id', 0);

            $album_owner = apply_filters('peepso_photos_filter_owner_album', $owner);

            if(get_current_user_id() === intval($owner) || (PeepSo::is_admin()) || intval($module_id) !== 0) {
                // delete activity
                $_photos_model = new PeepSoPhotosModel();
                $_activity = new PeepSoActivity();

                $photos = $_photos_model->get_user_photos_by_album($album_owner, $album_id, 0, 0, 'desc', $module_id);
                foreach ($photos as $photo) {
                    $_activity->delete_activity($photo->act_id);
                }

                // delete post
                $photos_album_model = new PeepSoPhotosAlbumModel();
                $album = $photos_album_model->get_photo_album($album_owner, $album_id, 0, $module_id);
                if(count($album)) {
                    $the_album = $album[0];
                    $_activity->delete_post($the_album->pho_post_id);
                }

                // delete album
                $photos_album_model->delete_album($album_owner, $album_id, $module_id);

                $resp->success(TRUE);
                $resp->set('msg', __('Photo Album has been deleted.', 'picso'));
            }
            else
            {
                $resp->success(FALSE);
                $resp->set('msg', __('You are not authorized to change this album name.', 'picso'));
            }
        }
    }

	/**
     * Save photo album name
     */
    public function set_album_name(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-album-name')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('user_id');
			$album_id = $this->_input->int('album_id');
			$name = $this->_input->value('name', '', FALSE); // SQL safe

            $can_edit = PeepSo::check_permissions($owner, PeepSo::PERM_POST_EDIT, get_current_user_id());
			if ($can_edit) {
				// save photo album name
				$photos_album_model = new PeepSoPhotosAlbumModel();
				$photos_album_model->set_photo_album_name($name, $album_id);

				$resp->success(TRUE);
				$resp->set('msg', __('Photo Album name saved.', 'picso'));
			}
			else
			{
				$resp->success(FALSE);
				$resp->set('msg', __('You are not authorized to change this album name.', 'picso'));
			}
        }
	}

	/**
     * Save photo album description
     */
    public function set_album_description(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-album-description')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $user_id = $this->_input->int('user_id');
            $owner = $this->_input->int('owner_id');
			$album_id = $this->_input->int('album_id');
			$description = $this->_input->value('description', '', FALSE); // SQL safe

            $can_edit = PeepSo::check_permissions($owner, PeepSo::PERM_POST_EDIT, get_current_user_id());
			if ($can_edit) {

                $description = htmlspecialchars($description);
                $description = substr(trim(PeepSoSecurity::strip_content($description)), 0, PeepSo::get_option('site_status_limit', 4000));

				// save photo album description
				$photos_album_model = new PeepSoPhotosAlbumModel();
				$photos_album_model->set_photo_album_description($description, $album_id);

                $the_album = $photos_album_model->get_album($album_id, $owner);
                $post_id = $the_album->pho_post_id;

                $filtered_content = apply_filters('peepso_activity_post_content', $description, $post_id);

                // Update post content on stream
                $album_post = array(
                    'ID'           => $post_id,
                    'post_content' => $filtered_content,
                    'post_excerpt' => $filtered_content,
                );

                // Update the post into the database
                // wp_update_post( $album_post );
                global $wpdb;
                $query = "UPDATE ".$wpdb->prefix."posts SET post_content='" . $filtered_content . "', post_excerpt='" . $description . "' WHERE ID = '" . $post_id . "'";
                $wpdb->query($query);

				$resp->success(TRUE);
				$resp->set('msg', __('Photo Album description saved.', 'picso'));
			}
			else
			{
				$resp->success(FALSE);
				$resp->set('msg', __('You are not authorized to change this album description.', 'picso'));
			}
        }
	}

    /**
     * Save photo album extra field value
     */
    public function set_album_extra_field(PeepSoAjaxResponse $resp)
    {
        $type = $this->_input->value('type_extra_field', '', FALSE); // SQL safe

        if(!empty($type)) {
            $save = apply_filters('peepso_photo_album_update_' . $type, array());

            if(!empty($save) && isset($save['success'])) {
                $resp->success($save['success']);
                if( isset($save['msg']) ) {
                    $resp->set('msg', $save['msg']);
                }

                if( isset($save['error']) ) {
                    $resp->error($save['error']);
                }
            }
        }
        else
        {
            $resp->success(FALSE);
            $resp->set('msg', __('Invalid extra field.', 'picso'));
        }


    }

	/**
     * Save photo album access
     */
    public function set_album_access(PeepSoAjaxResponse $resp)
    {
        // SQL safe, WP sanitizes it
        if (FALSE === wp_verify_nonce($this->_input->value('_wpnonce','',FALSE), 'set-album-access')) {
            $resp->success(FALSE);
            $resp->error(__('Request could not be verified.', 'picso'));
        } else {

            $owner = $this->_input->int('user_id');
			$album_id = $this->_input->int('album_id');
            $acc = $this->_input->int('acc');
            
            $can_edit = PeepSo::check_permissions($owner, PeepSo::PERM_POST_EDIT, get_current_user_id());
			if ($can_edit) {
				// save photo album access
				$photos_album_model = new PeepSoPhotosAlbumModel();
				$photos_album_model->set_photo_album_acc($acc, $album_id, $owner);

				$resp->success(TRUE);
				$resp->set('msg', __('Photo Album access changed.', 'picso'));
			}
			else
			{
				$resp->success(FALSE);
				$resp->set('msg', __('You are not authorized to change this album access.', 'picso'));
			}
        }
	}

    /**
     * todo:docblock
     */
    public function delete_stream_album(PeepSoAjaxResponse $resp) {

        $post_id = $this->_input->int('post_id');
        $user_id = $this->_input->int('uid');

        $_activity = PeepSoActivity::get_instance();

        $_post = $_activity->get_post($post_id);
        $post = $_post->post;
        // verify it's the current user AND they have ownership of the item
        if (get_current_user_id() == $user_id &&
			(PeepSo::check_permissions(intval($post->author_id), PeepSo::PERM_POST_DELETE, $user_id) ||
            PeepSo::check_permissions(intval($post->act_owner_id), PeepSo::PERM_POST_DELETE, $user_id))) {

            // Update post
            $my_post = array(
                'ID'           => $post_id,
                'post_status'  => 'pending'
            );

            // Update the post into the database
            $update = wp_update_post( $my_post );

            if (is_wp_error($update)) {
                $resp->success(FALSE);
                $resp->error($delete->get_error_message());
            } else {
                $resp->set('act_id', $_post->act_id);
                $resp->success(TRUE);
            }
        } else {
            $resp->success(FALSE);
            $resp->error(__('You do not have permission to do that.', 'picso'));
        }
    }
}

// EOF
