<?php

class PeepSoMessages
{
	private static $_instance = NULL;

	const MAX_MSG_PREVIEW_LEN = 100;

	public $_messages_model = NULL;
	private $_messages = array();

	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->_messages_model = new PeepSoMessagesModel();
	}

	/**
	 * Retrieve singleton class instance
	 * @return PeepSoMessage instance
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}

	/**
	 * Echoes the available bulk actions.
	 * @param  string $type inbox|sent
	 */
	public function display_bulk_actions($type = 'inbox')
	{
		$bulk_actions = array(
			'' => __('Bulk Actions', 'msgso'),
			'markread' => __('Mark as Read', 'msgso'),
			'markunread' => __('Mark as Unread', 'msgso'),
			'delete' => __('Archive', 'msgso'),
		);

		if ('sent' === $type) {
			unset($bulk_actions['markread']);
			unset($bulk_actions['markunread']);
		}

		echo '<select name="action" class="ps-select ps-select-small">';

		foreach ($bulk_actions as $value => $text)
			echo '<option value="', $value, '">', $text, '</option>', PHP_EOL;

		echo '</select>';
	}

	/**
	 * Displays the number of users in a conversation.
	 */
	public function display_participant_summary()
	{
		global $post;

		$model = new PeepSoMessagesModel();
		$root = $model->get_root_conversation($post);

		$peepso_participants = new PeepSoMessageParticipants();
		$participants = $peepso_participants->get_participants($root, get_current_user_id());
		$num_participants = count($participants) - 1;

		$current_user_id = get_current_user_id();

		$participants_link = '<a href="#" onclick="ps_messages.show_long_participants(); return false;">' . ($num_participants-1) . _n(' other', ' others', $num_participants, 'msgso') . '</a>';

		$first_participant = '';
		$long_participants = '';
		$ctr = 1;
		foreach ($participants as $participant_user_id) {
			if ($current_user_id === intval($participant_user_id)) {
				continue;
			}
				if (++$ctr !== count($participants)) {
					if(strlen($first_participant)) {
						$long_participants .= ', ';
					} else {
					}
				} else {
					$long_participants .= ' ' . __('and', 'msgso') . ' ';
				}

				if (1 === $num_participants) {
					$long_participants = '';
				}

                $participant = PeepSoUser::get_instance($participant_user_id);

				ob_start();
	            do_action('peepso_action_render_user_name_before', $participant->get_id());
	            $before_fullname = ob_get_clean();

	            ob_start();
	            do_action('peepso_action_render_user_name_after', $participant->get_id());
	            $after_fullname = ob_get_clean();


				if( !strlen( $first_participant ) ) {
					$first_participant.= '<a href="' . $participant->get_profileurl() . '">' . $before_fullname . $participant->get_fullname() . $after_fullname . '</a>';
				} else {
					$long_participants .= '<a href="' . $participant->get_profileurl() . '">' . $before_fullname . $participant->get_fullname() . $after_fullname . '</a>';
				}
		}

		if (1 === $num_participants) {
			$participants_link = $long_participants;
		}

		$summary_string = sprintf(
			__('%1$s and %2$s', 'msgso'),
			$first_participant,
			$participants_link);

		$long_string = sprintf(
			__('%1$s%2$s', 'msgso'),
			$first_participant,
			$long_participants
		);

		if( strlen($long_string < 75 )) {
			echo '<span id="long-participants">', $long_string ,'</span>';
 		} else {
			echo '<span id="summary-participants">', $summary_string, '</span>';
			echo '<span id="long-participants" style="display: none;">', $long_participants, '</span>';
		}
	}

	/**
	 * Template tag - displays the pagination("n-n of n") text.
	 */
	public function display_totals()
	{
		$stats = $this->_messages_model->display_totals();

		echo sprintf(__('<b>%1$d</b>-<b>%2$d</b> of <b>%3$d</b>', 'msgso'), $stats['offset'] + 1, $stats['max'], $stats['total']);
	}

	/**
	 * Returns the appropriate title for a conversation. Gets the post title if it's the parent message,
	 * else it returns the message.
	 * @return string
	 */
	public function get_conversation_title()
	{
		global $post;

		if (0 == $post->post_parent && FALSE === empty($post->post_title))
			return (strip_tags($post->post_title));

		$content = $post->post_content;
		$content = strip_tags($content);
		$content = apply_filters('peepso_remove_shortcodes', $content);

		if (strlen($content) > self::MAX_MSG_PREVIEW_LEN) {
		 	$content = (substr($content, 0, self::MAX_MSG_PREVIEW_LEN)) . '&hellip;';
		}

		$content_extra = apply_filters('peepso_post_extras', array());

		$content = $content . ' ' . implode(' ', $content_extra);

		$content = str_replace(array('<a','</a'), array('<span','</span'), $content);

		if(!strlen($content)) {
			$content = "(no text)";
		}

		return ($content);
	}

	/**
	 * Generate URL for delete message
	 * @return string URL
	 */
	public function get_delete_message_url()
	{
		global $post;

		return (wp_nonce_url(PeepSo::get_page('messages') . '?delete/' . $post->ID, 'delete-message', '_wpnonce'));
	}

	/**
	 * Generate URL for leave conversation message
	 * @return string URL
	 */
	public function get_leave_conversation_url()
	{
		global $post;

		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($post);

		return (wp_nonce_url(PeepSo::get_page('messages') . '?leave/' . $parent_id, 'leave-conversation', '_wpnonce'));
	}

	/**
	 * Echoes the avatar URL of the user sending the message or the one receiving it.
	 * @param  int $post_author The user ID.
	 * @param  int $post_id The message ID.
	 */
	public function get_message_avatar($args)
	{
		$user = PeepSoUser::get_instance($args['post_author']);
		$current_user_id = get_current_user_id();
		$avatars = array();
		// If message is 'sent'
		if ($current_user_id === intval($args['post_author'])) {
			$model = new PeepSoMessagesModel();
			// Get first participant and echo that
			$message_id = intval($args['post_id']);
			$parent_id = $model->get_root_conversation($message_id);

			$peepso_participants = new PeepSoMessageParticipants();
			$participants = $peepso_participants->get_participants($parent_id);
			if (count($participants) > 2) {
				$avatars[] = PeepSoUser::get_instance($args['post_author'])->get_avatar();
			} else {
				foreach ($participants as $participant_user_id) {
					if ($current_user_id !== intval($participant_user_id)) {
						$avatars[] = PeepSoUser::get_instance($participant_user_id)->get_avatar();
						break;
					}
				}	
			}
		} else {
			$avatars[] = PeepSoUser::get_instance($args['post_author'])->get_avatar();
		}

		foreach ($avatars as $avatar)
			echo '<img class="cavatar" src="'.$avatar.'" alt="'.esc_attr(sprintf(__('Message from %s', 'msgso'), trim(strip_tags($user->get_fullname())))).'">';
	}

	/**
	 * Template tag - used to display all conversations.
	 */
	public function get_message_list($type = 'inbox')
	{
		return (PeepSoTemplate::exec_template(
			'messages',
			'message-list',
			array(
				'query' => $this->_messages_model->get_query(),
				'total' => $this->_messages_model->get_total(),
				'type' => $type
			),
			TRUE
		)
		);
	}

	/**
	 * Returns the message url
	 * @param  mixed $message A post ID or post object
	 * @return string
	 */
	public function get_message_url($message = NULL)
	{
		global $post;

		if (is_null($message))
			$message = $post;

		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($message);

		return (PeepSo::get_page('messages') . '?view/' . $parent_id . '/');
	}

	/**
	 * Returns the id of parent (root) conversation
	 * @param  mixed $message A post ID or post object
	 * @return string
	 */
	public function get_root_conversation($message = NULL)
	{
		global $post;

		if (is_null($message)) {
			$message = $post;
		}

		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($message);

		return $parent_id;
	}

	/**
	 * Iterates through the $_messages ArrayObject and returns the current
	 * @param  int $user_id
	 * @return PeepSoUser
	 */
	public function get_next_message($user_id = NULL)
	{
		global $post;

		if (is_null($this->_messages))
			$this->_messages = $this->_messages_model->get_messages('inbox', $user_id);

		if ($this->get_iterator()->valid()) {
			$message = $this->get_iterator()->current();
			$post = $message;
			setup_postdata($post);

			$this->get_iterator()->next();
			return ($message);
		}

		return (FALSE);
	}

	/**
	 * Loops through the WP_Query object and sets up the current message to the global $post variable.
	 * @return boolean Returns TRUE until it reaches the end of the loop wherein it returns FALSE.
	 */
	public function get_next_message_in_conversation()
	{
		while ($this->_messages_in_conversation->have_posts()) {
			if ($this->_messages_in_conversation->current_post >= $this->_messages_in_conversation->post_count)
				return (FALSE);

			$this->_messages_in_conversation->the_post();
			return (TRUE);
		}

		$this->_messages_in_conversation = NULL;
		return (FALSE);
	}

	/**
	 * Echoes the display name of the user sending the message or the one receiving it.
	 * @param  int $post_author The user ID.
	 * @param  int $post_id The message ID.
	 */
	public function get_recipient_name($args, $current_user_id = NULL)
	{
		if ($current_user_id === NULL) {
			$current_user_id = get_current_user_id();
		}

		$post_author = intval($args['post_author']);
		$message_id = intval($args['post_id']);
		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($message_id);

		$peepso_participants = new PeepSoMessageParticipants();
		$participants = $peepso_participants->get_participants($parent_id, $current_user_id);

		foreach ($participants as $participant_user_id) {

			if($participant_user_id != $current_user_id) {
				$user = PeepSoUser::get_instance($participant_user_id);
				break;
			}
		}

		$in_conversation = count($participants) - 1;

		if ($in_conversation > 1) {
			$and_others = $in_conversation-1;
			ob_start();
            do_action('peepso_action_render_user_name_before', $user->get_id());
            $before_fullname = ob_get_clean();

            ob_start();
            do_action('peepso_action_render_user_name_after', $user->get_id());
            $after_fullname = ob_get_clean();

			printf(_n('%s and %d other','%s and %d others', $and_others, 'msgso'), $before_fullname . $user->get_fullname() . $after_fullname, $and_others);
		}
		else {
			if(!isset($user) || !is_object($user)) {
				$user = PeepSoUser::get_instance($args['post_author']);
			}
			//[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
			do_action('peepso_action_render_user_name_before', $user->get_id());

			echo $user->get_fullname();

			//[peepso]_[action]_[WHICH_PLUGIN]_[WHERE]_[WHAT]_[BEFORE/AFTER]
			do_action('peepso_action_render_user_name_after', $user->get_id());
		}
	}

	public function get_last_author_name($args)
	{
		$current_user_id = get_current_user_id();
		$post_author = intval($args['post_author']);
		$message_id = intval($args['post_id']);
		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($message_id);

		$peepso_participants = new PeepSoMessageParticipants();
		$participants = $peepso_participants->get_participants($parent_id, $current_user_id);

		$in_conversation = count($participants) -1;

		if($current_user_id == $post_author) {
			echo __('You: ', 'msgso');
		} else if ($in_conversation > 1) {
			$author = PeepSoUser::get_instance($post_author);
			echo $author->get_firstname();
			echo ": ";
			return;
		}

	}

	/**
	 * Returns TRUE|FALSE whether the current user has messages.
	 * @param  string $type inbox|sent
	 * @return boolean
	 */
	public function has_messages($type = 'inbox')
	{
		return ($this->_messages_model->has_messages($type));
	}

	/**
	 * Return TRUE|FALSE if there are messages in a conversation.
	 * @param  int  $message_id A message/post ID
	 * @return boolean
	 */
	public function has_messages_in_conversation( $args = array() )
	{
		$msg_id		= isset($args['msg_id']) 	? $args['msg_id'] 		: 0;
		$from_id 	= isset($args['from_id']) 	? $args['from_id'] 		: 0;
		$direction	= isset($args['direction']) ? $args['direction'] 	: NULL;



		$model = new PeepSoMessagesModel();

		$this->_messages_in_conversation = $model->get_messages_in_conversation($msg_id, get_current_user_id(), $from_id, $direction);

		return ($this->_messages_in_conversation->have_posts());
	}

	/**
	 * Shows a single message.
	 * @param  WP_Post $message A WP_Post object with a post type of peepso-message.
	 */
	public function show_message($message)
	{
		add_filter('peepso_photos_photo_click', array(&$this, 'photo_click'), 10, 2);
		PeepSoTemplate::exec_template('messages', 'message-list-item', (array) $message);
		remove_filter('peepso_photos_photo_click', array(&$this, 'photo_click'));
	}

	/**
	 * Template tag - displays a single message from a conversation.
	 * @param  WP_Post $message A WP_Post object of post type peepso-message
	 */
	public function show_message_in_conversation($message = NULL)
	{
		global $post;

		if (!is_null($message)) {
			$post = $message;
			setup_postdata($post);
		}

		$easter_eggs = array(
			'live long and prosper' => 'llap.png',
			'may the force be with you' => 'mtfbwy.png',
		);

		foreach($easter_eggs  as $key => $egg) {
			if(stristr($post->post_content, $key)) {
				$post->post_content = '<img style="height:24px;display:inline-block;" src="'.PeepSo::get_asset('images/'.$egg).'" alt="Surprise!" /> '.$post->post_content;
			}
		}

		if (PeepSoMessagesPlugin::CPT_MESSAGE === $post->post_type) {
			add_filter('peepso_activity_content', array('PeepSoSecurity', 'strip_content'));
			add_filter('peepso_photos_photo_click', array(&$this, 'photo_click'), 10, 2);
			add_filter('peepso_photos_max_visible_photos', array(&$this, 'max_visible_photos'), 10, 2);
			add_filter('peepso_photos_get_template', array(&$this, 'photo_template'), 10, 2);
			PeepSoTemplate::exec_template('messages', 'conversation-message', (array) $post);
			remove_filter('peepso_photos_get_template', array(&$this, 'photo_template'));
			remove_filter('peepso_photos_max_visible_photos', array(&$this, 'max_visible_photos'));
			remove_filter('peepso_photos_photo_click', array(&$this, 'photo_click'));
			remove_filter('peepso_activity_content', array('PeepSoSecurity', 'strip_content'));
		} else if (PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE === $post->post_type) {
			if (
					PeepSoMessagesPlugin::MESSAGE_INLINE_LEFT_CONVERSATION === $post->post_content
				|| 	PeepSoMessagesPlugin::MESSAGE_INLINE_NEW_GROUP === $post->post_content
			)
				PeepSoTemplate::exec_template('messages', 'conversation-notice', (array) $post);
		}
	}

	/**
	 * Template tag - displays a single message from a conversation.
	 * @param  WP_Post $message A WP_Post object of post type peepso-message
	 */
	public function show_message_in_conversation_chat($message = NULL)
	{
		global $post;
		$PeepSoActivity = new PeepSoActivity();

		if (!is_null($message)) {
			$post = $message;
			setup_postdata($post);
		}

		$easter_eggs = array(
			'live long and prosper' => 'llap.png',
			'may the force be with you' => 'mtfbwy.png',
		);

		foreach($easter_eggs  as $key => $egg) {
			if(stristr($post->post_content, $key)) {
				$post->post_content = '<img style="height:24px;display:inline-block;" src="'.PeepSo::get_asset('images/'.$egg).'" alt="Surprise!" /> '.$post->post_content;
			}
		}

		if (PeepSoMessagesPlugin::CPT_MESSAGE === $post->post_type) {
			add_filter('peepso_activity_content', array('PeepSoSecurity', 'strip_content'));
			add_filter('peepso_photos_photo_click', array(&$this, 'photo_click'), 10, 2);
			add_filter('peepso_photos_max_visible_photos', array(&$this, 'max_visible_photos'), 10, 2);
			add_filter('peepso_photos_get_template', array(&$this, 'photo_template'), 10, 2);
			PeepSoTemplate::exec_template('messages', 'conversation-message-chat', (array) $post);
			remove_filter('peepso_photos_get_template', array(&$this, 'photo_template'));
			remove_filter('peepso_photos_max_visible_photos', array(&$this, 'max_visible_photos'));
			remove_filter('peepso_photos_photo_click', array(&$this, 'photo_click'));
			remove_filter('peepso_activity_content', array('PeepSoSecurity', 'strip_content'));
		} else if (PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE === $post->post_type) {
			if (PeepSoMessagesPlugin::MESSAGE_INLINE_LEFT_CONVERSATION === $post->post_content)
				PeepSoTemplate::exec_template('messages', 'conversation-notice-chat', (array) $post);
		}
	}

	public function get_message_object( $message = NULL )
	{
		global $post;

		return $post;
	}

	/**
	 * Get message iterator
	 * @return object ArrayObject iterator
	 */
	private function get_iterator()
	{
		return ($this->_messages_model->get_iterator());
	}

	/**
	 * Displays the post content of the message
	 */
	public function show_message_preview()
	{
		$message = strip_tags(get_the_content());

		if (strlen($message) > self::MAX_MSG_PREVIEW_LEN)
			echo substr($message, 0, self::MAX_MSG_PREVIEW_LEN), '&hellip;';
		else
			echo $message;
	}

	/**
	 * Calls the `peepso_messages_after_conversation_title` action
	 */
	public function after_message_title()
	{
		global $post;
		do_action('peepso_messages_after_conversation_title', $post);
	}

	/**
	 * Disables the modal comments for message photos
	 * @param  string $onclick The onclick string to be used for the photo link
	 * @param  object $photo The photo to use
	 * @return string
	 */
	public function photo_click($onclick, $photo)
	{
		$location = $photo->location;
		$location = str_replace('thumbs/', '', $location);
		$location = str_replace(array('_s_s.', '_m_s.', '_l.'), '.', $location);
		return 'return (ps_messages.open_image(\'' . $location . '\'));';
	}

	/**
	 * Change template for photo post
	 */
	public function photo_template()
	{
		return array('messages', 'show-photos');
	}


	/**
	 * Re-set maximum visible photos in message item
	 * @param  int $max_photos Current maximum visible photos setting
	 * @return int
	 */
	public function max_visible_photos($max_photos)
	{
		return 1000;
	}

	/**
	 * Displays the post content of the message
	 */
	public function show_photo($photo)
	{
		PeepSoTemplate::exec_template('messages', 'show-photo', (array)$photo);
	}

}

// EOF
