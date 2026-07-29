/**
 * Posts by Taxonomy Widget Load More Functionality
 * 
 * Handles AJAX loading of more posts when Load More button is clicked
 */
(function($) {
    'use strict';

    if (window.omsarTaxonomyLoadMoreInitialized) {
        return;
    }
    window.omsarTaxonomyLoadMoreInitialized = true;

    function appendUniqueCards($grid, html) {
        if (!html || !html.trim()) {
            return;
        }

        var $wrapper = $('<div>').html(html);
        $wrapper.find('.omsar-post-card').each(function() {
            var $card = $(this);
            var postLink = $card.data('post-link') || $card.find('.omsar-post-title a').first().attr('href');

            if (postLink) {
                var isDuplicate = $grid.find('.omsar-post-card').filter(function() {
                    var existingLink = $(this).data('post-link') || $(this).find('.omsar-post-title a').first().attr('href');
                    return existingLink === postLink;
                }).length > 0;

                if (isDuplicate) {
                    return;
                }
            }

            $grid.append($card);
        });
    }

    $(document).ready(function() {
        // Handle Load More button click
        $(document).on('click', '.omsar-load-more-btn', function(e) {
            e.preventDefault();

            var $button = $(this);
            if ($button.prop('disabled') || $button.hasClass('loading')) {
                return;
            }
            var $wrapper = $button.closest('.omsar-load-more-wrapper');
            var $grid = $wrapper.siblings('.omsar-posts-grid');
            var widgetId = $button.data('widget-id');
            var tab = $button.data('tab');
            
            // Disable button during load
            $button.prop('disabled', true).addClass('loading');
            
            // Get data from grid
            var currentPage = parseInt($grid.data('current-page')) || 1;
            var postsPerPage = parseInt($grid.data('posts-per-page')) || 6;
            var totalPosts = parseInt($grid.data('total-posts')) || 0;
            var postType = $grid.data('post-type');
            var taxonomy = $grid.data('taxonomy');
            var termId = $grid.data('term-id') || 0;
            var termName = $grid.data('term-name') || '';
            var useAcf = $grid.data('use-acf') === '1' || $grid.data('use-acf') === 1;
            var acfField = $grid.data('acf-field') || '';
            // Check for Projects widget tabs_acf_field
            var tabsAcfField = $grid.data('tabs-acf-field') || '';
            if (tabsAcfField && !acfField) {
                acfField = tabsAcfField;
                useAcf = true;
            }
            var listingStyle = $grid.data('listing-style');
            var orderby = $grid.data('orderby');
            var order = $grid.data('order');
            var defaultImage = $grid.data('default-image') || '';
            var badgeSource = $grid.data('badge-source') || 'none';
            var badgeTaxonomy = $grid.data('badge-taxonomy') || '';
            var badgeCustomField = $grid.data('badge-custom-field') || '';
            var badgeTaxonomyDisplay = $grid.data('badge-taxonomy-display') || 'name';
            var readMoreText = $grid.data('read-more-text') || '';
            var showReadMore = $grid.data('show-read-more') === 'yes' || $grid.data('show-read-more') === true || $grid.data('show-read-more') === '1';
            var clickableCards = $grid.data('clickable-cards') === '1' || $grid.data('clickable-cards') === 1 || $grid.data('clickable-cards') === true || $grid.data('clickable-cards') === 'yes';
            
            // Calculate next page and offset
            var nextPage = currentPage + 1;
            var offset = currentPage * postsPerPage;
            
            // Get current filter values before AJAX request
            var $widget = $grid.closest('.omsar-posts-by-taxonomy-widget, .omsar-projects-widget');
            var $filters = $widget.find('.omsar-style4-filters-container, .omsar-style4-filters');
            var $searchInput = $widget.find('.omsar-posts-search-input');
            var $dropdown = $widget.find('.omsar-style4-dropdown');
            var currentSearchTerm = $searchInput.length > 0 ? $searchInput.val().trim() : '';
            var currentDropdownValue = $dropdown.length > 0 ? $dropdown.val() : '';
            
            // Get dropdown filter settings from widget
            var dropdownFilterTaxonomy = $grid.data('dropdown-filter-taxonomy') || '';
            var dropdownFilterCustomField = $grid.data('dropdown-filter-custom-field') || '';
            
            // Get custom field filter parameters
            var customFieldMetaKey = $grid.data('custom-field-meta-key') || '';
            var customFieldMetaValue = $grid.data('custom-field-meta-value') || '';
            
            // Get Style 5 display options
            var style5ContentOverlay = $grid.data('style5-content-overlay') === '1' || $grid.data('style5-content-overlay') === 1 || $grid.data('style5-content-overlay') === true;
            var style5SideLayout = $grid.data('style5-side-layout') === '1' || $grid.data('style5-side-layout') === 1 || $grid.data('style5-side-layout') === true;
            var style5ShowCategories = $grid.data('style5-show-categories') === '1' || $grid.data('style5-show-categories') === 1 || $grid.data('style5-show-categories') === true;
            var style5ShowExcerpt = $grid.data('style5-show-excerpt') === '1' || $grid.data('style5-show-excerpt') === 1 || $grid.data('style5-show-excerpt') === true;
            var style5ShowDate = $grid.data('style5-show-date') === '1' || $grid.data('style5-show-date') === 1 || $grid.data('style5-show-date') === true;
            var style5ShowAuthor = $grid.data('style5-show-author') === '1' || $grid.data('style5-show-author') === 1 || $grid.data('style5-show-author') === true;
            
            // Prepare AJAX data
            var ajaxData = {
                action: 'omsar_load_more_taxonomy_posts',
                widget_id: widgetId,
                tab: tab,
                post_type: postType,
                taxonomy: taxonomy,
                term_id: termId,
                term_name: termName,
                use_acf: useAcf ? 1 : 0,
                acf_field: acfField,
                listing_style: listingStyle,
                posts_per_page: postsPerPage,
                offset: offset,
                current_page: currentPage, // Add current page for server-side verification
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
            
            // Add filter parameters to AJAX data if Style 4
            if (listingStyle === 'style4') {
                ajaxData.filter_search_term = currentSearchTerm;
                ajaxData.filter_dropdown_value = currentDropdownValue;
                ajaxData.filter_dropdown_taxonomy = dropdownFilterTaxonomy;
                ajaxData.filter_dropdown_custom_field = dropdownFilterCustomField;
            }
            
            // Get dropdown filter settings from widget
            var dropdownFilterTaxonomy = $grid.data('dropdown-filter-taxonomy') || '';
            var dropdownFilterCustomField = $grid.data('dropdown-filter-custom-field') || '';
            
            // Add filter parameters to AJAX data
            if (listingStyle === 'style4') {
                ajaxData.filter_search_term = currentSearchTerm;
                ajaxData.filter_dropdown_value = currentDropdownValue;
                ajaxData.filter_dropdown_taxonomy = dropdownFilterTaxonomy;
                ajaxData.filter_dropdown_custom_field = dropdownFilterCustomField;
            }
            
            // Make AJAX request
            $.ajax({
                url: omsarTaxonomyLoadMore?.ajaxurl || ajaxurl,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    if (response.success && response.data) {
                        // Append new posts to grid if any
                        if (response.data.html && response.data.html.trim() !== '') {
                            appendUniqueCards($grid, response.data.html);
                        }
                        
                        // Update current page
                        $grid.data('current-page', nextPage);
                        
                        // Update total posts count if provided (for filtered results)
                        if (response.data.total_filtered_posts !== undefined) {
                            $grid.data('total-posts', response.data.total_filtered_posts);
                        }
                        
                        // Update results count if Style 4
                        if (listingStyle === 'style4') {
                            var widgetId = $widget.attr('id');
                            if (widgetId && typeof window.updateStyle4ResultsCount === 'function') {
                                // Wait a bit for DOM to update, then count
                                setTimeout(function() {
                                    var $postCards = $widget.find('.omsar-post-card');
                                    var totalCount = $postCards.length;
                                    
                                    // Check if filters are active
                                    var hasActiveFilters = false;
                                    if ($searchInput.length > 0 && $searchInput.val().trim() !== '') {
                                        hasActiveFilters = true;
                                    }
                                    if ($dropdown.length > 0 && $dropdown.val() !== '') {
                                        hasActiveFilters = true;
                                    }
                                    
                                    var countToShow = hasActiveFilters ? $widget.find('.omsar-post-card:visible').length : totalCount;
                                    window.updateStyle4ResultsCount(widgetId, countToShow);
                                }, 300);
                            }
                        }
                        
                        // Update load more button based on server response
                        if (response.data.has_more) {
                            // Use updated total from response if available, otherwise use original total
                            var totalFiltered = response.data.total_filtered_posts !== undefined && response.data.total_filtered_posts !== null && response.data.total_filtered_posts > 0 
                                ? response.data.total_filtered_posts 
                                : totalPosts;
                            var loadedSoFar = $widget.find('.omsar-post-card').length;
                            var remaining = totalFiltered - loadedSoFar;
                            
                            if (remaining > 0) {
                                $button.find('.omsar-load-more-count').text('(' + remaining + ' ' + (omsarTaxonomyLoadMore?.moreText || 'more') + ')');
                                $button.prop('disabled', false).removeClass('loading');
                            } else {
                                // Hide button if no more posts (safety check)
                                $wrapper.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        } else {
                            // Hide button if no more posts
                            $wrapper.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                    } else {
                        // Error handling
                        $button.prop('disabled', false).removeClass('loading');
                        if (response.data && response.data.message) {
                            alert(response.data.message);
                        }
                    }
                },
                error: function() {
                    $button.prop('disabled', false).removeClass('loading');
                    alert('Error loading more posts. Please try again.');
                }
            });
        });
        
        // Hide load more button when switching tabs
        $(document).on('shown.bs.tab', '.omsar-taxonomy-tabs button[data-bs-toggle="tab"]', function() {
            // Reset any loading states
            $('.omsar-load-more-btn').prop('disabled', false).removeClass('loading');
        });

        // Make dynamically loaded cards clickable when enabled
        $(document).on('click', '.omsar-post-card-clickable', function(e) {
            // Ignore clicks on nested links/buttons to preserve their behavior
            if ($(e.target).closest('a, button').length === 0) {
                var postLink = $(this).data('post-link');
                if (postLink) {
                    window.location.href = postLink;
                }
            }
        });
    });

})(jQuery);

