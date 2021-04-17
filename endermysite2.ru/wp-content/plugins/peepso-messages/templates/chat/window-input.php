<div>
	<textarea placeholder="" />
	<?php if ( count($addons) > 0 ) { ?>
	<div class="ps-chat-input-addons" style="position:absolute; top:0; right:0; bottom:0; padding:5px 7px">
		<?php foreach( $addons as $addon ) { ?>
		<?php echo $addon; ?>
		<?php } ?>
	</div>
	<?php } ?>
</div>
