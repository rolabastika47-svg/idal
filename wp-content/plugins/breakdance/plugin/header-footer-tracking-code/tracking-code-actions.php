<?php

namespace Breakdance\HeaderFooterTrackingCode;

use function Breakdance\Data\get_global_option;
use function BreakdanceWPCodeBox\runSnippet;


add_action('wp_head', function () {
    breakdance_output_tracking_code('breakdance_settings_tracking_code_header');
}, PHP_INT_MAX, 1);

add_action('wp_footer', function () {
    breakdance_output_tracking_code('breakdance_settings_tracking_code_footer');
}, PHP_INT_MAX);

/**
 * Outputs tracking code or executes a CodeBox snippet.
 *
 * @param string $area Name of the global option to retrieve.
 */
function breakdance_output_tracking_code(string $area): void
{
    $code = (string) get_global_option($area);
    if (
        strpos($code, '\BreakdanceWPCodeBox\runSnippet') !== false
        && preg_match('/runSnippet\((\d+)\)/', $code, $matches)
        && isset($matches[1])
    ) {
        if (function_exists('BreakdanceWPCodeBox\runSnippet')) {
            echo (string) runSnippet((int) $matches[1]);
        }
    } else {
        echo $code;
    }
}
