<?php

echo $args['before_widget'];
$owner = PeepSoUser::get_instance($instance['user_id']);

?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
	<div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
		<a href="<?php echo $owner->get_profileurl();?>friends"><?php
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
		?></a>
	</div>
	<div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
		<div class="ps-widget--friends">
		<?php
			if ( count($instance['list']) )
			{
		?>
			<div class="ps-widget__friends">
				<?php foreach ($instance['list'] as $friend) { ?>
					<div class="ps-widget__friends-item">
						<div class="ps-avatar ps-avatar--full">
							<?php
							$friend = PeepSoUser::get_instance($friend['friendID']);
							printf('<a href="%s"><img alt="%s avatar" title="%s" src="%s" class="ps-name-tips"></a>',
								$friend->get_profileurl(),
								$friend->get_fullname(),
								$friend->get_fullname(),
								$friend->get_avatar()
							);
							?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php
			}
			else
			{
				echo "<span class='ps-text--muted'>" . __('No friends', 'friendso') . "</span>";
			}
		?>
		</div>
	</div>
</div>

<?php

echo $args['after_widget'];

// EOF
