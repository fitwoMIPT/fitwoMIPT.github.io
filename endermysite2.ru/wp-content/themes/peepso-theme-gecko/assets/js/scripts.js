(function($) {
	// Initialize sticky sidebar.
	$(function() {
		var $adminbar = $('#wpadminbar');
		var $header = $('header.header').not('.header--static');
		var $footer = $('footer.footer');
		var offsetTop = ($adminbar.height() || 0) + ($header.height() || 0) + 29;
		var offsetBottom = ($footer.height() || 0) + 29;

		$('.sidebar--sticky .sidebar__inner').gc_stick_in_parent({
			offset_top: offsetTop,
			offset_bottom: offsetBottom
		});
	});

	// Modal
	$('.gc-modal__toggle').click(function() {
		$('html').addClass('gc-modal--open');
	});

	$('.gc-modal__close').click(function() {
		$('html').removeClass('gc-modal--open');
	});

	function check_cart() {
		var woocart_toggle = $('.header__cart-toggle');
		var woocart = $('.widget_shopping_cart_content p');

		if (woocart.hasClass('woocommerce-mini-cart__empty-message')) {
			$(woocart_toggle).addClass('empty');
		} else {
			$(woocart_toggle).removeClass('empty');
		}
	}
	setInterval(check_cart, 1000);

	// HEADER CART TOGGLE >>>
	$('.js-header-cart-toggle').click(function(e) {
		var $toggle = $(this);
		var $document = $(document.body);
		var $widget = $toggle.parent().find('.header__cart');
		var is_opened = $toggle.hasClass('open');
		var event_name = 'click.gc-header-cart';

		// Hide widget.
		if (is_opened) {
			$toggle.removeClass('open');
			$widget.stop().fadeOut();
			$document.off(event_name);
		}

		// Show widget.
		else {
			$toggle.addClass('open');
			$widget.stop().fadeIn();

			// Setup autohide trigger.
			setTimeout(function() {
				$document.off(event_name);
				$document.on(event_name, function(e) {
					// Skip if click event occurs inside the widget.
					if ($(e.target).closest($widget).length) {
						return;
					}

					// Hide widget.
					$toggle.removeClass('open');
					$widget.stop().fadeOut();
					$document.off(event_name);
				});
			}, 1);
		}
	});
	// <<< END

	$('.gc-modal__menu .menu-item-has-children').on('click', function(e) {
		var xPosition = e.pageX;
		var width = $(this).width();
		var area = width - 40;

		$(this).toggleClass('open');

		if (xPosition > width) {
			$(this)
				.find('.sub-menu')
				.toggle();
		}
	});

	$(window).scroll(function() {
		var scroll = $(window).scrollTop();

		if (scroll >= 30) {
			$('body').addClass('scroll');
			$('.header').addClass('header--scroll');
		} else {
			$('body').removeClass('scroll');
			$('.header').removeClass('header--scroll');
		}
	});

	if ($('html').hasClass('smooth-scroll')) {
		// Select all links with hashes
		$('a[href*="#"]')
			// Remove links that don't actually link to anything
			.not('[href="#"]')
			.not('[href="#0"]')
			.click(function(event) {
				// On-page links
				if (
					location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
					location.hostname == this.hostname
				) {
					// Figure out element to scroll to
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
					// Does a scroll target exist?
					if (target.length) {
						// Only prevent default if animation is actually gonna happen
						event.preventDefault();
						$('html, body').animate(
							{
								scrollTop: target.offset().top
							},
							1000,
							function() {
								// Callback after animation
								// Must change focus!
								var $target = $(target);
								$target.focus();
								if ($target.is(':focus')) {
									// Checking if the target was focused
									return false;
								} else {
									$target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
									$target.focus(); // Set focus again
								}
							}
						);
					}
				}
			});
	}

	$(window).on('load', function() {
		$(window).on('resize', function(e) {
			checkScreenSize();
		});

		checkScreenSize();

		function checkScreenSize() {
			var footer = $('.footer').height();
			var newWindowWidth = $(window).width();

			if (newWindowWidth >= 980) {
				var footer = $('.footer').height();
				$('body').css('padding-bottom', footer);
			} else {
				$('body').css('padding-bottom', 0);
			}
		}
	});

	// Scroll to top
	$(window).scroll(function() {
		var scroll = $(window).scrollTop();
		var button = '.js-scroll-top';

		if (scroll >= 100) {
			$(button).fadeIn();
		} else {
			$(button).fadeOut();
		}
	});

	// Toggle header widget
	$('.js-header-widget-toggle').click(function() {
		var toggle = $(this);
		var logo = '.header__logo';
		var openClass = 'open';
		var headerWidget = '.header__widget';

		$(toggle)
			.toggleClass(openClass)
			.parent()
			.find(headerWidget)
			.toggle();
		$(logo).toggle();
	});

	// Toggle header widget
	$('.js-header-search-toggle').click(function() {
		var toggle = $(this);
		var headerWidget = '.header__search-bar';

		$(toggle)
			.parent()
			.find(headerWidget)
			.toggle();
	});

	// Gecko grid arrangement using Macy.js.
	$(function() {
		function initMasonry($container) {
			var instance = Macy({
				container: $container,
				margin: { x: 15, y: 15 },
				columns: 2
			});

			function _rearrange() {
				var winWidth = $(window).width();
				if (winWidth < 980) {
					instance.remove();
				} else {
					instance.reInit();
				}
			}

			// Debounce grid rearrangement.
			var timer = null;
			function rearrange() {
				clearTimeout(timer);
				timer = setTimeout(_rearrange, 500);
			}

			$(window).on('load resize scroll', rearrange);
		}

		// Post grid.
		var $gridPosts = document.querySelector('#gecko-blog .content__posts');
		if ($gridPosts) {
			initMasonry($gridPosts);
		}

		// Archive grid.
		var $gridCategory = document.querySelector('.archive.date .content--grid');
		if ($gridCategory) {
			initMasonry($gridCategory);
		}

		// Category grid.
		var $gridCategory = document.querySelector('.category .content--grid');
		if ($gridCategory) {
			initMasonry($gridCategory);
		}
	});
})(jQuery);
