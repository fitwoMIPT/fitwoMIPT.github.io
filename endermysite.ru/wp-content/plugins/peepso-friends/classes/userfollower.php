<?php

class PeepSoUserFollower {

    const TABLE = 'peepso_user_followers';

    public $passive_user_id;
    public $active_user_id;

    public $is_follower = 0; // indicated if the DB record exists

    public $follow      = 0;
    public $notify      = 0;
    public $email       = 0;

    private $_table;

    public function __construct($passive_user_id, $active_user_id = NULL, $create = FALSE) {

        if(NULL == $active_user_id) {
            $active_user_id = get_current_user_id();
        }

        global $wpdb;

        $this->passive_user_id = intval($passive_user_id);
        $this->active_user_id = intval($active_user_id);

        $this->_table = $wpdb->prefix.PeepSoUserFollower::TABLE;


        if( $this->active_user_id > 0 && $this->passive_user_id > 0) {
            $this->init($create);
        }
    }

    public function init($create = FALSE)
    {
        // Reset all flags
        $this->is_follower  = 0;
        $this->follow       = 0;
        $this->notify		= 0;
        $this->email        = 0;

        // Calculate flags based on the database state
        global $wpdb;

        $query = "SELECT * FROM $this->_table WHERE `uf_passive_user_id`=%d AND `uf_active_user_id`=%d LIMIT 1";
        $query = $wpdb->prepare($query, array($this->passive_user_id, $this->active_user_id));

        $follower = $wpdb->get_row($query);

        if (NULL !== $follower) {

            $this->is_follower  = 1;
            $this->notify		= $follower->uf_notify;
            $this->email        = $follower->uf_email;

            if ($create) {
                $this->follow = 1;
            } else {
                $this->follow = $follower->uf_follow;
            }

            $this->save();

        } else {
            $PeepSoFriendsModel = new PeepSoFriendsModel();
            if(TRUE == $create || $PeepSoFriendsModel->are_friends($this->active_user_id, $this->passive_user_id)) {
                $query = "INSERT INTO $this->_table (`uf_active_user_id`, `uf_passive_user_id`, `uf_follow`) VALUES (%d, %d, 1)";
                $wpdb->query($wpdb->prepare($query, $this->active_user_id, $this->passive_user_id));

                $this->is_follower  = 1;
                $this->follow       = 1;
                $this->notify		= 0;
                $this->email        = 0;
            }
        }
    }

    public function set($prop, $value) {
        if(!$this->is_follower || !property_exists($this, $prop)) {
            return NULL;
        }

        $this->$prop = $value;

        return $this->save();
    }

    public function save() {
        if(!$this->is_follower) {
            return NULL;
        }

        global $wpdb;
        return $wpdb->update($this->_table, array( 'uf_follow'=>$this->follow, 'uf_notify'=>$this->notify,'uf_email'=>$this->email), array('uf_passive_user_id'=>$this->passive_user_id, 'uf_active_user_id'=>$this->active_user_id) );
    }
}