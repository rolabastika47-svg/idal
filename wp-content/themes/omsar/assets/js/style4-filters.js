/**
 * Style 4 Filters for Posts by Taxonomy Widget
 * 
 * Handles text search and dropdown filtering for Style 4 posts
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle Style 4 filtering
        $(document).on('input', '.omsar-style4-search-input', function() {
            var $searchInput = $(this);
            var $widget = $searchInput.closest('.omsar-posts-by-taxonomy-widget, .omsar-projects-widget');
            var widgetId = $widget.attr('id');
            
            if (widgetId) {
                applyStyle4Filters(widgetId);
            }
        });

        // Handle general search input for Projects widget and Style 4 widgets only
        var filterTimeout;
        $(document).on('input', '.omsar-posts-search-input', function() {
            var $searchInput = $(this);
            var $widget = $searchInput.closest('.omsar-posts-by-taxonomy-widget, .omsar-projects-widget');
            var widgetId = $widget.attr('id');
            
            if (widgetId) {
                // Check if this is Style 4 or Projects widget before triggering AJAX
                var $grid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
                if ($grid.length > 0) {
                    var listingStyle = $grid.data('listing-style') || '';
                    var isProjectsWidget = $widget.hasClass('omsar-projects-widget');
                    
                    // Only trigger AJAX for Style 4 or Projects widget
                    // For other styles, let the client-side search handler (posts-by-taxonomy-search.js) handle it
                    if (listingStyle === 'style4' || isProjectsWidget) {
                        // Debounce the filter to avoid too many AJAX calls
                        clearTimeout(filterTimeout);
                        filterTimeout = setTimeout(function() {
                            // Reset pagination when filter changes
                            resetLoadMorePagination($widget);
                            // Load filtered posts from server
                            loadFilteredPosts(widgetId);
                        }, 300); // 300ms delay
                    }
                    // If not Style 4 or Projects widget, do nothing - let client-side handler work
                }
            }
        });

        $(document).on('change', '.omsar-style4-dropdown', function() {
            var $dropdown = $(this);
            var $widget = $dropdown.closest('.omsar-posts-by-taxonomy-widget, .omsar-projects-widget');
            var widgetId = $widget.attr('id');
            
            if (widgetId) {
                // Reset pagination when filter changes
                resetLoadMorePagination($widget);
                // Load filtered posts from server
                loadFilteredPosts(widgetId);
            }
        });
        
        /**
         * Reset load more pagination when filters change
         */
        function resetLoadMorePagination($widget) {
            var $grid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
            if ($grid.length > 0) {
                // Reset to page 1
                $grid.data('current-page', 1);
                
                // Hide load more button - it will be re-shown if needed when loading
                var $loadMoreWrapper = $widget.find('.omsar-load-more-wrapper');
                $loadMoreWrapper.fadeOut(200);
            }
        }
        
        /**
         * Load filtered posts from server (page 1)
         */
        function loadFilteredPosts(widgetId) {
            var $widget = $('#' + widgetId);
            var $grid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
            
            if ($grid.length === 0) {
                // Fallback to client-side filtering if grid not found
                applyStyle4Filters(widgetId);
                return;
            }
            
            // Get grid data attributes
            var postType = $grid.data('post-type') || 'post';
            var tab = $grid.data('tab') || 'all';
            var listingStyle = $grid.data('listing-style') || 'style4';
            var postsPerPage = parseInt($grid.data('posts-per-page')) || 9;
            var orderby = $grid.data('orderby') || 'date';
            var order = $grid.data('order') || 'DESC';
            var defaultImage = $grid.data('default-image') || '';
            var badgeSource = $grid.data('badge-source') || '';
            var badgeTaxonomy = $grid.data('badge-taxonomy') || '';
            var badgeCustomField = $grid.data('badge-custom-field') || '';
            var badgeTaxonomyDisplay = $grid.data('badge-taxonomy-display') || 'name';
            var readMoreText = $grid.data('read-more-text') || 'Read More';
            var showReadMore = $grid.data('show-read-more') === 'yes' || $grid.data('show-read-more') === true || $grid.data('show-read-more') === '1';
            var clickableCards = $grid.data('clickable-cards') === '1' || $grid.data('clickable-cards') === 1 || $grid.data('clickable-cards') === true || $grid.data('clickable-cards') === 'yes';
            var dropdownFilterTaxonomy = $grid.data('dropdown-filter-taxonomy') || '';
            var dropdownFilterCustomField = $grid.data('dropdown-filter-custom-field') || '';
            
            // Get custom field filter parameters
            var customFieldMetaKey = $grid.data('custom-field-meta-key') || '';
            var customFieldMetaValue = $grid.data('custom-field-meta-value') || '';
            
            // Get Style 5 display options
            var style5ContentOverlay = $grid.data('style5-content-overlay') === '1' || $grid.data('style5-content-overlay') === 1 || $grid.data('style5-content-overlay') === true;
            var style5ShowCategories = $grid.data('style5-show-categories') === '1' || $grid.data('style5-show-categories') === 1 || $grid.data('style5-show-categories') === true;
            var style5ShowExcerpt = $grid.data('style5-show-excerpt') === '1' || $grid.data('style5-show-excerpt') === 1 || $grid.data('style5-show-excerpt') === true;
            var style5ShowDate = $grid.data('style5-show-date') === '1' || $grid.data('style5-show-date') === 1 || $grid.data('style5-show-date') === true;
            var style5ShowAuthor = $grid.data('style5-show-author') === '1' || $grid.data('style5-show-author') === 1 || $grid.data('style5-show-author') === true;
            
            // Get current filter values
            var $searchInput = $widget.find('.omsar-posts-search-input');
            var $dropdown = $widget.find('.omsar-style4-dropdown');
            var searchTerm = $searchInput.length > 0 ? $searchInput.val().trim() : '';
            var dropdownValue = $dropdown.length > 0 ? $dropdown.val() : '';
            
            // Show loading state
            $grid.addClass('omsar-loading');
            
            // Prepare AJAX data
            var ajaxData = {
                action: 'omsar_load_more_taxonomy_posts',
                widget_id: widgetId,
                tab: tab,
                post_type: postType,
                taxonomy: '',
                term_id: '',
                term_name: '',
                use_acf: 0,
                acf_field: '',
                listing_style: listingStyle,
                posts_per_page: postsPerPage,
                offset: 0, // Start from beginning
                current_page: 1,
                orderby: orderby,
                order: order,
                default_image: defaultImage,
                badge_source: badgeSource,
                badge_taxonomy: badgeTaxonomy,
                badge_custom_field: badgeCustomField,
                badge_taxonomy_display: badgeTaxonomyDisplay,
                read_more_text: readMoreText,
                show_read_more: showReadMore ? 'yes' : 'no',
                clickable_cards: clickableCards ? '1' : '0',
                filter_search_term: searchTerm,
                filter_dropdown_value: dropdownValue,
                filter_dropdown_taxonomy: dropdownFilterTaxonomy,
                filter_dropdown_custom_field: dropdownFilterCustomField,
                custom_field_meta_key: customFieldMetaKey,
                custom_field_meta_value: customFieldMetaValue,
                style5_content_overlay: style5ContentOverlay ? '1' : '0',
                style5_show_categories: style5ShowCategories ? '1' : '0',
                style5_show_excerpt: style5ShowExcerpt ? '1' : '0',
                style5_show_date: style5ShowDate ? '1' : '0',
                style5_show_author: style5ShowAuthor ? '1' : '0',
                nonce: omsarTaxonomyLoadMore?.nonce || ''
            };
            
            // Make AJAX request
            $.ajax({
                url: omsarTaxonomyLoadMore?.ajaxurl || omsarAjax?.ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    $grid.removeClass('omsar-loading');
                    
                    if (response.success && response.data) {
                        // Replace current posts with filtered results
                        $grid.html(response.data.html || '');
                        
                        // Reset pagination
                        $grid.data('current-page', 1);
                        
                        // Update total posts count if provided
                        if (response.data.total_filtered_posts !== undefined && response.data.total_filtered_posts !== null) {
                            $grid.data('total-posts', response.data.total_filtered_posts);
                        }
                        
                        // Trigger custom event to notify other handlers that posts were loaded
                        $widget.trigger('omsar:posts-loaded');
                        
                        // Show/hide Load More button based on has_more
                        var $loadMoreWrapper = $widget.find('.omsar-load-more-wrapper');
                        if (response.data.has_more === true) {
                            // Show button if it exists, create if it doesn't
                            if ($loadMoreWrapper.length === 0) {
                                // Get load more text from existing button or use default
                                var $existingButton = $widget.find('.omsar-load-more-btn');
                                var loadMoreText = $existingButton.length > 0 ? $existingButton.text() : 'Load More';
                                $loadMoreWrapper = $('<div class="omsar-load-more-wrapper" data-tab="' + tab + '">' +
                                    '<button class="omsar-load-more-btn" data-widget-id="' + widgetId + '" data-tab="' + tab + '">' +
                                    loadMoreText + '</button></div>');
                                $grid.after($loadMoreWrapper);
                            }
                            $loadMoreWrapper.fadeIn(200);
                        } else {
                            // Hide button if no more posts
                            $loadMoreWrapper.fadeOut(200);
                        }
                        
                        // Update results count if function exists
                        if (typeof window.updateStyle4ResultsCount === 'function') {
                            var visibleCount = $grid.find('.omsar-post-card').length;
                            window.updateStyle4ResultsCount(widgetId, visibleCount);
                        }
                        
                        // Show/hide "no results" message
                        var $noResults = $widget.find('.omsar-style4-no-results');
                        if (response.data.html === '' || ($grid.find('.omsar-post-card').length === 0 && (searchTerm !== '' || dropdownValue !== ''))) {
                            if ($noResults.length === 0) {
                                $grid.after('<div class="omsar-style4-no-results"><p>No posts found matching your filters.</p></div>');
                            }
                        } else {
                            $noResults.remove();
                        }
                    } else {
                        // Fallback to client-side filtering
                        applyStyle4Filters(widgetId);
                    }
                },
                error: function() {
                    $grid.removeClass('omsar-loading');
                    // Fallback to client-side filtering
                    applyStyle4Filters(widgetId);
                }
            });
        }

        /**
         * Apply both search and dropdown filters
         * Also considers general search input if it exists
         */
        function applyStyle4Filters(widgetId) {
            var $widget = $('#' + widgetId);
            var $filters = $widget.find('.omsar-style4-filters, .omsar-style4-filters-container');
            var $searchInput = $filters.find('.omsar-style4-search-input');
            var $dropdown = $filters.find('.omsar-style4-dropdown');
            
            // Get active tab (or use the widget itself if no tabs)
            var $activeTab = $widget.find('.tab-pane.active');
            // If no active tab found, check if we're using a single grid without tabs
            if ($activeTab.length === 0) {
                // Look for direct grid containers (for widgets without tabs)
                var $directGrid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
                if ($directGrid.length > 0) {
                    // Create a virtual tab container for filtering
                    $activeTab = $widget;
                } else {
                    return;
                }
            }
            
            // Get post cards from active tab or from the widget directly if no tabs
            var $postCards = $activeTab.find('.omsar-post-card');
            // If no cards found in tab, try finding them directly in the widget
            if ($postCards.length === 0) {
                $postCards = $widget.find('.omsar-post-card');
            }
            
            // Get filter values
            var searchTerm = '';
            if ($searchInput.length > 0) {
                searchTerm = $searchInput.val().toLowerCase().trim();
            }
            
            // Also check general search input
            var $generalSearch = $widget.find('.omsar-posts-search-input');
            var generalSearchTerm = '';
            if ($generalSearch.length > 0) {
                generalSearchTerm = $generalSearch.val().toLowerCase().trim();
            }
            
            var selectedValue = '';
            if ($dropdown.length > 0) {
                selectedValue = $dropdown.val();
            }
            
            // Filter posts
            var visibleCount = 0;
            $postCards.each(function() {
                var $card = $(this);
                var shouldShow = true;
                
                // Apply Style 4 search filter (if exists) - search ONLY in post title
                if (searchTerm !== '') {
                    var postTitle = ($card.data('post-title') || '').toLowerCase();
                    if (postTitle.indexOf(searchTerm) === -1) {
                        shouldShow = false;
                    }
                }
                
                // Apply general search filter (if exists) - search ONLY in post title
                if (shouldShow && generalSearchTerm !== '') {
                    var postTitle = ($card.data('post-title') || '').toLowerCase();
                    if (postTitle.indexOf(generalSearchTerm) === -1) {
                        shouldShow = false;
                    }
                }
                
                // Apply dropdown filter
                if (shouldShow && selectedValue !== '') {
                    var filterValue = $card.attr('data-filter-value') || $card.data('filter-value') || '';
                    // Convert both to strings for comparison to handle type mismatches
                    var filterValueStr = String(filterValue);
                    var selectedValueStr = String(selectedValue);
                    
                    // Debug: uncomment to see values in console
                    // console.log('Filter Value:', filterValueStr, 'Selected Value:', selectedValueStr, 'Match:', filterValueStr === selectedValueStr);
                    
                    if (filterValueStr !== selectedValueStr) {
                        shouldShow = false;
                    }
                }
                
                // Show/hide card
                if (shouldShow) {
                    $card.fadeIn(200);
                    visibleCount++;
                } else {
                    $card.fadeOut(200);
                }
            });
            
            // Update results count
            window.updateStyle4ResultsCount(widgetId, visibleCount);
            
            // Show/hide "no results" message
            var $noResults = $activeTab.find('.omsar-style4-no-results');
            // Also check in the widget if not found in tab
            if ($noResults.length === 0) {
                $noResults = $widget.find('.omsar-style4-no-results');
            }
            if (visibleCount === 0 && (searchTerm !== '' || generalSearchTerm !== '' || selectedValue !== '')) {
                if ($noResults.length === 0) {
                    var noResultsText = 'No posts found matching your filters.';
                    // Append to the grid container or active tab
                    var $gridContainer = $activeTab.find('.omsar-projects-grid, .omsar-posts-grid').first();
                    if ($gridContainer.length > 0) {
                        $gridContainer.after('<div class="omsar-style4-no-results"><p>' + noResultsText + '</p></div>');
                    } else {
                        $activeTab.append('<div class="omsar-style4-no-results"><p>' + noResultsText + '</p></div>');
                    }
                }
            } else {
                $noResults.remove();
            }
        }

        /**
         * Update results count display
         * Made globally accessible for use in other scripts
         */
        window.updateStyle4ResultsCount = function(widgetId, count) {
            if (!widgetId) {
                console.warn('updateStyle4ResultsCount: widgetId is missing');
                return;
            }
            
            var $widget = $('#' + widgetId);
            if ($widget.length === 0) {
                console.warn('updateStyle4ResultsCount: Widget not found with ID:', widgetId);
                return;
            }
            
            var $resultsCount = $widget.find('.omsar-style4-results-count');
            if ($resultsCount.length === 0) {
                // Try finding by data attribute as fallback
                $resultsCount = $('.omsar-style4-results-count[data-widget-id="' + widgetId + '"]');
            }
            
            if ($resultsCount.length > 0) {
                var $countNumber = $resultsCount.find('.omsar-results-count-number');
                
                console.log('updateStyle4ResultsCount:', {
                    widgetId: widgetId,
                    count: count,
                    resultsCountFound: $resultsCount.length,
                    countNumberFound: $countNumber.length
                });
                
                if ($countNumber.length > 0) {
                    // Update the number directly
                    $countNumber.text(count);
                    console.log('Updated count number to:', count);
                } else {
                    // If number span doesn't exist, create it
                    var countText = $resultsCount.data('count-text') || $resultsCount.attr('data-count-text') || 'Number of results:';
                    var $countText = $resultsCount.find('.omsar-results-count-text');
                    
                    console.log('Count number span not found, creating it. Count text:', countText);
                    
                    if ($countText.length > 0) {
                        // Check if text contains {count} placeholder
                        if (countText.indexOf('{count}') !== -1) {
                            $countText.html(countText.replace('{count}', '<span class="omsar-results-count-number">' + count + '</span>'));
                        } else {
                            // Append the number if no placeholder
                            $countText.append(' <span class="omsar-results-count-number">' + count + '</span>');
                        }
                        console.log('Created count number span with value:', count);
                    } else {
                        console.warn('Count text element not found');
                    }
                }
            } else {
                console.warn('updateStyle4ResultsCount: Results count element not found for widget:', widgetId);
            }
        };

        /**
         * Initialize results count on page load and tab switch
         */
        function initializeStyle4ResultsCount(widgetId) {
            if (!widgetId) {
                return;
            }
            
            var $widget = $('#' + widgetId);
            if ($widget.length === 0) {
                console.warn('initializeStyle4ResultsCount: Widget not found with ID:', widgetId);
                return;
            }
            
            var $activeTab = $widget.find('.tab-pane.active');
            if ($activeTab.length === 0) {
                // Try to find the first visible tab
                $activeTab = $widget.find('.tab-pane').first();
            }
            
            if ($activeTab.length > 0) {
                // Count all posts in the active tab (not just visible ones)
                // This ensures we get the correct count on initial load
                var $postCards = $activeTab.find('.omsar-post-card');
                var totalCount = $postCards.length;
                
                // If filters are active, count only visible posts
                var $filters = $widget.find('.omsar-style4-filters, .omsar-style4-filters-container');
                var $searchInput = $filters.find('.omsar-style4-search-input');
                var $dropdown = $filters.find('.omsar-style4-dropdown');
                // Also check general search input
                var $generalSearch = $widget.find('.omsar-posts-search-input');
                
                var hasActiveFilters = false;
                if ($searchInput.length > 0 && $searchInput.val().trim() !== '') {
                    hasActiveFilters = true;
                }
                if ($generalSearch.length > 0 && $generalSearch.val().trim() !== '') {
                    hasActiveFilters = true;
                }
                if ($dropdown.length > 0 && $dropdown.val() !== '') {
                    hasActiveFilters = true;
                }
                
                var countToShow = totalCount;
                if (hasActiveFilters) {
                    // Count only visible posts when filters are active
                    countToShow = $activeTab.find('.omsar-post-card:visible').length;
                }
                
                // Debug logging
                console.log('initializeStyle4ResultsCount:', {
                    widgetId: widgetId,
                    totalCount: totalCount,
                    hasActiveFilters: hasActiveFilters,
                    countToShow: countToShow,
                    postCardsFound: $postCards.length
                });
                
                // Update the count
                window.updateStyle4ResultsCount(widgetId, countToShow);
            } else {
                console.warn('initializeStyle4ResultsCount: No active tab found for widget:', widgetId);
            }
        }


        // Initialize results count on page load for all Style 4 widgets
        // Use multiple attempts to ensure DOM is fully rendered
        function initAllResultsCounts() {
            // Find all Style 4 widgets with results count (both Posts by Taxonomy and Projects widgets)
            $('.omsar-posts-by-taxonomy-widget, .omsar-projects-widget').each(function() {
                var $widget = $(this);
                var widgetId = $widget.attr('id');
                var $resultsCount = $widget.find('.omsar-style4-results-count');
                
                if (widgetId && $resultsCount.length > 0) {
                    // Initialize count for this widget
                    initializeStyle4ResultsCount(widgetId);
                }
            });
        }
        
        // Try multiple times to ensure DOM is ready
        $(window).on('load', function() {
            setTimeout(initAllResultsCounts, 100);
        });
        setTimeout(initAllResultsCounts, 200);
        setTimeout(initAllResultsCounts, 500);
        setTimeout(initAllResultsCounts, 1000);

        // Handle clickable cards
        $(document).on('click', '.omsar-card-clickable', function(e) {
            var $card = $(this);
            var cardLink = $card.data('card-link') || $card.attr('data-card-link');
            
            // Don't navigate if clicking on interactive elements (though they should be disabled)
            if ($(e.target).closest('a, button, input, select, textarea').length > 0) {
                return;
            }
            
            if (cardLink && cardLink !== 'javascript:void(0);') {
                window.location.href = cardLink;
            }
        });
    });
})(jQuery);

