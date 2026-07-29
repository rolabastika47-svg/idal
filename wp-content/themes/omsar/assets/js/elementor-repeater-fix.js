/**
 * Elementor Repeater Fix - Always show delete button
 * 
 * This script ensures that delete buttons in Elementor repeaters are always visible,
 * even when there's only one item in the repeater (especially for nested repeaters).
 */
(function($) {
    'use strict';

    /**
     * Function to show delete buttons in repeaters
     */
    function showRepeaterDeleteButtons() {
        // Find all repeater items - Elementor uses various selectors
        var selectors = [
            '.elementor-repeater-row-tools .elementor-repeater-tool-remove',
            '.elementor-repeater-row .elementor-repeater-tool-remove',
            '.elementor-repeater-row .elementor-button-wrapper .elementor-repeater-tool-remove',
            'button.elementor-repeater-tool-remove',
            '.elementor-repeater-row-tools button[data-action="remove"]'
        ];

        selectors.forEach(function(selector) {
            $(selector).each(function() {
                var $button = $(this);
                // Remove any inline styles that might hide it
                $button.css({
                    'display': '',
                    'visibility': '',
                    'opacity': ''
                });
                
                // Remove any classes that might hide it
                $button.removeClass('elementor-hidden');
                
                // Force show if parent has min-items logic
                var $row = $button.closest('.elementor-repeater-row');
                if ($row.length > 0) {
                    var $repeater = $row.closest('.elementor-repeater-fields-wrapper');
                    if ($repeater.length > 0) {
                        var rowCount = $repeater.find('.elementor-repeater-row').length;
                        // Even if there's only one row, show the delete button
                        if (rowCount >= 1) {
                            $button.show();
                        }
                    }
                }
            });
        });

        // Also handle nested repeaters specifically
        $('.elementor-repeater-fields-wrapper').each(function() {
            var $repeater = $(this);
            var $rows = $repeater.find('.elementor-repeater-row');
            var $deleteButtons = $repeater.find('.elementor-repeater-tool-remove');
            
            // Always show delete buttons, regardless of row count
            $deleteButtons.each(function() {
                var $btn = $(this);
                $btn.css('display', 'inline-flex');
                $btn.css('visibility', 'visible');
                $btn.css('opacity', '1');
            });
        });
    }

    /**
     * Initialize when Elementor is ready
     */
    function init() {
        // Run immediately
        showRepeaterDeleteButtons();

        // Use MutationObserver for better performance
        var observer = new MutationObserver(function(mutations) {
            var shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0) {
                    shouldUpdate = true;
                }
            });
            if (shouldUpdate) {
                setTimeout(showRepeaterDeleteButtons, 50);
            }
        });

        // Observe changes in the Elementor panel
        var elementorPanel = document.querySelector('.elementor-panel');
        if (elementorPanel) {
            observer.observe(elementorPanel, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }

        // Also observe the document body for dynamically added content
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Wait for Elementor to be ready
    if (typeof elementor !== 'undefined') {
        elementor.on('preview:loaded', init);
        elementor.on('panel:init', function() {
            setTimeout(init, 300);
        });
    }

    // Also run on document ready as fallback
    $(document).ready(function() {
        setTimeout(init, 500);
    });

    // Run periodically to catch any missed cases (with debounce)
    var lastRun = 0;
    setInterval(function() {
        var now = Date.now();
        if (now - lastRun > 500) { // Run max once per 500ms
            showRepeaterDeleteButtons();
            lastRun = now;
        }
    }, 1000);

})(jQuery);
