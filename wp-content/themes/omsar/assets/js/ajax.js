/**
 * AJAX Functionality for OMSAR Theme
 * 
 * This file contains all AJAX-related JavaScript functionality.
 */

(function($) {
    'use strict';

    // News Filter Functionality (Month/Year)
    var newsFilterTimeout;
    $(document).on('change', '#month-filter, #year-filter', function() {
        var $monthFilter = $('#month-filter');
        var $yearFilter = $('#year-filter');
        var $container = $('#news-cards-container');
        var $loadMoreBtn = $('#load-more-news-btn');
        var postType = $loadMoreBtn.data('post-type') || 'news';
        var perPage = parseInt($loadMoreBtn.data('per-page')) || 8;
        
        var filterMonth = $monthFilter.val() || '';
        var filterYear = $yearFilter.val() || '';
        
        // Clear any existing timeout
        clearTimeout(newsFilterTimeout);
        
        // Show loading state
        $container.html('<div class="col-12 text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $loadMoreBtn.hide();
        
        // Debounce the filter request
        newsFilterTimeout = setTimeout(function() {
            $.ajax({
                url: omsarAjax.ajaxurl,
                type: 'POST',
                timeout: 30000,
                cache: false,
                data: {
                    action: 'filter_news',
                    nonce: omsarAjax.nonce,
                    post_type: postType,
                    per_page: perPage,
                    filter_month: filterMonth,
                    filter_year: filterYear
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        var htmlContent = response.data.html || '';
                        var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
                        var total = response.data.total || 0;
                        
                        // Update container with filtered results
                        if (htmlContent && htmlContent.trim() !== '') {
                            $container.html(htmlContent);
                        } else {
                            $container.html('<div class="col-12"><p>No news found for the selected filters.</p></div>');
                        }
                        
                        // Reset load more button
                        $loadMoreBtn.data('page', 1);
                        $loadMoreBtn.data('total', total);
                        $loadMoreBtn.data('filter-month', filterMonth);
                        $loadMoreBtn.data('filter-year', filterYear);
                        $loadMoreBtn.text(omsarAjax.loadMoreText || 'Load More').removeClass('loading').prop('disabled', false);
                        
                        // Animate filtered cards
                        var $filteredCards = $container.find('.news-card-item');
                        if ($filteredCards.length > 0 && typeof window.animateNewNewsCards === 'function') {
                            // Remove existing animation classes and re-animate
                            $filteredCards.removeClass('card-animated');
                            setTimeout(function() {
                                window.animateNewNewsCards($filteredCards);
                            }, 50);
                        }
                        
                        // Animate load more button if visible
                        if (hasMore && total > perPage) {
                            $loadMoreBtn.show().removeClass('button-animated');
                            setTimeout(function() {
                                $loadMoreBtn.addClass('button-animated');
                            }, 500);
                        } else {
                            $loadMoreBtn.hide();
                        }
                    } else {
                        $container.html('<div class="col-12"><p>Error loading filtered news. Please try again.</p></div>');
                    }
                },
                error: function(xhr, status, error) {
                    $container.html('<div class="col-12"><p>Error loading filtered news. Please try again.</p></div>');
                    console.error('Filter News Error:', status, error);
                }
            });
        }, 300); // 300ms debounce
    });

    // Events Filter Functionality (Month/Year) - filters by custom_date field
    var eventsFilterTimeout;
    $(document).on('change', '#month-filter, #year-filter', function() {
        // Check if we're on events page by looking for events container
        var $container = $('#events-cards-container');
        
        // Only proceed if we have the events container (not news or workshop page)
        if ($container.length === 0) {
            return;
        }
        
        var $loadMoreBtn = $('#load-more-events-btn');
        var $monthFilter = $('#month-filter');
        var $yearFilter = $('#year-filter');
        var postType = ($loadMoreBtn.length > 0) ? ($loadMoreBtn.data('post-type') || 'events') : 'events';
        var perPage = ($loadMoreBtn.length > 0) ? (parseInt($loadMoreBtn.data('per-page')) || 8) : 8;
        
        var filterMonth = $monthFilter.val() || '';
        var filterYear = $yearFilter.val() || '';
        
        // Clear any existing timeout
        clearTimeout(eventsFilterTimeout);
        
        // Show loading state
        $container.html('<div class="col-12 text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        if ($loadMoreBtn.length > 0) {
            $loadMoreBtn.hide();
        }
        
        // Debounce the filter request
        eventsFilterTimeout = setTimeout(function() {
            $.ajax({
                url: omsarAjax.ajaxurl,
                type: 'POST',
                timeout: 30000,
                cache: false,
                data: {
                    action: 'filter_events',
                    nonce: omsarAjax.eventsNonce,
                    post_type: postType,
                    per_page: perPage,
                    filter_month: filterMonth,
                    filter_year: filterYear
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        var htmlContent = response.data.html || '';
                        var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
                        var total = response.data.total || 0;
                        
                        // Update container with filtered results
                        if (htmlContent && htmlContent.trim() !== '') {
                            $container.html(htmlContent);
                            
                            // Animate filtered cards
                            var $filteredCards = $container.find('.news-card-item');
                            if ($filteredCards.length > 0 && typeof window.animateNewMediaCards === 'function') {
                                // Remove existing animation classes and re-animate
                                $filteredCards.removeClass('card-animated');
                                setTimeout(function() {
                                    window.animateNewMediaCards($filteredCards);
                                }, 50);
                            }
                        } else {
                            $container.html('<div class="col-12"><p>No events found for the selected filters.</p></div>');
                        }
                        
                        // Reset load more button if it exists
                        if ($loadMoreBtn.length > 0) {
                            $loadMoreBtn.data('page', 1);
                            $loadMoreBtn.data('total', total);
                            $loadMoreBtn.data('filter-month', filterMonth);
                            $loadMoreBtn.data('filter-year', filterYear);
                            $loadMoreBtn.text(omsarAjax.loadMoreText || 'Load More').removeClass('loading').prop('disabled', false);
                            
                            // Animate load more button if visible
                            if (hasMore && total > perPage) {
                                $loadMoreBtn.show().removeClass('button-animated');
                                setTimeout(function() {
                                    $loadMoreBtn.addClass('button-animated');
                                }, 500);
                            } else {
                                $loadMoreBtn.hide();
                            }
                        }
                    } else {
                        $container.html('<div class="col-12"><p>Error loading filtered events. Please try again.</p></div>');
                    }
                },
                error: function(xhr, status, error) {
                    $container.html('<div class="col-12"><p>Error loading filtered events. Please try again.</p></div>');
                    console.error('Filter Events Error:', status, error);
                }
            });
        }, 300); // 300ms debounce
    });

    // Load More News Functionality (Optimized)
    $(document).on('click', '#load-more-news-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $container = $('#news-cards-container');
        var currentPage = parseInt($button.data('page')) || 1; // Current page (starts at 1 for initial load)
        var postType = $button.data('post-type') || 'news';
        var perPage = parseInt($button.data('per-page')) || 8;
        var filterMonth = $button.data('filter-month') || $('#month-filter').val() || '';
        var filterYear = $button.data('filter-year') || $('#year-filter').val() || '';
        
        // Prevent multiple simultaneous requests
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return false;
        }
        
        // Calculate next page to request
        var nextPage = currentPage + 1;
        
        // Disable button and show loading state immediately
        $button.addClass('loading').prop('disabled', true).text(omsarAjax.loadingText || 'Loading...');
        
        // Optimized AJAX request with timeout
        var ajaxRequest = $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000, // 30 second timeout
            cache: false, // Prevent caching
            data: {
                action: 'load_more_news',
                nonce: omsarAjax.nonce,
                page: nextPage, // Request the next page
                post_type: postType,
                per_page: perPage,
                filter_month: filterMonth,
                filter_year: filterYear
            },
            beforeSend: function() {
                // Scroll to button to show loading state
                if ($(window).scrollTop() < $button.offset().top - 200) {
                    $('html, body').animate({
                        scrollTop: $button.offset().top - 100
                    }, 300);
                }
            }
        });
        
        ajaxRequest.done(function(response) {
            // Always remove loading state first
            $button.removeClass('loading').prop('disabled', false);
            
            // Validate response structure
            if (!response || !response.success || !response.data) {
                console.error('Load More: Invalid response structure', response);
                $button.text(omsarAjax.loadMoreText || 'Load More');
                return;
            }
            
            var htmlContent = response.data.html || '';
            var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
            
            // If we have HTML content, append it (this handles all cases including last batch)
            if (htmlContent && htmlContent.trim() !== '') {
                try {
                    var $newItems = $(htmlContent);
                    
                    // Only append if we have valid items
                    if ($newItems.length > 0) {
                        $container.append($newItems);
                        
                        // Animate newly loaded cards
                        if (typeof window.animateNewNewsCards === 'function') {
                            window.animateNewNewsCards($newItems);
                        }
                        
                        // Smooth scroll to first new item (only if there are more posts to load)
                        if (hasMore && $newItems.length > 0) {
                            var firstNewItem = $newItems.first();
                            setTimeout(function() {
                                if (firstNewItem.length && firstNewItem.offset()) {
                                    $('html, body').animate({
                                        scrollTop: firstNewItem.offset().top - 100
                                    }, 500);
                                }
                            }, 100);
                        }
                    } else {
                        console.warn('Load More: HTML content exists but no valid items found');
                    }
                } catch (e) {
                    console.error('Load More: Error parsing HTML', e, htmlContent);
                }
            } else {
                // No HTML content - might be the last batch with no posts or an error
                console.warn('Load More: No HTML content returned', {
                    has_more: hasMore,
                    total: response.data.total,
                    loaded: response.data.loaded,
                    count: response.data.count
                });
            }
            
            // Update button data with the page we just loaded (not next_page)
            // This ensures we track the current page correctly
            $button.data('page', nextPage);
            
            // Debug logging
            console.log('Load More Debug:', {
                requestedPage: nextPage,
                returnedPage: response.data.next_page,
                hasMore: hasMore,
                total: response.data.total,
                loaded: response.data.loaded,
                count: response.data.count,
                buttonPage: $button.data('page')
            });
            
            // Handle button state based on whether there are more posts
            if (!hasMore) {
                // No more posts - hide button after a short delay to ensure content is visible
                setTimeout(function() {
                    $button.fadeOut(300);
                }, 200);
            } else {
                // More posts available - re-enable button
                $button.text(omsarAjax.loadMoreText || 'Load More');
            }
        });
        
        ajaxRequest.fail(function(xhr, status, error) {
            $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
            
            if (status === 'timeout') {
                console.error('AJAX Timeout: Request took too long');
                alert('Request timed out. Please try again.');
            } else {
                console.error('AJAX Error:', status, error);
                alert('Error loading more posts. Please try again.');
            }
        });
    });

    // Recruitments Status Filter Functionality (tabs: Open, Upcoming, Closed — default active: Open; pagination resets per tab, Load More)
    $(document).on('click', '.omsar-recruitments-widget .omsar-recruitment-status-tab', function(e) {
        e.preventDefault();
        
        var $tab = $(this);
        if ($tab.hasClass('active')) {
            return;
        }
        var statusFilter = $tab.attr('data-status') || 'open';
        var $filters = $tab.closest('.omsar-recruitments-filters');
        var $widget = $filters.closest('.omsar-recruitments-widget');
        if (!$widget.length) {
            return;
        }
        var $container = $widget.find('.omsar-recruitments-grid');
        var $loadMoreWrapper = $widget.find('.omsar-recruitment-load-more-wrapper');
        var $loadMoreBtn = $widget.find('.omsar-recruitment-load-more-btn');
        
        // Update active tab (style only; tabs are fixed)
        $filters.find('.omsar-recruitment-status-tab').removeClass('active').attr('aria-selected', 'false');
        $tab.addClass('active').attr('aria-selected', 'true');
        
        // Show loading state
        $container.addClass('loading');
        var perPage = parseInt($widget.attr('data-posts-per-page'), 10) || 9;
        var orderby = $widget.attr('data-orderby') || 'meta_value';
        var order = $widget.attr('data-order') || 'DESC';
        var columns = $widget.attr('data-columns') || '3';
        var backgroundType = $widget.attr('data-background-type') || 'image';
        var backgroundImage = $widget.attr('data-background-image') || '';
        var backgroundOverlay = $widget.attr('data-background-overlay') || 'no';
        
        // Request first page for this tab only (pagination resets when switching tabs)
        $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'load_more_recruitments',
                nonce: omsarAjax.recruitmentsNonce,
                page: 1,
                per_page: perPage,
                orderby: orderby,
                order: order,
                columns: columns,
                background_type: backgroundType,
                background_image: backgroundImage,
                background_overlay: backgroundOverlay,
                status_filter: statusFilter
            },
            success: function(response) {
                $container.removeClass('loading');
                
                if (!response || !response.success || !response.data) {
                    console.error('Filter Recruitments: Invalid response structure', response);
                    return;
                }
                
                var htmlContent = response.data.html || '';
                var hasMore = response.data.has_more === true || response.data.has_more === 1;
                var total = parseInt(response.data.total, 10) || 0;
                $container.html(htmlContent);
                $loadMoreBtn.data('page', 1).data('status', statusFilter).data('has-more', hasMore ? 1 : 0).data('total', total);
                if (hasMore) {
                    $loadMoreWrapper.show();
                } else {
                    $loadMoreWrapper.hide();
                }
            },
            error: function(xhr, status, error) {
                $container.removeClass('loading');
                console.error('Filter Recruitments Error:', status, error);
                alert('Error filtering recruitments. Please try again.');
            }
        });
    });

    // Recruitments Load More (per active tab, respects posts_per_page)
    $(document).on('click', '.omsar-recruitments-widget .omsar-recruitment-load-more-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return;
        }
        var $widget = $button.closest('.omsar-recruitments-widget');
        if (!$widget.length) {
            return;
        }
        var $container = $widget.find('.omsar-recruitments-grid');
        var $loadMoreWrapper = $widget.find('.omsar-recruitment-load-more-wrapper');
        var statusFilter = $widget.find('.omsar-recruitment-status-tab.active').attr('data-status') || $button.data('status') || 'open';
        var currentPage = parseInt($button.data('page'), 10) || 1;
        var perPage = parseInt($widget.attr('data-posts-per-page'), 10) || 9;
        var orderby = $widget.attr('data-orderby') || 'meta_value';
        var order = $widget.attr('data-order') || 'DESC';
        var columns = $widget.attr('data-columns') || '3';
        var backgroundType = $widget.attr('data-background-type') || 'image';
        var backgroundImage = $widget.attr('data-background-image') || '';
        var backgroundOverlay = $widget.attr('data-background-overlay') || 'no';
        var nextPage = currentPage + 1;
        var loadMoreText = (typeof omsarAjax !== 'undefined' && omsarAjax.loadMoreText) ? omsarAjax.loadMoreText : 'Load More';
        $button.addClass('loading').prop('disabled', true).text(loadMoreText + '...');
        $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'load_more_recruitments',
                nonce: omsarAjax.recruitmentsNonce,
                page: nextPage,
                per_page: perPage,
                orderby: orderby,
                order: order,
                columns: columns,
                background_type: backgroundType,
                background_image: backgroundImage,
                background_overlay: backgroundOverlay,
                status_filter: statusFilter
            },
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false).text(loadMoreText);
                if (!response || !response.success || !response.data) {
                    console.error('Load More Recruitments: Invalid response structure', response);
                    return;
                }
                var htmlContent = response.data.html || '';
                var hasMore = response.data.has_more === true || response.data.has_more === 1;
                var total = parseInt(response.data.total, 10) || 0;
                if (htmlContent && htmlContent.trim() !== '') {
                    $container.append(htmlContent);
                }
                $button.data('page', nextPage).data('has-more', hasMore ? 1 : 0).data('total', total);
                if (hasMore) {
                    $loadMoreWrapper.show();
                } else {
                    $loadMoreWrapper.hide();
                }
            },
            error: function(xhr, status, error) {
                $button.removeClass('loading').prop('disabled', false).text(loadMoreText);
                if (status === 'timeout') {
                    alert('Request timed out. Please try again.');
                } else {
                    alert('Error loading more recruitments. Please try again.');
                }
            }
        });
    });

    // Helper: save current tab content and Load More state to cache (so switching back restores content and pagination)
    function omsarProcurementSaveTabCache($widget) {
        var cache = $widget.data('procurementTabCache') || {};
        var $activeTab = $widget.find('.omsar-procurement-status-tab.active');
        var status = $activeTab.length ? $activeTab.attr('data-status') || 'open' : 'open';
        var $container = $widget.find('.omsar-procurement-notices-grid');
        var $btn = $widget.find('.omsar-procurement-load-more-btn');
        cache[status] = {
            html: $container.length ? $container.html() : '',
            page: $btn.length ? (parseInt($btn.data('page'), 10) || 1) : 1,
            has_more: $btn.length ? ($btn.data('has-more') === 1 || $btn.data('has-more') === '1') : false,
            total: $btn.length ? (parseInt($btn.data('total'), 10) || 0) : 0
        };
        $widget.data('procurementTabCache', cache);
    }

    // Prime cache for default (Open) tab on widgets that are in the DOM
    $(document).ready(function() {
        $('.omsar-procurement-notices-widget').each(function() {
            var $widget = $(this);
            if (!$widget.data('procurementTabCache')) {
                $widget.data('procurementTabCache', {});
                omsarProcurementSaveTabCache($widget);
            }
        });
    });

    // Procurement Notices Status Filter Functionality (tabs: Open, Cancelled, Closed — default active: Open; pagination per tab, Load More)
    $(document).on('click', '.omsar-procurement-status-tab', function(e) {
        e.preventDefault();
        
        var $tab = $(this);
        if ($tab.hasClass('active')) {
            return;
        }
        var statusFilter = $tab.attr('data-status') || 'open';
        var $filters = $tab.closest('.omsar-procurement-filters');
        var $widget = $filters.closest('.omsar-procurement-notices-widget');
        var $container = $widget.find('.omsar-procurement-notices-grid');
        var $loadMoreWrapper = $widget.find('.omsar-procurement-load-more-wrapper');
        var $loadMoreBtn = $widget.find('.omsar-procurement-load-more-btn');
        
        if (!$widget.data('procurementTabCache')) {
            $widget.data('procurementTabCache', {});
        }
        var cache = $widget.data('procurementTabCache');
        
        omsarProcurementSaveTabCache($widget);
        
        $filters.find('.omsar-procurement-status-tab').removeClass('active').attr('aria-selected', 'false');
        $tab.addClass('active').attr('aria-selected', 'true');
        
        // If we have cached content for this tab, restore it and Load More state (no AJAX)
        if (cache[statusFilter] && cache[statusFilter].html !== undefined) {
            $container.html(cache[statusFilter].html || '');
            $loadMoreBtn.data('page', cache[statusFilter].page).data('status', statusFilter)
                .data('has-more', cache[statusFilter].has_more ? 1 : 0).data('total', cache[statusFilter].total || 0);
            if (cache[statusFilter].has_more) {
                $loadMoreWrapper.show();
            } else {
                $loadMoreWrapper.hide();
            }
            return;
        }
        
        // No cache: fetch first page for this tab (posts_per_page from widget)
        $container.addClass('loading');
        var perPage = parseInt($widget.attr('data-posts-per-page'), 10) || 6;
        var orderby = $widget.attr('data-orderby') || 'date';
        var order = $widget.attr('data-order') || 'DESC';
        var columns = $widget.attr('data-columns') || '3';
        var defaultImage = $widget.attr('data-default-image') || '';
        var gradientEnabled = $widget.attr('data-gradient-enabled') || 'no';
        
        $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'load_more_procurements',
                nonce: omsarAjax.procurementsNonce,
                page: 1,
                per_page: perPage,
                orderby: orderby,
                order: order,
                columns: columns,
                default_image: defaultImage,
                gradient_enabled: gradientEnabled,
                status_filter: statusFilter
            },
            success: function(response) {
                $container.removeClass('loading');
                
                if (!response || !response.success || !response.data) {
                    console.error('Filter Procurements: Invalid response structure', response);
                    return;
                }
                
                var htmlContent = response.data.html || '';
                var hasMore = response.data.has_more === true || response.data.has_more === 1;
                var total = parseInt(response.data.total, 10) || 0;
                if (htmlContent && htmlContent.trim() !== '') {
                    $container.html(htmlContent);
                } else {
                    var noResultsMsg = (typeof omsarAjax !== 'undefined' && omsarAjax.noProcurementsMessage) 
                        ? omsarAjax.noProcurementsMessage 
                        : 'No procurement notices are available at this time. Please check back later.';
                    $container.html('<div class="omsar-procurement-no-results"><p class="omsar-procurement-no-results-message">' + noResultsMsg + '</p></div>');
                }
                
                cache = $widget.data('procurementTabCache') || {};
                cache[statusFilter] = { html: $container.html(), page: 1, has_more: hasMore, total: total };
                $widget.data('procurementTabCache', cache);
                $loadMoreBtn.data('page', 1).data('status', statusFilter).data('has-more', hasMore ? 1 : 0).data('total', total);
                if (hasMore) {
                    $loadMoreWrapper.show();
                } else {
                    $loadMoreWrapper.hide();
                }
            },
            error: function(xhr, status, error) {
                $container.removeClass('loading');
                console.error('Filter Procurements Error:', status, error);
                alert('Error filtering procurement notices. Please try again.');
            }
        });
    });

    // Procurement Notices Load More (per active tab, respects posts_per_page)
    $(document).on('click', '.omsar-procurement-load-more-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return;
        }
        var $widget = $button.closest('.omsar-procurement-notices-widget');
        var $container = $widget.find('.omsar-procurement-notices-grid');
        var $loadMoreWrapper = $widget.find('.omsar-procurement-load-more-wrapper');
        var statusFilter = $widget.find('.omsar-procurement-status-tab.active').attr('data-status') || $button.data('status') || 'open';
        var currentPage = parseInt($button.data('page'), 10) || 1;
        var perPage = parseInt($widget.attr('data-posts-per-page'), 10) || 6;
        var orderby = $widget.attr('data-orderby') || 'date';
        var order = $widget.attr('data-order') || 'DESC';
        var columns = $widget.attr('data-columns') || '3';
        var defaultImage = $widget.attr('data-default-image') || '';
        var gradientEnabled = $widget.attr('data-gradient-enabled') || 'no';
        var nextPage = currentPage + 1;

        $button.addClass('loading').prop('disabled', true).text(omsarAjax.loadingText || 'Loading...');

        $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'load_more_procurements',
                nonce: omsarAjax.procurementsNonce,
                page: nextPage,
                per_page: perPage,
                orderby: orderby,
                order: order,
                columns: columns,
                default_image: defaultImage,
                gradient_enabled: gradientEnabled,
                status_filter: statusFilter
            },
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
                if (!response || !response.success || !response.data) {
                    return;
                }
                var htmlContent = response.data.html || '';
                var hasMore = response.data.has_more === true || response.data.has_more === 1;
                var total = parseInt(response.data.total, 10) || 0;
                if (htmlContent && htmlContent.trim() !== '') {
                    $container.append(htmlContent);
                }
                var cache = $widget.data('procurementTabCache') || {};
                if (!cache[statusFilter]) {
                    cache[statusFilter] = {};
                }
                cache[statusFilter].html = $container.html();
                cache[statusFilter].page = nextPage;
                cache[statusFilter].has_more = hasMore;
                cache[statusFilter].total = total;
                $widget.data('procurementTabCache', cache);
                $button.data('page', nextPage).data('has-more', hasMore ? 1 : 0).data('total', total).data('status', statusFilter);
                if (hasMore) {
                    $loadMoreWrapper.show();
                } else {
                    $loadMoreWrapper.hide();
                }
            },
            error: function(xhr, status, error) {
                $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
                console.error('Procurement Load More Error:', status, error);
                alert('Error loading more procurement notices. Please try again.');
            }
        });
    });

    // Knowledge and Resources: clickable cards (card click goes to single post; download links still work)
    $(document).on('click', '.omsar-knowledge-resources-widget .omsar-kr-card--clickable', function(e) {
        if ($(e.target).closest('a').length) {
            return;
        }
        var href = $(this).data('href');
        if (href) {
            window.location.href = href;
        }
    });

    // Knowledge and Resources search (debounced)
    var omsarKrSearchDebounce;
    $(document).on('input', '.omsar-kr-search-input', function() {
        var $input = $(this);
        var $widget = $input.closest('.omsar-knowledge-resources-widget');
        clearTimeout(omsarKrSearchDebounce);
        omsarKrSearchDebounce = setTimeout(function() {
            var search = $input.val().trim();
            var $container = $widget.find('.omsar-kr-list');
            var $noResults = $widget.find('.omsar-kr-no-results');
            var $loadMoreWrapper = $widget.find('.omsar-kr-load-more-wrapper');
            var $button = $loadMoreWrapper.find('.omsar-kr-load-more-btn');
            var perPage = parseInt($widget.attr('data-posts-per-page'), 10) || 6;
            var orderby = $widget.attr('data-orderby') || 'date';
            var order = $widget.attr('data-order') || 'DESC';
            var defaultImage = $widget.attr('data-default-image') || '';
            var cardsClickable = $widget.attr('data-cards-clickable') === '1';

            $.ajax({
                url: omsarAjax.ajaxurl,
                type: 'POST',
                timeout: 30000,
                cache: false,
                data: {
                    action: 'load_more_knowledge_resources',
                    nonce: omsarAjax.knowledgeResourcesNonce,
                    page: 1,
                    per_page: perPage,
                    orderby: orderby,
                    order: order,
                    default_image: defaultImage,
                    search: search,
                    cards_clickable: cardsClickable ? '1' : '0'
                }
            }).done(function(response) {
                if (!response || !response.success || !response.data) return;
                var noResults = response.data.no_results === true;
                var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
                var htmlContent = response.data.html || '';

                $container.html(htmlContent);
                if (noResults) {
                    $noResults.show().find('.omsar-kr-no-results-message').text(response.data.no_results_message || '');
                    $loadMoreWrapper.hide();
                } else {
                    $noResults.hide();
                    if (hasMore && $button.length) {
                        $button.data('page', 1).data('total', response.data.total || 0);
                        $loadMoreWrapper.show();
                    } else {
                        $loadMoreWrapper.hide();
                    }
                }
            }).fail(function() {
                $container.empty();
                $noResults.show().find('.omsar-kr-no-results-message').text(omsarAjax.krSearchError || 'Error loading search results.');
                $loadMoreWrapper.hide();
            });
        }, 300);
    });

    // Load More Knowledge and Resources
    $(document).on('click', '.omsar-kr-load-more-btn', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $widget = $button.closest('.omsar-knowledge-resources-widget');
        var $container = $widget.find('.omsar-kr-list');
        var $searchInput = $widget.find('.omsar-kr-search-input');
        var currentPage = parseInt($button.data('page'), 10) || 1;
        var perPage = $widget.attr('data-posts-per-page') ? parseInt($widget.attr('data-posts-per-page'), 10) : (parseInt($button.data('per-page'), 10) || 6);
        var orderby = $widget.attr('data-orderby') || $button.data('orderby') || 'date';
        var order = $widget.attr('data-order') || $button.data('order') || 'DESC';
        var defaultImage = $widget.attr('data-default-image') || $button.data('default-image') || '';
        var cardsClickable = ($widget.attr('data-cards-clickable') === '1' || $button.data('cards-clickable') === 1 || $button.data('cards-clickable') === '1');
        var search = $searchInput.length ? $searchInput.val().trim() : '';

        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return false;
        }

        var nextPage = currentPage + 1;
        $button.addClass('loading').prop('disabled', true).text(omsarAjax.loadingText || 'Loading...');

        $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: {
                action: 'load_more_knowledge_resources',
                nonce: omsarAjax.knowledgeResourcesNonce,
                page: nextPage,
                per_page: perPage,
                orderby: orderby,
                order: order,
                default_image: defaultImage,
                search: search,
                cards_clickable: cardsClickable ? '1' : '0'
            },
            beforeSend: function() {
                if ($(window).scrollTop() < $button.offset().top - 200) {
                    $('html, body').animate({ scrollTop: $button.offset().top - 100 }, 300);
                }
            }
        }).done(function(response) {
            $button.removeClass('loading').prop('disabled', false);
            var loadMoreLabel = omsarAjax.loadMoreText || 'Load More';
            if (!response || !response.success || !response.data) {
                $button.text(loadMoreLabel);
                return;
            }
            var htmlContent = response.data.html || '';
            var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
            if (htmlContent && htmlContent.trim() !== '') {
                try {
                    var $wrap = $('<div>').html(htmlContent);
                    var $newItems = $wrap.children();
                    if ($newItems.length) {
                        $container.append($newItems);
                        if (hasMore && $newItems.length) {
                            var $first = $newItems.first();
                            setTimeout(function() {
                                if ($first.length && $first.offset()) {
                                    $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 500);
                                }
                            }, 100);
                        }
                    }
                } catch (err) {
                    console.error('Load More Knowledge and Resources: parse error', err, htmlContent);
                }
            }
            $button.data('page', nextPage);
            $button.text(loadMoreLabel);
            if (!hasMore) {
                setTimeout(function() { $button.closest('.omsar-kr-load-more-wrapper').fadeOut(300); }, 200);
            }
        }).fail(function(xhr, status, error) {
            $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
            if (status === 'timeout') {
                alert('Request timed out. Please try again.');
            } else {
                console.error('Load More Knowledge and Resources:', status, error);
                alert('Error loading more. Please try again.');
            }
        });
    });

    // Procurement Apply Modal 
    // Use DOM ready and lazy lookups so modal works when widget is in the page
    $(function() {
        function getApplyModal() {
            return $('#omsar-procurement-apply-modal');
        }

        function openProcurementApplyModal(procurementId) {
            var $modal = getApplyModal();
            if (!$modal.length) {
                return;
            }
            var $form = $('#omsar-procurement-apply-form');
            var $idInput = $('#omsar-procurement-apply-id');
            var $emailInput = $('#omsar-procurement-apply-email');
            var $error = $('#omsar-procurement-apply-email-error');
            var $success = $('#omsar-procurement-apply-success');
            $idInput.val(procurementId || '');
            if ($form.length && $form[0].reset) {
                $form[0].reset();
            }
            $error.text('');
            $emailInput.removeClass('error');
            $form.find('.omsar-procurement-apply-form-group').show();
            $form.find('.omsar-procurement-apply-form-actions').show();
            $success.hide().text('');
            $modal.attr('aria-hidden', 'false').addClass('active');
            $('body').addClass('omsar-modal-open');
            setTimeout(function() {
                $emailInput.focus();
            }, 100);
        }

        function closeProcurementApplyModal() {
            var $modal = getApplyModal();
            if ($modal.length) {
                $modal.attr('aria-hidden', 'true').removeClass('active');
                var $form = $('#omsar-procurement-apply-form');
                var $success = $('#omsar-procurement-apply-success');
                $form.find('.omsar-procurement-apply-form-group').show();
                $form.find('.omsar-procurement-apply-form-actions').show();
                $success.hide().text('');
            }
            $('body').removeClass('omsar-modal-open');
        }

        $(document).on('click', '.omsar-procurement-apply-button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            var shouldPopup = ($btn.attr('data-procurement-popup') === '1');
            if (shouldPopup) {
                openProcurementApplyModal($btn.data('procurement-id') || '');
                return;
            }
            var rawLink = $btn.attr('data-procurement-link') || '';
            if (!rawLink || rawLink.trim() === '') {
                return;
            }
            window.open(rawLink, '_blank', 'noopener,noreferrer');
        });

        $(document).on('click', '#omsar-procurement-apply-modal .omsar-procurement-apply-modal-overlay, #omsar-procurement-apply-modal .omsar-procurement-apply-modal-close', function() {
            closeProcurementApplyModal();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && getApplyModal().hasClass('active')) {
                closeProcurementApplyModal();
            }
        });

        $(document).on('submit', '#omsar-procurement-apply-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $emailInput = $('#omsar-procurement-apply-email');
            var $error = $('#omsar-procurement-apply-email-error');
            var $submitBtn = $form.find('.omsar-procurement-apply-submit-btn');
            var email = $emailInput.val().trim();
            var procurementId = $('#omsar-procurement-apply-id').val() || '';
            var msgRequired = (typeof omsarAjax !== 'undefined' && omsarAjax.procurementApplyEmailRequired) ? omsarAjax.procurementApplyEmailRequired : 'Please enter your email.';
            var msgInvalid = (typeof omsarAjax !== 'undefined' && omsarAjax.procurementApplyEmailInvalid) ? omsarAjax.procurementApplyEmailInvalid : 'Please enter a valid email address.';
            if (!email) {
                $error.text(msgRequired);
                $emailInput.addClass('error').focus();
                return;
            }
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $error.text(msgInvalid);
                $emailInput.addClass('error').focus();
                return;
            }
            $error.text('');
            $emailInput.removeClass('error');
            if (!$submitBtn.length || $submitBtn.prop('disabled')) {
                return;
            }
            $submitBtn.prop('disabled', true);
            $.ajax({
                url: typeof omsarAjax !== 'undefined' ? omsarAjax.ajaxurl : '',
                type: 'POST',
                data: {
                    action: 'submit_procurement_apply',
                    nonce: typeof omsarAjax !== 'undefined' ? omsarAjax.procurementApplyNonce : '',
                    email: email,
                    procurement_id: procurementId
                },
                success: function(response) {
                    $submitBtn.prop('disabled', false);
                    if (response && response.success) {
                        var $success = $('#omsar-procurement-apply-success');
                        var msg = (response.data && response.data.message) ? response.data.message : (typeof omsarAjax !== 'undefined' && omsarAjax.procurementApplySuccessMessage ? omsarAjax.procurementApplySuccessMessage : 'Thank you. Your application has been submitted successfully.');
                        $form.find('.omsar-procurement-apply-form-group').hide();
                        $form.find('.omsar-procurement-apply-form-actions').hide();
                        $success.text(msg).css('display', 'block');
                    } else {
                        var errMsg = (response && response.data && response.data.message) ? response.data.message : (typeof omsarAjax !== 'undefined' && omsarAjax.procurementApplyErrorGeneric ? omsarAjax.procurementApplyErrorGeneric : 'An error occurred. Please try again.');
                        $error.text(errMsg).show();
                        $emailInput.addClass('error');
                    }
                },
                error: function(xhr, status, err) {
                    $submitBtn.prop('disabled', false);
                    var errMsg = (typeof omsarAjax !== 'undefined' && omsarAjax.procurementApplyErrorGeneric) ? omsarAjax.procurementApplyErrorGeneric : 'An error occurred. Please try again.';
                    $error.text(errMsg).show();
                    $emailInput.addClass('error');
                }
            });
        });
    });

    // Load More Events Functionality (Optimized)
    $(document).on('click', '#load-more-events-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $container = $('#events-cards-container');
        var currentPage = parseInt($button.data('page')) || 1; // Current page (starts at 1 for initial load)
        var postType = $button.data('post-type') || 'events';
        var perPage = parseInt($button.data('per-page')) || 8;
        var filterMonth = $button.data('filter-month') || $('#month-filter').val() || '';
        var filterYear = $button.data('filter-year') || $('#year-filter').val() || '';
        
        // Prevent multiple simultaneous requests
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return false;
        }
        
        // Calculate next page to request
        var nextPage = currentPage + 1;
        
        // Disable button and show loading state immediately
        $button.addClass('loading').prop('disabled', true).text(omsarAjax.loadingText || 'Loading...');
        
        // Optimized AJAX request with timeout
        var ajaxRequest = $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000, // 30 second timeout
            cache: false, // Prevent caching
            data: {
                action: 'load_more_events',
                nonce: omsarAjax.eventsNonce,
                page: nextPage, // Request the next page
                post_type: postType,
                per_page: perPage,
                filter_month: filterMonth,
                filter_year: filterYear
            },
            beforeSend: function() {
                // Scroll to button to show loading state
                if ($(window).scrollTop() < $button.offset().top - 200) {
                    $('html, body').animate({
                        scrollTop: $button.offset().top - 100
                    }, 300);
                }
            }
        });
        
        ajaxRequest.done(function(response) {
            // Always remove loading state first
            $button.removeClass('loading').prop('disabled', false);
            
            // Validate response structure
            if (!response || !response.success || !response.data) {
                console.error('Load More Events: Invalid response structure', response);
                $button.text(omsarAjax.loadMoreText || 'Load More');
                return;
            }
            
            var htmlContent = response.data.html || '';
            var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
            
            // If we have HTML content, append it (this handles all cases including last batch)
            if (htmlContent && htmlContent.trim() !== '') {
                try {
                    var $newItems = $(htmlContent);
                    
                    // Only append if we have valid items
                    if ($newItems.length > 0) {
                        $container.append($newItems);
                        
                        // Animate newly loaded cards
                        if (typeof window.animateNewMediaCards === 'function') {
                            window.animateNewMediaCards($newItems);
                        }
                        
                        // Smooth scroll to first new item (only if there are more posts to load)
                        if (hasMore && $newItems.length > 0) {
                            var firstNewItem = $newItems.first();
                            setTimeout(function() {
                                if (firstNewItem.length && firstNewItem.offset()) {
                                    $('html, body').animate({
                                        scrollTop: firstNewItem.offset().top - 100
                                    }, 500);
                                }
                            }, 100);
                        }
                    } else {
                        console.warn('Load More Events: HTML content exists but no valid items found');
                    }
                } catch (e) {
                    console.error('Load More Events: Error parsing HTML', e, htmlContent);
                }
            } else {
                // No HTML content - might be the last batch with no posts or an error
                console.warn('Load More Events: No HTML content returned', {
                    has_more: hasMore,
                    total: response.data.total,
                    loaded: response.data.loaded,
                    count: response.data.count
                });
            }
            
            // Update button data with the page we just loaded (not next_page)
            // This ensures we track the current page correctly
            $button.data('page', nextPage);
            
            // Debug logging
            console.log('Load More Events Debug:', {
                requestedPage: nextPage,
                returnedPage: response.data.next_page,
                hasMore: hasMore,
                total: response.data.total,
                loaded: response.data.loaded,
                count: response.data.count,
                buttonPage: $button.data('page')
            });
            
            // Handle button state based on whether there are more posts
            if (!hasMore) {
                // No more posts - hide button after a short delay to ensure content is visible
                setTimeout(function() {
                    $button.fadeOut(300);
                }, 200);
            } else {
                // More posts available - re-enable button
                $button.text(omsarAjax.loadMoreText || 'Load More');
            }
        });
        
        ajaxRequest.fail(function(xhr, status, error) {
            $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
            
            if (status === 'timeout') {
                console.error('AJAX Timeout: Request took too long');
                alert('Request timed out. Please try again.');
            } else {
                console.error('AJAX Error:', status, error);
                alert('Error loading more events. Please try again.');
            }
        });
    });

    // Workshop Filter Functionality (Month/Year) - filters by custom_date field
    var workshopFilterTimeout;
    $(document).on('change', '#month-filter, #year-filter', function() {
        // Check if we're on workshop page by looking for workshop container and button
        var $container = $('#workshop-cards-container');
        var $loadMoreBtn = $('#load-more-workshop-btn');
        
        // Only proceed if we have the workshop elements (not news or events page)
        if ($container.length === 0 || $loadMoreBtn.length === 0) {
            return;
        }
        
        var $monthFilter = $('#month-filter');
        var $yearFilter = $('#year-filter');
        var postType = $loadMoreBtn.data('post-type') || 'workshops';
        var perPage = parseInt($loadMoreBtn.data('per-page')) || 8;
        
        var filterMonth = $monthFilter.val() || '';
        var filterYear = $yearFilter.val() || '';
        
        // Clear any existing timeout
        clearTimeout(workshopFilterTimeout);
        
        // Show loading state
        $container.html('<div class="col-12 text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $loadMoreBtn.hide();
        
        // Debounce the filter request
        workshopFilterTimeout = setTimeout(function() {
            $.ajax({
                url: omsarAjax.ajaxurl,
                type: 'POST',
                timeout: 30000,
                cache: false,
                data: {
                    action: 'filter_workshops',
                    nonce: omsarAjax.workshopNonce,
                    post_type: postType,
                    per_page: perPage,
                    filter_month: filterMonth,
                    filter_year: filterYear
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        var htmlContent = response.data.html || '';
                        var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
                        var total = response.data.total || 0;
                        
                        // Update container with filtered results
                        if (htmlContent && htmlContent.trim() !== '') {
                            $container.html(htmlContent);
                            
                            // Animate filtered cards
                            var $filteredCards = $container.find('.news-card-item');
                            if ($filteredCards.length > 0 && typeof window.animateNewMediaCards === 'function') {
                                // Remove existing animation classes and re-animate
                                $filteredCards.removeClass('card-animated');
                                setTimeout(function() {
                                    window.animateNewMediaCards($filteredCards);
                                }, 50);
                            }
                        } else {
                            $container.html('<div class="col-12"><p>No workshops found for the selected filters.</p></div>');
                        }
                        
                        // Reset load more button
                        $loadMoreBtn.data('page', 1);
                        $loadMoreBtn.data('total', total);
                        $loadMoreBtn.data('filter-month', filterMonth);
                        $loadMoreBtn.data('filter-year', filterYear);
                        $loadMoreBtn.text(omsarAjax.loadMoreText || 'Load More').removeClass('loading').prop('disabled', false);
                        
                        // Animate load more button if visible
                        if (hasMore && total > perPage) {
                            $loadMoreBtn.show().removeClass('button-animated');
                            setTimeout(function() {
                                $loadMoreBtn.addClass('button-animated');
                            }, 500);
                        } else {
                            $loadMoreBtn.hide();
                        }
                    } else {
                        $container.html('<div class="col-12"><p>Error loading filtered workshops. Please try again.</p></div>');
                    }
                },
                error: function(xhr, status, error) {
                    $container.html('<div class="col-12"><p>Error loading filtered workshops. Please try again.</p></div>');
                    console.error('Filter Workshops Error:', status, error);
                }
            });
        }, 300); // 300ms debounce
    });

    // Load More Workshop Functionality (Optimized)
    $(document).on('click', '#load-more-workshop-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $container = $('#workshop-cards-container');
        var currentPage = parseInt($button.data('page')) || 1; // Current page (starts at 1 for initial load)
        var postType = $button.data('post-type') || 'workshops';
        var perPage = parseInt($button.data('per-page')) || 8;
        var filterMonth = $button.data('filter-month') || $('#month-filter').val() || '';
        var filterYear = $button.data('filter-year') || $('#year-filter').val() || '';
        
        // Prevent multiple simultaneous requests
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return false;
        }
        
        // Calculate next page to request
        var nextPage = currentPage + 1;
        
        // Disable button and show loading state immediately
        $button.addClass('loading').prop('disabled', true).text(omsarAjax.loadingText || 'Loading...');
        
        // Optimized AJAX request with timeout
        var ajaxRequest = $.ajax({
            url: omsarAjax.ajaxurl,
            type: 'POST',
            timeout: 30000, // 30 second timeout
            cache: false, // Prevent caching
            data: {
                action: 'load_more_workshop',
                nonce: omsarAjax.workshopNonce,
                page: nextPage, // Request the next page
                post_type: postType,
                per_page: perPage,
                filter_month: filterMonth,
                filter_year: filterYear
            },
            beforeSend: function() {
                // Scroll to button to show loading state
                if ($(window).scrollTop() < $button.offset().top - 200) {
                    $('html, body').animate({
                        scrollTop: $button.offset().top - 100
                    }, 300);
                }
            }
        });
        
        ajaxRequest.done(function(response) {
            // Always remove loading state first
            $button.removeClass('loading').prop('disabled', false);
            
            // Validate response structure
            if (!response || !response.success || !response.data) {
                console.error('Load More Workshop: Invalid response structure', response);
                $button.text(omsarAjax.loadMoreText || 'Load More');
                return;
            }
            
            var htmlContent = response.data.html || '';
            var hasMore = response.data.has_more !== false && response.data.has_more !== 0;
            
            // If we have HTML content, append it (this handles all cases including last batch)
            if (htmlContent && htmlContent.trim() !== '') {
                try {
                    var $newItems = $(htmlContent);
                    
                    // Only append if we have valid items
                    if ($newItems.length > 0) {
                        $container.append($newItems);
                        
                        // Animate newly loaded cards
                        if (typeof window.animateNewMediaCards === 'function') {
                            window.animateNewMediaCards($newItems);
                        }
                        
                        // Smooth scroll to first new item (only if there are more posts to load)
                        if (hasMore && $newItems.length > 0) {
                            var firstNewItem = $newItems.first();
                            setTimeout(function() {
                                if (firstNewItem.length && firstNewItem.offset()) {
                                    $('html, body').animate({
                                        scrollTop: firstNewItem.offset().top - 100
                                    }, 500);
                                }
                            }, 100);
                        }
                    } else {
                        console.warn('Load More Workshop: HTML content exists but no valid items found');
                    }
                } catch (e) {
                    console.error('Load More Workshop: Error parsing HTML', e, htmlContent);
                }
            } else {
                // No HTML content - might be the last batch with no posts or an error
                console.warn('Load More Workshop: No HTML content returned', {
                    has_more: hasMore,
                    total: response.data.total,
                    loaded: response.data.loaded,
                    count: response.data.count
                });
            }
            
            // Update button data with the page we just loaded (not next_page)
            // This ensures we track the current page correctly
            $button.data('page', nextPage);
            
            // Debug logging
            console.log('Load More Workshop Debug:', {
                requestedPage: nextPage,
                returnedPage: response.data.next_page,
                hasMore: hasMore,
                total: response.data.total,
                loaded: response.data.loaded,
                count: response.data.count,
                buttonPage: $button.data('page')
            });
            
            // Handle button state based on whether there are more posts
            if (!hasMore) {
                // No more posts - hide button after a short delay to ensure content is visible
                setTimeout(function() {
                    $button.fadeOut(300);
                }, 200);
            } else {
                // More posts available - re-enable button
                $button.text(omsarAjax.loadMoreText || 'Load More');
            }
        });
        
        ajaxRequest.fail(function(xhr, status, error) {
            $button.removeClass('loading').prop('disabled', false).text(omsarAjax.loadMoreText || 'Load More');
            
            if (status === 'timeout') {
                console.error('AJAX Timeout: Request took too long');
                alert('Request timed out. Please try again.');
            } else {
                console.error('AJAX Error:', status, error);
                alert('Error loading more workshops. Please try again.');
            }
        });
    });

})(jQuery);

