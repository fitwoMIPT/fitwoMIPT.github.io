<div class="ps-widget__photos-item ps-js-photo" data-post-id="<?php echo $pho_post_id; ?>">
	<a data-id="<?php echo $act_id; ?>" href="#" rel="post-<?php echo $pho_post_id;?>"
			onclick="ps_comments.open(<?php echo $pho_id ?>, 'photo', { nonav: 1 }); return false;">
		<img src="<?php echo $pho_thumbs['s_s']; ?>" alt="<?php echo $pho_orig_name;?>" />
	</a>
</div>
