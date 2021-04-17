{{

data = data || {};

var memberActions = data.member_actions || [],
	followerActions = data.follower_actions || [];

function fixLabel( label ) {
	return ( label || '' ).replace( /^[a-z]/, function( chr ) {
		return chr.toUpperCase();
	});
}

_.each( memberActions.concat( followerActions ), function( item ) {
	if ( _.isArray( item.action ) ) {

}}

<span class="ps-dropdown ps-dropdown--right ps-dropdown--group-privacy ps-js-dropdown">
	<button class="ps-btn ps-btn--small ps-js-dropdown-toggle">
		<span>{{= fixLabel( item.label ) }}</span>
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="display:none" />
	</button>
	<div class="ps-dropdown__menu ps-js-dropdown-menu">
		{{ _.each( item.action, function( item ) { }}
		<a href="#"
				{{= item.action ? 'data-method="' + item.action + '"' : 'disabled="disabled"' }}
				data-confirm="{{= item.confirm }}" data-id="{{= data.id }}"
				{{ if (item.args) _.each( item.args, function( value, key ) { }}
				data-{{= key }}="{{= value }}"
				{{ }); }}
		>
			<div class="ps-dropdown__group-title">
				<i class="{{= item.icon }}"></i>
				<span>{{= fixLabel( item.label ) }}</span>
			</div>
			<div class="ps-dropdown__group-desc">{{= item.desc }}</div>
		</a>
		{{ }); }}
	</div>
</span>

{{ } else { }}

<button class="ps-btn ps-btn--small"
		{{= item.action ? 'data-method="' + item.action + '"' : 'disabled="disabled"' }}
		data-confirm="{{= item.confirm }}" data-id="{{= data.id }}"
		{{ if (item.args) _.each( item.args, function( value, key ) { }}
		data-{{= key }}="{{= value }}"
		{{ }); }}
>
	<span>{{= fixLabel( item.label ) }}</span>
	<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="display:none" />
</button>

{{ } }}
{{ }); }}
