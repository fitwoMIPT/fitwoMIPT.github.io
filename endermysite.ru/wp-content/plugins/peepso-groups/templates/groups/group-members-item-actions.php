{{ _.each( data.member_passive_actions, function( item ) { }}
{{ if ( _.isArray( item.action ) ) { }}

<button class="ps-btn ps-dropdown__toggle ps-js-dropdown-toggle">
	<span class="{{= item.class ? item.class : 'ps-icon-cog' }}"></span>
</button>
<div class="ps-dropdown__menu ps-js-dropdown-menu" style="display:none">
	{{ _.each( item.action, function( subitem ) { }}
	<a href="#" class="ps-js-group-member-action" {{= subitem.action ? 'data-method="' + subitem.action + '"' : 'disabled="disabled"' }}
			data-confirm="{{= subitem.confirm }}" data-id="{{= data.id }}" data-passive_user_id="{{= data.passive_user_id }}"
			{{ if (subitem.args) _.each( subitem.args, function( value, key ) { }}
			data-{{= key }}="{{= value }}"
			{{ }); }}
	>
		<span>{{= ( subitem.label || '' ).charAt(0).toUpperCase() + ( subitem.label || '' ).slice(1) }}</span>
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="padding-left:5px;display:none" />
	</a>
	{{ }); }}
</div>

{{ } else { }}

<button class="ps-btn ps-btn--small ps-js-group-member-action" {{= item.action ? 'data-method="' + item.action + '"' : 'disabled="disabled"' }}
		data-confirm="{{= item.confirm }}" data-id="{{= data.id }}" data-passive_user_id="{{= data.passive_user_id }}"
		{{ if (item.args) _.each( item.args, function( value, key ) { }}
		data-{{= key }}="{{= value }}"
		{{ }); }}
>
	<span>{{= ( item.label || '' ).charAt(0).toUpperCase() + ( item.label || '' ).slice(1) }}</span>
	<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif') ?>" style="padding-left:5px;display:none" />
</button>

{{ } }}
{{ }); }}
