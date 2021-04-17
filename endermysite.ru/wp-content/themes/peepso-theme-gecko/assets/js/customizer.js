( function( $ ) {
	// COLORS
	wp.customize( 'color_primary', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--primary', newval);
		} );
	} );

	wp.customize( 'color_primary_shade', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--primary-shade', newval);
		} );
	} );

	wp.customize( 'color_primary_shade', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--primary-shade', newval);
		} );
	} );

	wp.customize( 'color_primary_light', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--primary-light', newval);
		} );
	} );

	wp.customize( 'color_primary_alt', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--alt', newval);
		} );
	} );

	wp.customize( 'color_text', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--text', newval);
		} );
	} );

	wp.customize( 'color_text_light', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--text-light', newval);
		} );
	} );

	// GENERAL
	wp.customize( 'general_links_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--link', newval);
		} );
	} );

	wp.customize( 'general_links_hover_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--color--link-hover', newval);
		} );
	} );

	wp.customize( 'general_background_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--body-bg', newval);
		} );
	} );

	wp.customize( 'general_background_image_size', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--body-bg--image-size', newval);
		} );
	} );

	wp.customize( 'general_background_image_repeat', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--body-bg--image-repeat', newval);
		} );
	} );

	// LAYOUT
	wp.customize( 'layout_width', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--container', newval);
		} );
	} );

	wp.customize( 'layout_content_width', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--layout-grid', newval);
		} );
	} );

	// HEADER
	wp.customize( 'header_transparent', function( value ) {
		value.bind( function( newval ) {
			if (newval == 1) {
				$("html").addClass('header-is-transparent');
				$(".header").addClass('header--transparent');
			} else {
				$("html").removeClass('header-is-transparent');
				$(".header").removeClass('header--transparent');
			}
		} );
	} );

	wp.customize( 'header_menu_order', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-menu-order', newval);
		} );
	} );

	wp.customize( 'header_menu_align', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-menu-align', newval);
		} );
	} );

	wp.customize( 'header_background', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-background', newval);
		} );
	} );

	wp.customize( 'header_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-color', newval);
		} );
	} );

	wp.customize( 'header_color_hover', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-color--hover', newval);
		} );
	} );

	wp.customize( 'header_active_indi_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-active-indicator-color', newval);
		} );
	} );

	wp.customize( 'header_search_bg', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-search-bg', newval);
		} );
	} );

	wp.customize( 'header_search_input_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-search-input-color', newval);
		} );
	} );

	wp.customize( 'header_search_focus_shadow', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-search-focus-shadow', newval);
		} );
	} );

	wp.customize( 'header_search_separator', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-search-separator', newval);
		} );
	} );

	wp.customize( 'header_burger_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-burger-color', newval);
		} );
	} );

	wp.customize( 'header_burger_color_hover', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-burger-color--hover', newval);
		} );
	} );

	wp.customize( 'header_menu_bg', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-menu--mobile-bg', newval);
		} );
	} );

	wp.customize( 'header_menu_links', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-menu--mobile-links', newval);
		} );
	} );

	wp.customize( 'header_menu_sub_links', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--header-menu--mobile-sublinks', newval);
		} );
	} );

	// PEEPSO
	wp.customize( 'pinned_post_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--pin-post', newval);
		} );
	} );

	wp.customize( 'btn_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn-color', newval);
		} );
	} );

	wp.customize( 'btn_background', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn-bg', newval);
		} );
	} );

	wp.customize( 'btn_background_hover', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn-bg--hover', newval);
		} );
	} );

	wp.customize( 'btn_action_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn--action-color', newval);
		} );
	} );

	wp.customize( 'btn_action_background', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn--action-bg', newval);
		} );
	} );

	wp.customize( 'btn_action_background_hover', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--ps-btn--action-bg--hover', newval);
		} );
	} );

	// WIDGETS
	wp.customize( 'w_gradient_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--widget--gradient-color', newval);
		} );
	} );

	wp.customize( 'w_gradient_links_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--widget--gradient-links', newval);
		} );
	} );

	wp.customize( 'w_gradient_links_hover_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--widget--gradient-links-hover', newval);
		} );
	} );

	// FOOTER
	wp.customize( 'footer_links_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-links-color', newval);
		} );
	} );

	wp.customize( 'footer_links_color_hover', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-links-color--hover', newval);
		} );
	} );

	wp.customize( 'footer_text_color', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-text-color', newval);
		} );
	} );

	wp.customize( 'footer_bg', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-bg', newval);
		} );
	} );

	wp.customize( 'footer_copyrights_bg', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-copyrights-bg', newval);
		} );
	} );

	wp.customize( 'footer_widgets_title', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-widgets-title-color', newval);
		} );
	} );

	wp.customize( 'footer_widgets_list_separator', function( value ) {
		value.bind( function( newval ) {
			$("body").get(0).style.setProperty('--footer-widgets-list-separator', newval);
		} );
	} );

} )( jQuery );
