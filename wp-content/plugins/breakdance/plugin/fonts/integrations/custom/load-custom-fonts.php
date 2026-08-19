<?php

namespace Breakdance\CustomFonts;

use Breakdance\Fonts\FontsController;

use function Breakdance\Preferences\get_preferences;

add_action('breakdance_register_fonts', '\Breakdance\CustomFonts\loadCustomFonts');

/**
 * @param array<string, CustomFontFamily> $font
 * @return array<int, int>|null
 */
function getWeightAxisRangeFromCustomFont($font) {
    $faces = $font['faces'] ?? [];
    foreach ($faces as $face) {
        if (!empty($face['minWeight']) && !empty($face['maxWeight'])) {
            return [
                'tag' => 'wght',
                'start' => $face['minWeight'],
                'end' => $face['maxWeight'],
            ];
        }
    }
    return null;
}

function getWidthAxisRangeFromCustomFont($font) {
    $faces = $font['faces'] ?? [];
    foreach ($faces as $face) {
        if (!empty($face['minWidth']) && !empty($face['maxWidth'])) {
            return [
                'tag' => 'wdth',
                'start' => $face['minWidth'],
                'end' => $face['maxWidth'],
            ];
        }
    }
    return null;
}

/**
 * @param array $font
 * @return BreakdanceFontVariants
 */
function getVariantsFromCustomFont($font)
{
    return array_values(array_filter([
        getWeightAxisRangeFromCustomFont($font),
        getWidthAxisRangeFromCustomFont($font),
    ]));
}

function loadCustomFonts(FontsController $fontsController)
{

    // By adding the fonts to the top of the array with registerFontAtTheStar
    // They end up 'last in array' as 'first in font list'
    // So we reverse the custom fonts to have them as newest-to-oldest instead
    $fonts = array_reverse(get_preferences()['customFonts']);

    foreach ($fonts as $font) {
        $variants = getVariantsFromCustomFont($font);

        // Add the custom fonts at the start of the list
        $fontsController->registerFontAtTheStart(
            $font['id'],
            addQuotesToCssNameIfNecessary($font['cssName']),
            $font['family'],
            $font['fallbackString'],
            [
                'styles' => [$font['cssUrl']]
            ],
            null,
            null,
            $variants,
        );

    }

}
