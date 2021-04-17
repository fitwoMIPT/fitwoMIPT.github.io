<div class="ps-members-item-wrapper" data-id="{{= data.id }}">
	<div class="ps-members-item">
		<div class="ps-members-item-avatar">
			<span class="ps-avatar">
				<img src="{{= data.avatar }}" title="{{= data.fullname }}" alt="{{= data.fullname }} avatar">
			</span>
		</div>
		<div class="ps-members-item-body">
			<a href="{{= data.profileurl }}" class="ps-members-item-title" title="{{= data.fullname }}">
                {{= data.fullname_before }}{{= data.fullname }}{{= data.fullname_after }}
                <small class="ps-members-item-role">
                    {{ if ( data.role=='member_owner') { }}<?php echo __('owner','groupso');?>{{ } }}
                    {{ if ( data.role=='member_manager') { }} <?php echo __('manager','groupso');?> {{ } }}
                    {{ if ( data.role=='member_moderator') { }} <?php echo __('moderator','groupso');?> {{ } }}
                </small>
			</a>

		</div>
		<div class="ps-members-item-buttons ps-js-actions-placeholder ps-js-dropdown" data-id="{{= data.id }}" style="display:none">
			<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="padding:4px">
		</div>
	</div>
</div>
