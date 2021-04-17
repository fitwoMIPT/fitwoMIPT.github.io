<?php
$PeepSoGroupUsers = new PeepSoGroupUsers($group->id);
$PeepSoGroupUsers->update_members_count('banned');
$PeepSoGroupUsers->update_members_count('pending_user');
$PeepSoGroupUsers->update_members_count('pending_admin');
?>
<div class="ps-tabs__wrapper">
        <div class="ps-tabs ps-tabs--arrows">

            <div class="ps-tabs__item <?php if (!$tab) echo "current"; ?>"><a
                        href="<?php echo $group->get_url() . 'members/'; ?>"><?php echo __('All Members', 'groupso'); ?></a>
            </div>

            <div class="ps-tabs__item <?php if ('management'==$tab) echo "current"; ?>"><a
                        href="<?php echo $group->get_url() . 'members/management'; ?>"><?php echo __('Management', 'groupso'); ?></a>
            </div>

            <?php if($PeepSoGroupUser->can('manage_users')) { ?>

                <div class="ps-tabs__item <?php if ('invited' == $tab) echo "current"; ?>"><a
                        href="<?php echo $group->get_url() . 'members/invited'; ?>"><?php echo sprintf(__('Invited (<span class="ps-js-invited-count" data-id="%d">%s</span>)', 'groupso'), $group->id, $group->pending_user_members_count); ?></a>
                </div>

                <div class="ps-tabs__item <?php if ('pending' == $tab) echo "current"; ?>"><a
                        href="<?php echo $group->get_url() . 'members/pending'; ?>"><?php echo sprintf(__('Pending (<span class="ps-js-pending-count" data-id="%d">%s</span>)', 'groupso'), $group->id, $group->pending_admin_members_count); ?></a>
                </div>

                <div class="ps-tabs__item <?php if ('banned' == $tab) echo "current"; ?>"><a
                            href="<?php echo $group->get_url() . 'members/banned'; ?>"><?php echo sprintf(__('Banned (%s)', 'groupso'), $group->banned_members_count); ?></a>
                </div>

            <?php } ?>
        </div>
</div>
