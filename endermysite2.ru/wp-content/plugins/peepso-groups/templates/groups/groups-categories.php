<div class="ps-accordion__item ps-js-groups-cat ps-js-groups-cat-{{= data.id }}" data-id="{{= data.id }}">
	<div class="ps-accordion__title"  style="cursor:pointer">
		{{= data.name }}
		<?php if(PeepSo::get_option('groups_categories_show_count', 0)) { ?>
		{{= typeof data.groups_count !== 'undefined' ? ('(' + data.groups_count + ')') : '' }}
		<?php } ?>
		<a href="#" class="ps-accordion__action">
			<i class="ps-icon-expand"></i>
		</a>
	</div>
	<?php $single_column = PeepSo::get_option( 'groups_single_column', 0 ); ?>
	<div class="ps-groups <?php echo $single_column ? 'ps-groups--single-col' : '' ?> ps-js-groups" style="display:none">
		<img class="post-ajax-loader" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" />
	</div>
	<div class="ps-accordion__footer" style="display:none">
		<a href="<?php echo PeepSo::get_page('groups') . '?category='; ?>{{= data.id }}"><i class="ps-icon-caret-right"></i><?php echo __('Show all groups from this category', 'groupso') ;?></a>
	</div>
</div>
