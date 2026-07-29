/**
 * Animated Stats Counter Widget JavaScript
 * 
 * Handles scroll-triggered animation for counter numbers
 * 
 * @package OMSAR
 */

(function($) {
	'use strict';

	/**
	 * Animate counter from 0 to target value
	 */
	function animateCounter(element, targetValue, duration) {
		var $element = $(element);
		var $counterValue = $element.find('.omsar-counter-value');
		var startValue = 0;
		var startTime = null;
		var unit = $element.find('.omsar-stats-number').data('unit') || '';

		function animate(currentTime) {
			if (startTime === null) {
				startTime = currentTime;
			}

			var elapsed = (currentTime - startTime) / 1000; // Convert to seconds
			var progress = Math.min(elapsed / duration, 1);

			// Easing function (ease-out)
			var easeOut = 1 - Math.pow(1 - progress, 3);

			var currentValue = Math.floor(startValue + (targetValue - startValue) * easeOut);
			$counterValue.text(formatNumber(currentValue));

			if (progress < 1) {
				requestAnimationFrame(animate);
			} else {
				$counterValue.text(formatNumber(targetValue));
			}
		}

		requestAnimationFrame(animate);
	}

	/**
	 * Format number with commas
	 */
	function formatNumber(num) {
		return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	/**
	 * Check if element is in viewport
	 */
	function isInViewport(element) {
		var rect = element.getBoundingClientRect();
		var windowHeight = window.innerHeight || document.documentElement.clientHeight;
		var windowWidth = window.innerWidth || document.documentElement.clientWidth;
		
		return (
			rect.top < windowHeight &&
			rect.bottom > 0 &&
			rect.left < windowWidth &&
			rect.right > 0
		);
	}

	/**
	 * Initialize counters
	 */
	function initCounters() {
		$('.omsar-stats-counter-item').each(function() {
			var $item = $(this);
			
			// Skip if already animated
			if ($item.data('animated')) {
				return;
			}

			// Check if in viewport
			if (isInViewport(this)) {
				var targetValue = parseFloat($item.data('counter-value')) || 0;
				var duration = parseFloat($item.data('counter-duration')) || 2;

				// Mark as animated
				$item.data('animated', true);

				// Start animation
				animateCounter(this, targetValue, duration);
			}
		});
	}

	/**
	 * Handle scroll event
	 */
	function handleScroll() {
		initCounters();
	}

	// Initialize on document ready
	$(document).ready(function() {
		// Initial check
		initCounters();

		// Check on scroll
		$(window).on('scroll', handleScroll);

		// Check on resize (in case layout changes)
		$(window).on('resize', function() {
			setTimeout(initCounters, 100);
		});
	});

	// Re-initialize when Elementor frontend is ready
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/omsar_animated_stats_counter.default', function($scope) {
			// Reset animation state for this widget instance
			$scope.find('.omsar-stats-counter-item').data('animated', false);
			
			// Initialize counters
			setTimeout(function() {
				initCounters();
			}, 100);
		});
	}

})(jQuery);

