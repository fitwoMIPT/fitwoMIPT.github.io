<?php

class PeepSoFriendsAjax extends PeepSoAjaxCallback
{
	private $_friendsrequests = NULL;

	protected function __construct()
	{
		parent::__construct();
		$this->_friendsrequests = PeepSoFriendsRequests::get_instance();
	}

	public function set_follow_status(PeepSoAjaxResponse $resp) {
        $passive_user_id = $this->_input->int('user_id');
        $follow = $this->_input->int('follow');

        $PeepSoUserFollower = new PeepSoUserFollower($passive_user_id, get_current_user_id(), TRUE);
        $PeepSoUserFollower->set('follow', $follow);

        ob_start();
        $PeepSoProfile = PeepSoProfile::get_instance();
        $PeepSoProfile->init($passive_user_id);
        $PeepSoProfile->profile_actions();
        $actions = ob_get_clean();

        $resp->set('success', TRUE);
        $resp->set('actions', $actions);
    }

	/**
	 * ajax callback - calls PeepSoFriendReqeuests::add()
	 * @param PeepSoAjaxResponse $resp
	 */
	public function send_request(PeepSoAjaxResponse $resp)
	{
		$to_id = $this->_input->int('user_id');

        delete_user_option($to_id, 'peepso_should_get_notifications');

		$from_id = get_current_user_id();
		$result = $this->_friendsrequests->add($from_id, $to_id);

		$resp->success($result);

		if ($result) {
			$peepso_friends = PeepSoFriendsPlugin::get_instance();
			$profile = PeepSoProfile::get_instance();
			$profile->init($to_id);

			$status = $peepso_friends->get_friend_status($from_id, $to_id);
			ob_start();
			$profile->profile_actions();
			$actions = ob_get_clean();

			$buttons_notif = '';
			if ( isset($status['cancel']) ) {
				$buttons_notif = $this->get_notif_button($status['cancel']);
			}

			$resp->set('header', __('Notice', 'friendso'));
			$resp->set('message', __('Friend Request Sent', 'friendso'));
			$resp->set('actions', $actions);
			$resp->set('buttons_notif', $buttons_notif);
		}
	}

	/**
	 * ajax callback - calls PeepSoFriendReqeuests::remove()
	 * @param PeepSoAjaxResponse $resp
	 */
	public function cancel_request(PeepSoAjaxResponse $resp)
	{
		$request_id = $this->_input->int('request_id');
		$action = $this->_input->value('action','remove',array('remove','ignore','ignore_block'));

		$request = $this->_friendsrequests->get_request($request_id);
		$result = $this->_friendsrequests->remove($request_id, $action);
		$resp->success($result);

		if ($result) {
			$peepso_friends = PeepSoFriendsPlugin::get_instance();
			$profile = PeepSoProfile::get_instance();

			$status = $peepso_friends->get_friend_status($request['freq_user_id'], $request['freq_friend_id']);
			ob_start();
			$profile->profile_actions();
			$actions = ob_get_clean();

			$buttons_notif = '';
			if ( isset($status['add']) ) {
				$buttons_notif = $this->get_notif_button($status['add']);
			}

			$resp->set('user_id', $request['freq_friend_id']);
			$resp->set('header', __('Notice', 'friendso'));
			$resp->set('message', apply_filters('peepso_friends_requests_cancel_request_notice-' . $action, __('Friend Request Cancelled', 'friendso')));
			$resp->set('actions', $actions);
			$resp->set('buttons_notif', $buttons_notif);
		}
	}

