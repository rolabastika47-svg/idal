/**
 * Custom Elementor Posts Widget Scripts
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Initialize carousel if Owl Carousel is available
		if (typeof $.fn.owlCarousel !== 'undefined') {
			$('.custom-posts-carousel').each(function() {
				var $carousel = $(this);
				var items = parseInt($carousel.data('items')) || 3;
				var autoplay = $carousel.data('autoplay') === 'true' || $carousel.data('autoplay') === true;
				var autoplayTimeout = parseInt($carousel.data('autoplay-timeout')) || 5000;
				var nav = $carousel.data('nav') === 'true' || $carousel.data('nav') === true;
				var dots = $carousel.data('dots') === 'true' || $carousel.data('dots') === true;

				$carousel.owlCarousel({
					items: items,
					loop: true,
					autoplay: autoplay,
					autoplayTimeout: autoplayTimeout,
					autoplayHoverPause: true,
					nav: nav,
					dots: dots,
					navText: ['<span>←</span>', '<span>→</span>'],
					responsive: {
						0: {
							items: 1
						},
						768: {
							items: 2
						},
						1024: {
							items: items
						}
					}
				});
			});
		}
	});

	// Re-initialize carousel after Elementor preview updates
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/custom_posts.default', function($scope) {
			var $carousel = $scope.find('.custom-posts-carousel');
			
			if ($carousel.length && typeof $.fn.owlCarousel !== 'undefined') {
				// Destroy existing carousel if any
				if ($carousel.data('owl.carousel')) {
					$carousel.trigger('destroy.owl.carousel');
				}

				var items = parseInt($carousel.data('items')) || 3;
				var autoplay = $carousel.data('autoplay') === 'true' || $carousel.data('autoplay') === true;
				var autoplayTimeout = parseInt($carousel.data('autoplay-timeout')) || 5000;
				var nav = $carousel.data('nav') === 'true' || $carousel.data('nav') === true;
				var dots = $carousel.data('dots') === 'true' || $carousel.data('dots') === true;

				$carousel.owlCarousel({
					items: items,
					loop: true,
					autoplay: autoplay,
					autoplayTimeout: autoplayTimeout,
					autoplayHoverPause: true,
					nav: nav,
					dots: dots,
					navText: ['<span>←</span>', '<span>→</span>'],
					responsive: {
						0: {
							items: 1
						},
						768: {
							items: 2
						},
						1024: {
							items: items
						}
					}
				});
			}
		});
	}

})(jQuery);

