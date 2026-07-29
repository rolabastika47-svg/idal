<?php


namespace Breakdance\Integrations\TranslatePress;

/**
 * @param boolean $shouldOverride
 * @return boolean
 */
function disableBreakdanceForEditTranslationScreen($shouldOverride)
{
    if (isset($_GET['trp-edit-translation']) && $_GET['trp-edit-translation'] === 'true') {
        return false;
    }

    return $shouldOverride;
}

add_action('breakdance_should_override_template', '\Breakdance\Integrations\TranslatePress\disableBreakdanceForEditTranslationScreen');
