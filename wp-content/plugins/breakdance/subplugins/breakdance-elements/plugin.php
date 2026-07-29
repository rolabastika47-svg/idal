<?php

/**
 * Plugin Name: Breakdance Element Development
 * Plugin URI: https://breakdance.com/
 * Description: First-party Breakdance elements.
 * Author: Breakdance
 * Author URI: https://breakdance.com/
 * License: GPLv2
 * Text Domain: breakdance-elements
 * Domain Path: /languages/
 * Version: 1.0.0
 */

namespace EssentialElements;

use function Breakdance\Util\getDirectoryPathRelativeToPluginFolder;

define('BREAKDANCE_ELEMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('__BREAKDANCE_ELEMENTS_DIR__', __DIR__);

require __DIR__ . "/macros-manual/woo/base.php";
require_once __DIR__ . "/lib/base.php";

/**
 * Load plugin text domain for translations.
 * This works both when standalone and when bundled into the main Breakdance plugin.
 */
add_action('plugins_loaded', function() {
    // When bundled, this will be something like: breakdance/subplugins/breakdance-elements
    // When standalone, this will be: breakdance-elements
    $plugin_rel_path = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('breakdance-elements', false, $plugin_rel_path . '/languages');
});

add_action('breakdance_loaded', function() {
    \Breakdance\I18n\Elements\registerElementTranslations( 'EssentialElements', 'breakdance-elements' );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements',
        'EssentialElements',
        'element',
        'Breakdance Elements',
        true
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements-manual',
        'EssentialElements',
        'element',
        'Breakdance Manual Elements', 'breakdance-elements',
        true,
        true
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/macros',
        'EssentialElements',
        'macro',
        'Breakdance Macros',
        true,
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/presets',
        'EssentialElements',
        'preset',
        'Breakdance Presets',
        true,
    );

    // TODO move all presets?
    // TODO: create a manual folder? Register them? Some of them need to be done through an element instead????!
});
