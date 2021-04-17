<?php
// album title
$title = (0 === intval($album->pho_system_album)) ? $album->pho_album_name : __($album->pho_album_name, 'picso');

// default thumbnail
$pho_thumb = PeepSo::get_asset('images/album/default.png');

// if a custom thumb exists
if(isset($album->cover_photo->pho_thumbs['m_s'])) {
	$pho_thumb = $album->cover_photo->pho_thumbs['m_s'];
}
?>
<div class="ps-albums__item-wrapper">
    <div class="ps-albums__item">
        <a title="<?php echo $title;?>" href="<?php echo $profile_url . 'photos/album/' . $album->pho_album_id;?>" data-id="<?php echo $album->pho_album_id; ?>">

            <img class="" src="<?php echo $pho_thumb;?>" title="" alt="<?php echo $title;?>" />

            <div class="ps-albums__item-overlay">
                <div class="ps-albums__item-title">
                    <?php echo $title;?>
                </div>

                <div class="ps-albums__item-details">
                    <?php
                    // @todo:num photo album
                    echo sprintf(_n( '%s photo', '%s photos', $album->num_photo, 'picso' ), $album->num_photo);
                    ?>

                    <div class="ps-albums__item-status">
                        <?php
                        switch (intval($album->pho_album_acc)) {
                            case PeepSo::ACCESS_PUBLIC:
                                # code...
                                $privacy = "<i class='ps-icon-globe' title=" . __('Public', 'picso') . "></i>";
                                break;

                            case PeepSo::ACCESS_MEMBERS:
                                # code...
                                $privacy = "<i class='ps-icon-users' title=" . __('Members only', 'picso') . "></i>";
                                break;

                            case PeepSo::ACCESS_PRIVATE:
                                # code...
                                $privacy = "<i class='ps-icon-lock' title=" . __('Private', 'picso') . "></i>";
                                break;

                            default:
                                # code...
								if (class_exists('PeepSoFriendsPlugin')) {
									$privacy = "<i class='ps-icon-user' title=" . __('Friends Only', 'picso') . "></i>";
								} else {
									$privacy = "<i class='ps-icon-globe' title=" . __('Public', 'picso') . "></i>";
								}
                                break;
                        }
						echo $privacy;
                        ?>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
