<?php

namespace Breakdance\Themeless;

use function Breakdance\Render\render;

/**
 * For block-enabled themes, block rendering needs to run
 * before <head> so that blocks can add scripts and styles in wp_head().
 *
 * @see wp-includes/template-canvas.php Which is used by WP to render block-enabled themes
 */

$templateId = ThemelessController::getInstance()->popHierarchy();

$renderedHeader = get_breakdance_header_template_for_request();
$renderedTemplateHtml = render($templateId);
$renderedFooter = get_breakdance_footer_template_for_request();

$headerHtml = get_header_for_theme_simulator_having_breakdance_template_for_request($renderedHeader);

if (is_null($headerHtml)) {
    get_header();
} else {
    outputHeadHtml();
}

// Footer needs to be rendered after wp_head
// @see \WP_Scripts::do_item Which registers certain assets to footer
$footerHtml = get_footer_for_theme_simulator_having_breakdance_template_for_request($renderedFooter);

echo $headerHtml;

echo $renderedTemplateHtml;

echo $footerHtml;

if (is_null($footerHtml)) {
    get_footer();
} else {
    outputFootHtml();
}

