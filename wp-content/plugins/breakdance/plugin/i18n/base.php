<?php

namespace Breakdance\I18n;

require_once __DIR__ . '/elements.php';

add_action( 'plugins_loaded', 'Breakdance\I18n\registerPlugin' );

/**
 * Load our plugin text domain. This will load translations for PHP code.
 * @return void
 */

function registerPlugin() {
    /** @psalm-suppress UndefinedConstant */
    $dir = dirname( plugin_basename( (string) __BREAKDANCE_PLUGIN_FILE__ ) );
    load_plugin_textdomain( 'breakdance', false, $dir . '/languages' );
}

/**
 * @return string
 */
function getLanguageAttribute() {
    $locale = str_replace("_", "-", get_user_locale());
    return 'lang="' . $locale . '"';
}

// Initialize element translation filters
add_action( 'init', 'Breakdance\I18n\Elements\init' );