	/**
	 * ajax callback - calls PeepSoFriendReqeuests::accept()
	 * @param PeepSoAjaxResponse $resp
	 */
	public function accept_request(PeepSoAjaxResponse $resp)
	{
		$request_id = $this->_input->int('request_id');
		$request = $this->_friendsrequests->get_request($request_id);
		$from_id = $request['freq_user_id'];
		$to_id = $request['freq_friend_id'];

        delete_user_option($from_id, 'peepso_should_get_notifications');
        delete_user_option($to_id, 'peepso_should_get_notifications');

        $result = $this->_friendsrequests->accept($request_id);

		$resp->success($result);

		if ($result) {
			$peepso_friends = PeepSoFriendsPlugin::get_instance();
			$profile = PeepSoProfile::get_instance();
			$profile->init($from_id);

			$status = $peepso_friends->get_friend_status($to_id, $from_id);
			ob_start();
			$profile->profile_actions();
			$actions = ob_get_clean();

			$buttons_notif = '';
			if ( isset($status['unfriend']) ) {
				$buttons_notif = $this->get_notif_button($status['unfriend']);
			}

			// delete cache for birthday widgets
			$user_transient = array($from_id, $to_id);
			foreach ($user_transient as $user_transient_id) {
				$trans_upcoming_birthday = 'peepso_cache_widget_friendsupcomingbirthday_'. $user_transient_id;
	            $trans_save_date = 'peepso_cache_widget_friendsupcomingbirthday_savedate_'. $user_transient_id;
	            $trans_birthday = 'peepso_cache_widget_friendsbirthday_'. $user_transient_id;
	        	$trans_save_date_birthday = 'peepso_cache_widget_friendsbirthday_savedate_'. $user_transient_id;
	        	$trans_list_friends = 'peepso_cache_widget_friendslist_'. $user_transient_id;
            	$trans_save_date_list_friends = 'peepso_cache_widget_friendslist_savedate_'. $user_transient_id;

	        	delete_transient($trans_upcoming_birthday);
	        	delete_transient($trans_birthday);
	        	delete_transient($trans_list_friends);
	        	delete_transient($trans_save_date);
	        	delete_transient($trans_save_date_birthday);
	        	delete_transient($trans_save_date_list_friends);
			}

			$resp->set('user_id', $from_id);
			$resp->set('header', __('Notice', 'friendso'));
			$resp->set('message', __('Friend Request Accepted', 'friendso'));
			$resp->set('actions', $actions);
			$resp->set('buttons_notif', $buttons_notif);
		}
	}

	/**
	 * Ajax callback - calls PeepSoFriendsPlugin::delete()
	 * @param  PeepSoAjaxResponse $resp
	 */
	public function remove_friend(PeepSoAjaxResponse $resp)
	{
		$peepso_friends = PeepSoFriendsPlugin::get_instance();
		$from_id = get_current_user_id();
		$to_id = $this->_input->int('user_id');
		$success = $peepso_friends->model->delete($from_id, $to_id);

		if ($success) {
			do_action('friendso_user_unfriended', array('from'=>$from_id, 'to'=>$to_id));
			$profile = PeepSoProfile::get_instance();
			$profile->init($to_id);

			$PeepSoUserFollower = new PeepSoUserFollower($to_id, $from_id);
			$PeepSoUserFollower->set('follow', 0);
			
			$PeepSoUserFollower = new PeepSoUserFollower($from_id, $to_id);
        	$PeepSoUserFollower->set('follow', 0);

			$status = $peepso_friends->get_friend_status($from_id, $to_id);
			ob_start();
			$profile->profile_actions();
			$actions = ob_get_clean();

			$buttons_notif = '';
			if ( isset($status['add']) ) {
				$buttons_notif = $this->get_notif_button($status['add']);
			}

			// delete cache for birthday widgets
			$user_transient = array($from_id, $to_id);
			foreach ($user_transient as $user_transient_id) {
				$trans_upcoming_birthday = 'peepso_cache_widget_friendsupcomingbirthday_'. $user_transient_id;
	            $trans_save_date = 'peepso_cache_widget_friendsupcomingbirthday_savedate_'. $user_transient_id;
	            $trans_birthday = 'peepso_cache_widget_friendsbirthday_'. $user_transient_id;
	        	$trans_save_date_birthday = 'peepso_cache_widget_friendsbirthday_savedate_'. $user_transient_id;
	        	$trans_list_friends = 'peepso_cache_widget_friendslist_'. $user_transient_id;
            	$trans_save_date_list_friends = 'peepso_cache_widget_friendslist_savedate_'. $user_transient_id;

	        	delete_transient($trans_upcoming_birthday);
	        	delete_transient($trans_birthday);
	        	delete_transient($trans_list_friends);
	        	delete_transient($trans_save_date);
	        	delete_transient($trans_save_date_birthday);
	        	delete_transient($trans_save_date_list_friends);
			}

			$resp->set('header', __('Notice', 'friendso'));
			$resp->set('message', __('Friend Removed', 'friendso'));
			$resp->set('actions', $actions);
			$resp->set('buttons_notif', $buttons_notif);
		}
	}

