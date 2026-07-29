<?php

namespace Breakdance\WooCommerce;

/**
 * @param string $pageName
 * @param string $elementLabel
 * @param string $pageLabel
 */
function getErrorMessageForWooElementPageInWrongPage($pageName, $elementLabel, $pageLabel){
    $pageId = wc_get_page_id($pageName);

    /* translators: %1$s is the element label, %2$s is the WooCommerce page label */
    $line1 = sprintf(__('The <b>"%1$s"</b> element can only be added to the <b>WooCommerce %2$s Page</b>.', 'breakdance'), $elementLabel, $pageLabel);
    /* translators: %s is the WooCommerce page label */
    $line2 = sprintf(__('Set the <b>WooCommerce %s Page</b> in the WP admin at <b>WooCommerce &gt; Settings &gt; Advanced &gt; Page Setup</b>.', 'breakdance'), $pageLabel);

    if ($pageId === -1){
        /* translators: %s is the WooCommerce page label */
        $noPageSet = sprintf(__('No page has been set as the <b>WooCommerce %s Page</b>.', 'breakdance'), $pageLabel);
        echo <<<HTML
            <div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error">
                <div>
                    $line1<br /><br />
                    $noPageSet<br /><br />
                    $line2
                </div>
            </div>
        HTML;
    }
    else {
        $page = get_post($pageId);
        $pageTitle = $page && is_object($page) ? '"' . $page->post_title . '"' : "";

        /* translators: %1$s is the WooCommerce page label, %2$s is the page title, %3$d is the page ID */
        $currentPage = sprintf(__('The <b>WooCommerce %1$s Page</b> is currently set to <b>%2$s (ID: %3$d)</b>.', 'breakdance'), $pageLabel, $pageTitle, $pageId);
        echo <<<HTML
            <div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error">
                <div>
                    $line1<br /><br />
                    $currentPage<br /><br />
                    $line2
                </div>
            </div>
        HTML;
    }
}
