<?php
$PeepSoFriendsRequests = PeepSoFriendsRequests::get_instance();
?>
<div class="peepso ps-page-profile">
    <?php PeepSoTemplate::exec_template('general', 'navbar'); ?>
    <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'friends')); ?>

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">

            <?php
            PeepSoTemplate::exec_template('friends', 'submenu', array('current'=>'requests'));
            ?>

            <?php PeepSoTemplate::exec_template('general', 'register-panel'); ?>

            <div class="tab-content">
                <div class="tab-pane active" id="received">
                    <?php
                        if ($PeepSoFriendsRequests->has_received_requests(get_current_user_id())) {
                            echo '<div class="ps-members">';
                            while ($request = $PeepSoFriendsRequests->get_next_request()) {
                                ?>
                                <div class="ps-members-item-wrapper">
                                  <div id="freq-<?php echo $request['freq_id'];?>" class="ps-members-item">
                                      <?php $PeepSoFriendsRequests->show_request_thumb($request);?>
                                  </div>
                                </div>
                            <?php
                            }
                            echo '</div>';
                        } else {
                            echo __('You currently have no friend requests', 'friendso');
                        }
                    ?>
                </div>

                <div class="tab-pane" id="sent">
                    <?php
                        if ($PeepSoFriendsRequests->has_sent_requests(get_current_user_id())) {
                            echo '<div class="ps-members">';
                            while ($request = $PeepSoFriendsRequests->get_next_request()) {
                                ?>
                                <div class="ps-members-item-wrapper">
                                    <div id="freq-<?php echo $request['freq_id'];?>" class="ps-members-item">
                                        <?php $PeepSoFriendsRequests->show_request_thumb($request);?>
                                    </div>
                                </div>
                            <?php
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
