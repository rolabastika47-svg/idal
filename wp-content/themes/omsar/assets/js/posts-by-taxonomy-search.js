/**
 * Posts by Taxonomy Widget Search Functionality
 * 
 * Filters posts in real-time based on search input
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle search input - use AJAX to search ALL posts, not just loaded ones
        var searchTimeout;
        $(document).on('input', '.omsar-posts-search-input', function(e) {
            var $searchInput = $(this);
            var searchTerm = $searchInput.val().toLowerCase().trim();
            var $widget = $searchInput.closest('.omsar-posts-by-taxonomy-widget');
            var widgetId = $widget.attr('id');
            
            // Check if this is Style 4 or Projects widget - if so, let style4-filters.js handle it
            var $grid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
            if ($grid.length > 0) {
                var listingStyle = $grid.data('listing-style') || '';
                var isProjectsWidget = $widget.hasClass('omsar-projects-widget');
                
                // Skip client-side filtering for Style 4 and Projects widgets (they use AJAX)
                if (listingStyle === 'style4' || isProjectsWidget) {
                    return; // Let style4-filters.js handle it via AJAX
                }
            }
            
            // For other styles, use AJAX to search ALL posts when search term is provided
            if (widgetId && $grid.length > 0) {
                if (searchTerm !== '') {
                    // Debounce AJAX calls
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        loadSearchedPosts(widgetId, searchTerm);
                    }, 300);
                    return; // Don't do client-side filtering when using AJAX
                } else {
                    // Search cleared - reload original posts via AJAX
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        loadSearchedPosts(widgetId, ''); // Empty search term to reload all
                    }, 100);
                    return;
                }
            }
            
            // Fallback: Client-side filtering (only if AJAX is not available)
            // Get ALL post cards in the widget, regardless of tabs
            var $postCards = $widget.find('.omsar-post-card');
            
            // Check if Style 4 dropdown filter is active
            var $style4Dropdown = $widget.find('.omsar-style4-dropdown');
            var selectedDropdownValue = '';
            if ($style4Dropdown.length > 0) {
                selectedDropdownValue = $style4Dropdown.val();
            }

            if (searchTerm === '' && selectedDropdownValue === '') {
                // Show all posts if both search and dropdown are empty
                $postCards.css('display', '').fadeIn(200);
            } else {
                // Filter posts by search term and dropdown (if active)
                $postCards.each(function() {
                    var $card = $(this);
                    var shouldShow = true;
                    
                    // Apply search filter - search ONLY in post title
                    if (searchTerm !== '') {
                        var postTitle = ($card.data('post-title') || '').toLowerCase();
                        if (postTitle.indexOf(searchTerm) === -1) {
                            shouldShow = false;
                        }
                    }
                    
                    // Apply dropdown filter if active
                    if (shouldShow && selectedDropdownValue !== '') {
                        var filterValue = $card.attr('data-filter-value') || $card.data('filter-value') || '';
                        var filterValueStr = String(filterValue);
                        var selectedValueStr = String(selectedDropdownValue);
                        if (filterValueStr !== selectedValueStr) {
                            shouldShow = false;
                        }
                    }
                    
                    // Show/hide card - use display property directly to prevent conflicts
                    if (shouldShow) {
                        $card.css('display', '').fadeIn(200);
                    } else {
                        $card.fadeOut(200, function() {
                            $(this).css('display', 'none');
                        });
                    }
                });
            }

            // Show/hide "no results" message
            // Find the appropriate container for the message (works with or without tabs)
            var $visibleCards = $widget.find('.omsar-post-card:visible');
            var $noResults = $widget.find('.omsar-no-results');
            
            // Determine where to place/show the no results message
            var $messageContainer = null;
            var $activeTab = $widget.find('.tab-pane.active');
            if ($activeTab.length > 0) {
                // Tabs are visible - place message in active tab
                $messageContainer = $activeTab;
                $noResults = $activeTab.find('.omsar-no-results');
            } else {
                // Tabs are hidden - place message after the grid
                var $grid = $widget.find('.omsar-posts-grid').first();
                if ($grid.length > 0) {
                    $messageContainer = $grid.parent();
                    $noResults = $messageContainer.find('.omsar-no-results');
                } else {
                    $messageContainer = $widget;
                }
            }
            
            if ($visibleCards.length === 0 && (searchTerm !== '' || selectedDropdownValue !== '')) {
                if ($noResults.length === 0 && $messageContainer.length > 0) {
                    var noResultsText = (typeof omsarPostsSearchL10n !== 'undefined' && omsarPostsSearchL10n.noResults) ? omsarPostsSearchL10n.noResults : 'No posts found matching your search.';
                    $messageContainer.append('<div class="omsar-no-results"><p>' + noResultsText + '</p></div>');
                }
            } else {
                $noResults.remove();
            }
            
            // Update Style 4 results count if it exists
            var widgetId = $widget.attr('id');
            if (widgetId && typeof window.updateStyle4ResultsCount === 'function') {
                setTimeout(function() {
                    var visibleCount = $widget.find('.omsar-post-card:visible').length;
                    window.updateStyle4ResultsCount(widgetId, visibleCount);
                }, 250);
            }
            
            // Hide/show Load More button based on filtered results
            // When searching client-side, all posts are already loaded, so hide Load More when filtering
            var $loadMoreWrapper = $widget.find('.omsar-load-more-wrapper');
            if ($loadMoreWrapper.length > 0) {
                // If search or filter is active, hide Load More (all posts are already loaded client-side)
                if (searchTerm !== '' || selectedDropdownValue !== '') {
                    // When filtering client-side, all posts are already in the DOM
                    // So there are no more posts to load - hide the button
                    $loadMoreWrapper.fadeOut(200);
                } else {
                    // No search active - restore Load More visibility based on pagination
                    var $grid = $widget.find('.omsar-posts-grid').first();
                    if ($grid.length > 0) {
                        var currentPage = parseInt($grid.data('current-page')) || 1;
                        var totalPostsData = parseInt($grid.data('total-posts')) || 0;
                        var postsPerPage = parseInt($grid.data('posts-per-page')) || 10;
                        var loadedPosts = currentPage * postsPerPage;
                        
                        // Show Load More if there are more posts to load
                        if (totalPostsData > 0 && loadedPosts < totalPostsData) {
                            $loadMoreWrapper.fadeIn(200);
                        } else {
                            $loadMoreWrapper.fadeOut(200);
                        }
                    }
                }
            }
        });

        // Clear search when switching tabs
        $(document).on('shown.bs.tab', '.omsar-taxonomy-tabs button[data-bs-toggle="tab"]', function() {
            var $widget = $(this).closest('.omsar-posts-by-taxonomy-widget');
            var $searchInput = $widget.find('.omsar-posts-search-input');
            if ($searchInput.length) {
                $searchInput.val('').trigger('input');
            }
        });
        
        // Re-apply search filter when posts are loaded/updated (for non-Style 4 widgets)
        $(document).on('omsar:posts-loaded', '.omsar-posts-by-taxonomy-widget', function() {
            var $widget = $(this);
            var $searchInput = $widget.find('.omsar-posts-search-input');
            if ($searchInput.length > 0 && $searchInput.val().trim() !== '') {
                // Check if this is Style 4 - if so, skip (it handles its own filtering)
                var $grid = $widget.find('.omsar-projects-grid, .omsar-posts-grid').first();
                if ($grid.length > 0) {
                    var listingStyle = $grid.data('listing-style') || '';
                    var isProjectsWidget = $widget.hasClass('omsar-projects-widget');
                    if (listingStyle !== 'style4' && !isProjectsWidget) {
                        // Re-trigger search to filter newly loaded posts
                        setTimeout(function() {
                            $searchInput.trigger('input');
                        }, 50);
                    }
                }
            }
        });

        /**
         * Load searched posts via AJAX to search through ALL posts
         */
        function loadSearchedPosts(widgetId, searchTerm) {
            var $widget = $('#' + widgetId);
            var $grid = $widget.find('.omsar-posts-grid').first();
            
            if ($grid.length === 0) {
                return;
            }
            
            // Get grid data attributes
            var postType = $grid.data('post-type') || 'post';
            var tab = $grid.data('tab') || 'all';
            var listingStyle = $grid.data('listing-style') || 'style1';
            var postsPerPage = parseInt($grid.data('posts-per-page')) || 10;
            var orderby = $grid.data('orderby') || 'date';
            var order = $grid.data('order') || 'DESC';
            var defaultImage = $grid.data('default-image') || '';
            var badgeSource = $grid.data('badge-source') || 'none';
            var badgeTaxonomy = $grid.data('badge-taxonomy') || '';
            var badgeCustomField = $grid.data('badge-custom-field') || '';
            var badgeTaxonomyDisplay = $grid.data('badge-taxonomy-display') || 'name';
            var readMoreText = $grid.data('read-more-text') || '';
            var showReadMore = $grid.data('show-read-more') === 'yes' || $grid.data('show-read-more') === true || $grid.data('show-read-more') === '1';
        var clickableCards = $grid.data('clickable-cards') === '1' || $grid.data('clickable-cards') === 1 || $grid.data('clickable-cards') === true || $grid.data('clickable-cards') === 'yes';
            var customFieldMetaKey = $grid.data('custom-field-meta-key') || '';
            var customFieldMetaValue = $grid.data('custom-field-meta-value') || '';
            var taxonomy = $grid.data('taxonomy') || '';
            var useAcf = $grid.data('use-acf') === '1' || $grid.data('use-acf') === 1;
            var acfField = $grid.data('acf-field') || '';
            
            // Get Style 5 display options
            var style5ContentOverlay = $grid.data('style5-content-overlay') === '1' || $grid.data('style5-content-overlay') === 1 || $grid.data('style5-content-overlay') === true;
            var style5SideLayout = $grid.data('style5-side-layout') === '1' || $grid.data('style5-side-layout') === 1 || $grid.data('style5-side-layout') === true;
            var style5ShowCategories = $grid.data('style5-show-categories') === '1' || $grid.data('style5-show-categories') === 1 || $grid.data('style5-show-categories') === true;
            var style5ShowExcerpt = $grid.data('style5-show-excerpt') === '1' || $grid.data('style5-show-excerpt') === 1 || $grid.data('style5-show-excerpt') === true;
            var style5ShowDate = $grid.data('style5-show-date') === '1' || $grid.data('style5-show-date') === 1 || $grid.data('style5-show-date') === true;
            var style5ShowAuthor = $grid.data('style5-show-author') === '1' || $grid.data('style5-show-author') === 1 || $grid.data('style5-show-author') === true;
            
            // Show loading state
            $grid.addClass('omsar-loading');
            
            // Prepare AJAX data
            var ajaxData = {
                action: 'omsar_load_more_taxonomy_posts',
                widget_id: widgetId,
                tab: tab,
                post_type: postType,
                taxonomy: taxonomy,
                term_id: '',
                term_name: '',
                use_acf: useAcf ? 1 : 0,
                acf_field: acfField,
                listing_style: listingStyle,
                posts_per_page: postsPerPage,
                offset: 0, // Start from beginning for search
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
                filter_search_term: searchTerm, // Pass search term to search ALL posts
                filter_dropdown_value: '',
                filter_dropdown_taxonomy: '',
                filter_dropdown_custom_field: '',
                custom_field_meta_key: customFieldMetaKey,
                custom_field_meta_value: customFieldMetaValue,
                style5_content_overlay: style5ContentOverlay ? '1' : '0',
                style5_side_layout: style5SideLayout ? '1' : '0',
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
                        // Replace current posts with searched results
                        $grid.html(response.data.html || '');
                        
                        // Reset pagination
                        $grid.data('current-page', 1);
                        
                        // Update total posts count
                        if (response.data.total_filtered_posts !== undefined && response.data.total_filtered_posts !== null) {
                            $grid.data('total-posts', response.data.total_filtered_posts);
                        }
                        
                        // Show/hide Load More button based on has_more
                        var $loadMoreWrapper = $widget.find('.omsar-load-more-wrapper');
                        if (response.data.has_more === true) {
                            // Show button if it exists, create if it doesn't
                            if ($loadMoreWrapper.length === 0) {
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
                        
                        // Show/hide "no results" message
                        var $noResults = $widget.find('.omsar-no-results');
                        if (response.data.html === '' || ($grid.find('.omsar-post-card').length === 0 && searchTerm !== '')) {
                            if ($noResults.length === 0) {
                                var noResultsText = (typeof omsarPostsSearchL10n !== 'undefined' && omsarPostsSearchL10n.noResults) ? omsarPostsSearchL10n.noResults : 'No posts found matching your search.';
                                var $messageContainer = $widget.find('.tab-pane.active').length > 0 ? $widget.find('.tab-pane.active') : ($grid.parent().length > 0 ? $grid.parent() : $widget);
                                $messageContainer.append('<div class="omsar-no-results"><p>' + noResultsText + '</p></div>');
                            }
                        } else {
                            $noResults.remove();
                        }
                        
                        // Trigger custom event
                        $widget.trigger('omsar:posts-loaded');
                    }
                },
                error: function() {
                    $grid.removeClass('omsar-loading');
                    // Fallback to client-side filtering if AJAX fails
                    // (This will be handled by the original client-side code below)
                }
            });
        }

        // Handle clickable cards
        $(document).on('click', '.omsar-post-card-clickable', function(e) {
            // Don't navigate if clicking on a link or button inside the card
            if ($(e.target).closest('a, button').length === 0) {
                var postLink = $(this).data('post-link');
                if (postLink) {
                    window.location.href = postLink;
                }
            }
        });
    });

})(jQuery);

