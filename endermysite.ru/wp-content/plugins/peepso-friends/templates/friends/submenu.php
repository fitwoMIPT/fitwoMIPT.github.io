<?php
$PeepSoFriends = PeepSoFriends::get_instance();
?>
<select class="ps-select ps-full ps-js-friends-submenu" onchange="psfriends.select_menu(this);">
    <option value="friends" <?php if('friends' === $current) echo 'selected' ?> data-url="<?php echo PeepSoFriendsPlugin::get_url(get_current_user_id(), 'friends'); ?>">
        <?php echo __('Friends', 'friendso'); ?>
        (<?php echo $PeepSoFriends->get_num_friends(get_current_user_id()); ?>)
    </option>
    <option value="received-request"<?php if('requests' === $current) echo 'selected' ?> data-url="<?php echo PeepSoFriendsPlugin::get_url(get_current_user_id(), 'requests'); ?>"><?php echo __('Friend requests', 'friendso'); ?></option>
</select>

&nbsp;
