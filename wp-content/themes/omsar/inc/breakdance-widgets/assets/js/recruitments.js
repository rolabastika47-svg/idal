/**
 * Breakdance OMSAR Recruitments — tabs and load more.
 */
(function ($) {
    'use strict';

    var widgetSelector = '.omsar-bd-recruitments-widget';

    function getCfg() {
        return window.omsarBdRecruitments || {};
    }

    function ajaxRequest(data, onSuccess, onError) {
        var cfg = getCfg();
        $.ajax({
            url: cfg.ajaxurl || '/wp-admin/admin-ajax.php',
            type: 'POST',
            timeout: 30000,
            cache: false,
            data: data,
            success: onSuccess,
            error: onError
        });
    }

    $(document).on('click', widgetSelector + ' .omsar-recruitment-status-tab', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var cfg = getCfg();
        var $tab = $(this);
        if ($tab.hasClass('active')) {
            return;
        }

        var statusFilter = $tab.attr('data-status') || 'open';
        var $widget = $tab.closest(widgetSelector);
        var $container = $widget.find('.omsar-recruitments-grid');
        var $loadMoreWrapper = $widget.find('.omsar-recruitment-load-more-wrapper');
        var $loadMoreBtn = $widget.find('.omsar-recruitment-load-more-btn');

        $widget.find('.omsar-recruitment-status-tab').removeClass('active').attr('aria-selected', 'false');
        $tab.addClass('active').attr('aria-selected', 'true');
        $container.addClass('loading');

        ajaxRequest({
            action: 'omsar_bd_load_more_recruitments',
            nonce: cfg.nonce,
            page: 1,
            per_page: parseInt($widget.attr('data-posts-per-page'), 10) || 9,
            orderby: $widget.attr('data-orderby') || 'meta_value',
            order: $widget.attr('data-order') || 'DESC',
            background_type: $widget.attr('data-background-type') || 'image',
            background_image: $widget.attr('data-background-image') || '',
            background_overlay: $widget.attr('data-background-overlay') || 'no',
            status_filter: statusFilter
        }, function (response) {
            $container.removeClass('loading');
            if (!response || !response.success || !response.data) {
                return;
            }
            var hasMore = response.data.has_more === true || response.data.has_more === 1;
            var total = parseInt(response.data.total, 10) || 0;
            $container.html(response.data.html || '');
            $loadMoreBtn.data('page', 1).data('status', statusFilter).data('has-more', hasMore ? 1 : 0).data('total', total);
            $loadMoreWrapper.toggle(hasMore);
        }, function () {
            $container.removeClass('loading');
            alert(cfg.filterError || 'Error filtering recruitments. Please try again.');
        });
    });

    $(document).on('click', widgetSelector + ' .omsar-recruitment-load-more-btn', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var cfg = getCfg();
        var $button = $(this);
        if ($button.hasClass('loading') || $button.prop('disabled')) {
            return;
        }

        var $widget = $button.closest(widgetSelector);
        var $container = $widget.find('.omsar-recruitments-grid');
        var $loadMoreWrapper = $widget.find('.omsar-recruitment-load-more-wrapper');
        var statusFilter = $widget.find('.omsar-recruitment-status-tab.active').attr('data-status') || $button.data('status') || 'open';
        var currentPage = parseInt($button.data('page'), 10) || 1;
        var nextPage = currentPage + 1;
        var loadMoreText = cfg.loadMoreText || 'Load More';

        $button.addClass('loading').prop('disabled', true).text(loadMoreText + '...');

        ajaxRequest({
            action: 'omsar_bd_load_more_recruitments',
            nonce: cfg.nonce,
            page: nextPage,
            per_page: parseInt($widget.attr('data-posts-per-page'), 10) || 9,
            orderby: $widget.attr('data-orderby') || 'meta_value',
            order: $widget.attr('data-order') || 'DESC',
            background_type: $widget.attr('data-background-type') || 'image',
            background_image: $widget.attr('data-background-image') || '',
            background_overlay: $widget.attr('data-background-overlay') || 'no',
            status_filter: statusFilter
        }, function (response) {
            $button.removeClass('loading').prop('disabled', false).text(loadMoreText);
            if (!response || !response.success || !response.data) {
                return;
            }
            var htmlContent = response.data.html || '';
            var hasMore = response.data.has_more === true || response.data.has_more === 1;
            var total = parseInt(response.data.total, 10) || 0;
            if (htmlContent.trim() !== '') {
                $container.append(htmlContent);
            }
            $button.data('page', nextPage).data('has-more', hasMore ? 1 : 0).data('total', total);
            $loadMoreWrapper.toggle(hasMore);
        }, function (xhr, status) {
            $button.removeClass('loading').prop('disabled', false).text(loadMoreText);
            alert(status === 'timeout' ? (cfg.timeoutError || 'Request timed out.') : (cfg.loadMoreError || 'Error loading more recruitments.'));
        });
    });
}(jQuery));
