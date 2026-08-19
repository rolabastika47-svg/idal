<?php

namespace Breakdance\Elements\UniversalControls;

require_once __DIR__ . '/html.php';
require_once __DIR__ . '/advanced.php';
require_once __DIR__ . '/hide-by-breakpoint.php';

/**
 * Get all universal settings controls for the builder
 *
 * @return array{contentSections: Control[], designSections: Control[], settingsSections: Control[]}
 */
function getUniversalControls()
{
    // Start with empty controls
    $controls = [
        'contentSections' => [],
        'designSections' => [],
        'settingsSections' => [],
    ];

    /**
     * @var array{contentSections: Control[], designSections: Control[], settingsSections: Control[]}
     */
    $controls = bdox_run_filters('breakdance_universal_controls', $controls);

    return $controls;
}

/**
 * @return string
 */
function getUniversalCssTemplate()
{
    $template = (string) bdox_run_filters('breakdance_universal_css_template', '');
    return trim($template);
}

/**
 * @return string[]
 */
function getPropertyPathsToWhitelistInFlatProps()
{
    /**
     * @var string[]
     */
    $paths = bdox_run_filters('breakdance_universal_property_paths_to_whitelist_in_flat_props', []);
    return $paths;
}