	/**
	 * Used in the notification popup to render the list items.
	 */
	public function get_requests(PeepSoAjaxResponse $resp)
	{
		$notifications = array();

		if ($this->_friendsrequests->has_received_requests(get_current_user_id())) {
			while ($request = $this->_friendsrequests->get_next_request())
				$notifications[] = PeepSoTemplate::exec_template('friends', 'notification-popover-item', $request, TRUE);

			$resp->success(TRUE);
			$resp->set('notifications', $notifications);
		} else {
			$resp->success(FALSE);
			$resp->error(__('You currently have no friend requests', 'friendso'));
		}
	}

	/**
	 * Generates a dropdown options menu available to perform on a certain user based on their friend status.
	 */
	public function get_request_options(PeepSoAjaxResponse $resp)
	{
		// The target user
		$user_id = $this->_input->int('user_id');
		$current_user_id = get_current_user_id();

		$sc = PeepSoFriendsShortcode::get_instance();
		$resp->set('options', $sc->request_options($current_user_id, $user_id, FALSE));
		$resp->set('buttons', $sc->request_buttons($current_user_id, $user_id, FALSE));
		$resp->success(TRUE);
	}

    /**
     * GET
     * @param PeepSoAjaxResponse $resp
     * @return void
     */
    public function get_user_friends(PeepSoAjaxResponse $resp)
    {
        $owner = $this->_input->int('user_id');
        $page = $this->_input->int('page', 1);

        // default limit is 1 (NewScroll)
        $limit = $this->_input->int('limit', 1);

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $args=array(
            'offset'    => $offset,
            'number'    => $limit,
        );

        $friends_model =  new PeepSoFriendsModel();
        $friends = $friends_model->get_friends($owner, $args);

        ob_start();

        if (count($friends)) {
            foreach ($friends as $friend) {
                $friend = PeepSoUser::get_instance($friend);
				
				PeepSoFriendsShortcode::get_instance()->show_friend($friend);
            }

            $resp->success(1);
            $resp->set('found_friends', count($friends));
            $resp->set('friends', ob_get_clean());
        } else {
        	$message =  (get_current_user_id() == $owner) ? __('You have no friends yet', 'friendso') : sprintf(__('%s has no friends yet', 'friendso'), PeepSoUser::get_instance($owner)->get_firstname());
            $resp->error(PeepSoTemplate::exec_template('profile','no-results-ajax', array('message' => $message), TRUE));
		}


    }

    /**
     * Show mutual friends ajax
     */
    public function get_mutual_friends(PeepSoAjaxResponse $resp)
    {
        $owner = $this->_input->int('from_id');
        $friend = $this->_input->int('to_id');

        $page = $this->_input->int('page', 1);
        $sort = $this->_input->value('sort', 'desc', array('asc','desc'));

        $friends_per_page = 5;
        $offset = ($page - 1) * $friends_per_page;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $args=array(
            'offset'    => $offset,
            'number'    => $friends_per_page,
        );

        $friends_model =  new PeepSoFriendsModel();
        $friends = $friends_model->get_mutual_friends($owner, $friend, $args);

        ob_start();

        if (count($friends)) {
            foreach ($friends as $friend) {
                $friend = PeepSoUser::get_instance($friend['friendID']);

				PeepSoFriendsShortcode::get_instance()->show_friend($friend);
            }
        }

        $resp->success(1);
        $resp->set('title', __('Mutual Friends', 'friendso'));
        $resp->set('template', '<div class="ps-members-item-popup" style="max-height:410px;overflow:auto">##friends##</div><img src="' . PeepSo::get_asset('images/ajax-loader.gif') . '" alt="" style="display:none" />');
        $resp->set('found_friends', count($friends));
        $resp->set('friends', ob_get_clean());
    }

    private function get_notif_button($data)
    {
		$button = '';
		if ( isset($data) ) {
			$button .= '<button class="ps-btn ps-btn-small ps-button-action"';

			if (isset($data['extra']))
				$button .= ' ' . $data['extra'];
			if (isset($data['click']))
				$button .= ' onclick="' . esc_js($data['click']) . '" ';

			$button .= ' ">';

			if (isset($data['label']))
				$button .= '<span>' . $data['label'] . '</span>';
			if (isset($data['loading']))
				$button .= ' <img style="margin-left:2px;display:none" src="' . PeepSo::get_asset('images/ajax-loader.gif') .'" alt=""></span>';

			$button .= '</button>' . PHP_EOL;
		}

		return $button;
    }
}

// EOF
