<?php

if(class_exists('PeepSoMaintenanceFactory')) {
    class PeepSoMaintenanceFriends extends PeepSoMaintenanceFactory
    {
        /**
         * Rebuild missing user_followers records based on friends table
         */
        public static function rebuildFollowers($limit = 5)
        {

            if (!strlen(get_transient('peepso_user_followers_synced'))) {

                global $wpdb;

                $r = $wpdb->get_results("SELECT `fnd_user_id` as ua, `fnd_friend_id` as ub FROM " . $wpdb->prefix . 'peepso_friends' . " fnd WHERE NOT EXISTS (SELECT * FROM " . $wpdb->prefix . 'peepso_user_followers' . " WHERE uf_active_user_id=fnd.fnd_user_id AND uf_passive_user_id=fnd.fnd_friend_id) LIMIT 0,$limit");

                $i = 0;
                if (count($r)) {
                    foreach ($r as $f) {
                        new PeepSoUserFollower($f->ua, $f->ub);
                        new PeepSoUserFollower($f->ub, $f->ua);
                        $i++;
                    }
                }

                // just in case switch user_a and user_b and active in WHERE clause to rebuild the relation in the opposite direction as well
                $r = $wpdb->get_results("SELECT `fnd_user_id` as ub, `fnd_friend_id` as ua FROM " . $wpdb->prefix . 'peepso_friends' . " fnd WHERE NOT EXISTS (SELECT * FROM " . $wpdb->prefix . 'peepso_user_followers' . " WHERE uf_active_user_id=fnd.fnd_friend_id AND uf_passive_user_id=fnd.fnd_user_id) LIMIT 0,$limit");

                if (count($r)) {
                    foreach ($r as $f) {
                        new PeepSoUserFollower($f->ua, $f->ua);
                        new PeepSoUserFollower($f->ub, $f->ub);
                        $i++;
                    }
                }

                // Pause recount for a while if nothing found
                if (0 == $i) {
                    set_transient('peepso_user_followers_synced', 1, 900);
                }

                return $i;
            }
        }

        public static function cleanupFriendships()
        {
            global $wpdb;
            $wpdb->query("DELETE FROM `$wpdb->prefix" . PeepSoFriendsPlugin::TABLE . "` WHERE `fnd_user_id` NOT IN (SELECT ID FROM `$wpdb->users`)");            
            $wpdb->query("DELETE FROM `$wpdb->prefix" . PeepSoFriendsPlugin::TABLE . "` WHERE `fnd_friend_id` NOT IN (SELECT ID FROM `$wpdb->users`)");            
            $wpdb->query("DELETE FROM `$wpdb->prefix" . PeepSoFriendsRequests::TABLE . "` WHERE `freq_user_id` NOT IN (SELECT ID FROM `$wpdb->users`)");            
            $wpdb->query("DELETE FROM `$wpdb->prefix" . PeepSoUserFollower::TABLE . "` WHERE `uf_passive_user_id` NOT IN (SELECT ID FROM `$wpdb->users`)");            
            $wpdb->query("DELETE FROM `$wpdb->prefix" . PeepSoUserFollower::TABLE . "` WHERE `uf_active_user_id` NOT IN (SELECT ID FROM `$wpdb->users`)");            
        }
    }
}

//global $wpdb;
//
//// Delete friendships
//
//$sql = 'DELETE FROM `' . $wpdb->prefix . self::TABLE . '` WHERE `fnd_user_id`=%d';
//$wpdb->query($wpdb->prepare($sql, $id));
//
//
//$sql = 'DELETE FROM `' . $wpdb->prefix . self::TABLE . '` WHERE `fnd_friend_id`=%d';
//$wpdb->query($wpdb->prepare($sql, $id));
//
//
//// Clean up friend requests, both sent and received
//
//$sql = 'DELETE FROM `' . $wpdb->prefix . PeepSoFriendsRequests::TABLE . '` WHERE `freq_user_id`=%d';
//$wpdb->query($wpdb->prepare($sql, $id));
//
//$sql = 'DELETE FROM `' . $wpdb->prefix . PeepSoFriendsRequests::TABLE . '` WHERE `freq_friend_id`=%d';
//$wpdb->query($wpdb->prepare($sql, $id));