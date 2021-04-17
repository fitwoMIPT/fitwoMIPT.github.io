{{

var memberActions, followerActions;

data = data || {};
memberActions = data.member_actions || [];
followerActions = data.follower_actions || [];

_.each( memberActions.concat( followerActions ), function( item ) {
	if ( _.isArray( item.action ) ) {

}}

<span class="ps-dropdown ps-dropdown--right ps-dropdown--group-privacy ps-js-dropdown">
	<button class="ps-btn ps-btn--small ps-js-dropdown-toggle {{= item.class }}">
		<span>{{= item.label }}</span>
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="display:none;" />
	</button>
	<div class="ps-dropdown__menu ps-js-dropdown-menu">
		{{ _.each( item.action, function( item ) { }}
		<a class="ps-dropdown__group ps-js-group-member-action"
				{{= item.action ? 'data-method="' + item.action + '"' : 'disabled="disabled"' }}
				data-confirm="{{= item.confirm }}"
				{{ if ( item.args ) _.each( item.args, function( value, key ) { }}
				data-{{= key }}="{{= value }}"
				{{ }); }}
		>
			<div class="ps-dropdown__group-title">
				<i class="{{= item.icon }}"></i>
				<span>{{= item.label }}</span>
			</div>
			<div class="ps-dropdown__group-desc">{{= item.desc }}</div>
		</a>
		{{ }); }}
	</div>
</span>

{{

	} else {

}}

<button role="button" aria-label="{{= item.label }}" class="ps-btn ps-btn--small ps-js-group-member-action {{= item.class }}"
		{{= item.action ? 'data-method="' + item.action + '"' : 'disabled="disabled"' }}
		data-confirm="{{= item.confirm }}"
		{{ if ( item.args ) _.each( item.args, function( value, key ) { }}
		data-{{= key }}="{{= value }}"
		{{ }); }}
>
	<span>{{= item.label }}</span>
	<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="display:none;" />
</button>

{{

	}
});

}}
