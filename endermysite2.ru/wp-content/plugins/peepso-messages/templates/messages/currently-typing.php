<div class="ps-chat__item">
	<div class="ps-chat__item-inner">
        <div class="ps-chat__avatar">
            <div class="ps-avatar ps-avatar--chat">
				<a href="#">
					<img src="<?php echo $user->get_avatar();?>"
						 alt="<?php echo $user->get_fullname();?>"
						 title="<?php echo $user->get_fullname();?>">
				</a>
            </div>
        </div>
        <div class="ps-chat__message">
            <div class="ps-chat__meta">
                <div class="ps-chat__user">
                    <span><?php echo $user->get_fullname();?></span>
                </div>
            </div>

            <div class="ps-chat__bubble-wrapper">
				<!-- CSS typing indicator -->
				<div class="ps-typing-indicator">
					<span></span>
					<span></span>
					<span></span>
				</div>
            </div>
        </div>
	</div>
</div>