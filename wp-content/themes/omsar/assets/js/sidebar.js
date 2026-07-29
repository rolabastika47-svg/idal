/**
 * Sidebar Navigation JavaScript
 * 
 * Handles expandable menu items in the page sidebar
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle click on sidebar menu items with children (all levels)
        // Only prevent navigation when clicking on chevron icon
        $(document).on('click', '.sidebar-menu-item.has-children > .sidebar-link, .sidebar-submenu-item.has-children > .sidebar-sublink', function(e) {
            var $menuItem = $(this).parent();
            
            // If clicking on chevron, toggle expand without navigating
            var clickedChevron = $(e.target).hasClass('sidebar-chevron') || $(e.target).closest('.sidebar-chevron').length > 0;
            
            if (clickedChevron) {
                e.preventDefault();
                $menuItem.toggleClass('active');
                return false;
            }
            
            // Default: navigate to link normally
        });
        
        // Handle click on non-clickable menu items (disabled links) - toggle children on any click
        $(document).on('click', '.sidebar-link-disabled, .sidebar-sublink-disabled', function(e) {
            var $menuItem = $(this).parent();
            
            // If item has children, toggle expand/collapse on any click
            if ($menuItem.hasClass('has-children')) {
                e.preventDefault();
                e.stopPropagation();
                $menuItem.toggleClass('active');
                return false;
            }
            
            // Prevent navigation for non-clickable items without children
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
        
        // Auto-expand parent menu items if current page is in submenu (all levels)
        $('.sidebar-submenu-item.active').each(function() {
            var $item = $(this);
            // Expand all parent menus
            $item.parents('.sidebar-menu-item, .sidebar-submenu-item').addClass('active');
        });
        
        // Auto-expand if current page is active
        $('.sidebar-menu-item.active, .sidebar-submenu-item.active').each(function() {
            if ($(this).hasClass('has-children')) {
                $(this).addClass('active');
            }
        });
        
        // Sidebar Animation - Fade in from left
        // Only animates on initial parent page load, not on child/nested routes
        function initSidebarAnimation() {
            const sidebar = document.querySelector('.page-sidebar');
            if (!sidebar) {
                return;
            }

            // Get data attributes
            const parentPageId = sidebar.getAttribute('data-parent-page-id');
            const currentPageId = sidebar.getAttribute('data-current-page-id');
            const isParentPage = sidebar.getAttribute('data-is-parent-page') === '1';

            // Check sessionStorage FIRST - if parent has already been animated, skip animation for all pages
            const storageKey = 'omsar_sidebar_animated_' + parentPageId;
            const hasAnimated = sessionStorage.getItem(storageKey) === '1';

            // If parent has already been animated (in this session), skip animation for all pages
            if (hasAnimated) {
                // Ensure class is present (may already be there from PHP for child pages)
                if (!sidebar.classList.contains('sidebar-animated')) {
                    sidebar.classList.add('sidebar-animated');
                }
                return;
            }

            // If current page is a child or nested page, skip animation
            // Note: PHP already adds sidebar-animated class for child pages, but we ensure it's here
            if (!isParentPage) {
                // Ensure class is present (may already be there from PHP)
                if (!sidebar.classList.contains('sidebar-animated')) {
                    sidebar.classList.add('sidebar-animated');
                }
                return;
            }

            // Only animate if it's the parent page AND hasn't been animated yet
            // Intersection Observer for viewport entry (only for initial parent page load)
            const observerOptions = {
                threshold: 0.2, // Trigger when 20% of sidebar is visible
                rootMargin: '0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !entry.target.classList.contains('sidebar-animated')) {
                        entry.target.classList.add('sidebar-animated');
                        // Mark this parent page as animated in sessionStorage
                        // This prevents animation on all subsequent pages (parent and children) in this session
                        sessionStorage.setItem(storageKey, '1');
                        observer.unobserve(entry.target); // Stop observing once animated
                    }
                });
            }, observerOptions);

            observer.observe(sidebar);
        }

        // Initialize sidebar animation
        initSidebarAnimation();
    });

})(jQuery);

