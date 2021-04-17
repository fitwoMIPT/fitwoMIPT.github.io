(function( $, peepso, factory ) {

	ps_woo = peepso.woo = factory( $, peepso );

})( jQuery || $, peepso, function( $, peepso ) {

/*
 * PeepSo Messages class
 * @package PeepSo
 * @author PeepSo
 */

function PsWoo()
{
	//
}

var ps_woo = new PsWoo();

/**
 * Displays the list of products and hides the summary.
 */
PsWoo.prototype.show_long_products = function()
{
	$("#summary-products").hide();
	$("#long-products").fadeIn();
};


$(function() {
	$( document ).on( 'click.ps-woo__slider-wrapper', '.ps-woo__slider-btn', function() {
		var $btn = $( this ),
			step = +$btn.data( 'step' ),
			direction = step > 0 ? 'next' : step < 0 ? 'prev' : '',
			$wrapper, wrapperWidth, scrollLeft;

		if ( ! direction ) {
			return;
		}

		$wrapper = $btn.closest( '.ps-woo__slider-wrapper' ).children( '.ps-woo__slider' );
		wrapperWidth = $wrapper.width();
		$wrapper.children( '.ps-woo__slider-item' ).each(function() {
			var position;

			$item = $( this );
			position = $item.position();
			if ( direction === 'next' ) {

				if ( Math.floor( position.left ) > 0 ) {
					scrollLeft = $wrapper.scrollLeft() + position.left;
					return false;
				}
			} else if ( direction === 'prev' ) {
				if ( Math.floor( position.left ) < 0 ) {
					scrollLeft = $wrapper.scrollLeft() + position.left;
				}
			}
		});

		if ( typeof scrollLeft === 'number' ) {
			$wrapper.animate({ scrollLeft: scrollLeft }, 'fast' );
		}
	});
});

return ps_woo;

});
