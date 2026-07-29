<?php

namespace Breakdance\ActionsFilters;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\DesignLibrary\isRequestFromDesignLibraryModal;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromGutenbergIframe;

add_filter('show_admin_bar', '\Breakdance\ActionsFilters\hide_admin_bar_if_breakdance', 2147483647);

/**
 * @param boolean $adminBarShouldShow
 * @return boolean
 */
function hide_admin_bar_if_breakdance($adminBarShouldShow)
{
    if (
        isRequestFromDesignLibraryModal() ||
        isRequestFromBuilderIframe() ||
        isRequestFromBrowserIframe() ||
        isRequestFromGutenbergIframe()
    ) {
        return false;
    }

    return $adminBarShouldShow;
}
