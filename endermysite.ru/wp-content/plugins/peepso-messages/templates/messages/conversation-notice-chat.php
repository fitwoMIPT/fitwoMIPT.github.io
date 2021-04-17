<div class="ps-comment-item ps-js-message ps-js-message-<?php echo $ID ?>" data-id="<?php echo $ID ?>">
    <div class="ps-alert reset-gap ps-alert-warning">
	    <em><?php
	    	$user = PeepSoUser::get_instance($post_author);
			if( 'left' == $post_content) {
				printf(__('%s has left the conversation', 'msgso'), $user->get_fullname());
			}

			if( 'new_group' == $post_content) {
				printf(__('%s created a new group conversation', 'msgso'), $user->get_fullname());
			}

	    ?></em>
    </div>
</div>
