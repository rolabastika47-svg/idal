<?php

namespace Breakdance\GoogleFontsPlugin;

/*
do operations like this really have to happen for every request?
should it really be on init? or instead, maybe it sould be on some sort
of setup hook fired by breakdance before rendering... or when its a builder AJAX
request that needs these fonts?
 */

use Breakdance\Fonts\FontsController;
use function Breakdance\GoogleFontsPlugin\getVariableGoogleFont;
use function Breakdance\GoogleFontsPlugin\buildGoogleVariableFontUrl;

add_action('breakdance_register_fonts', '\Breakdance\GoogleFontsPlugin\loadGoogleFonts');

function loadGoogleFonts(FontsController $fontsController)
{
    $fonts = getFontListFromFile();

    foreach ($fonts as $font) {

        $slug = slugFromFontFamilyName((string) $font['family']);
        $cssName = '"' . (string) $font['family'] . '"';
        $dropdownLabel = (string) $font['family'];
        /** @var ElementDependencyWithoutConditions $dependencies */
        $dependencies = [
            'googleFonts' => [$font['family']],
        ];
        $previewImageUrl = generateFontPreviewUrl((string) $font['family']);
        $category = (string) $font['category'];
        $variants = $font['axes'] ?? null;

        if ($font['category'] === 'serif') {
            $fallbackString = 'serif';
        } else if ($font['category'] === 'sans-serif') {
            $fallbackString = 'sans-serif';
        } else if ($font['category'] === 'display') {
            $fallbackString = 'sans-serif';
        } else {
            $fallbackString = 'sans-serif';
        }

        $fontsController->registerFont(
            $slug,
            $cssName,
            $dropdownLabel,
            $fallbackString,
            $dependencies,
            $previewImageUrl,
            $category,
            $variants
        );
    }
}

/**
 * @param string $fontFamily
 * @return string
 */
function generateFontPreviewUrl($fontFamily)
{
    $fontFamilyUri = str_replace(' ', '%20', $fontFamily);
    return "https://raw.githubusercontent.com/khoben/gfont-previews/master/output/previews/" . $fontFamilyUri . "-regular.png";
}

/**
 * @return GoogleFont[]
 * @psalm-suppress MixedInferredReturnType
 */
function getFontListFromFile()
{
    static $fonts = null;

    if ($fonts !== null) {
        /** @psalm-suppress MixedReturnStatement */
        return $fonts;
    }

    if (!is_readable(\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE)) {
        return [];
    }
    $fileContents = file_get_contents(\Breakdance\Fonts\Consts::GOOGLE_FONT_FILE);

    if ($fileContents === false) {
        return [];
    }

    /**
     * @psalm-suppress MixedAssignment
     * @var array{items:GoogleFont[]}
     */
    $validated_google_font_data = json_decode($fileContents, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    $fonts = $validated_google_font_data['items'];

    return $validated_google_font_data['items'];
}

/**
 * @param string $fontFamily
 * @return string
 */
function slugFromFontFamilyName($fontFamily)
{
    return "gfont-" . strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $fontFamily));
}

/**
 * @param string[] $fontFamilies
 * @return string|null
 */
function buildGoogleFontUrl($fontFamilies)
{
    if (empty($fontFamilies)) {
        return null;
    }

    $fontQueries = array_map(function ($fontFamily) {
        $variableFont = getVariableGoogleFont($fontFamily);

        if ($variableFont) {
            return buildGoogleVariableFontUrl($variableFont);
        } else {
            // Build static font query with all weights and styles
            $weights = ['100', '200', '300', '400', '500', '600', '700', '800', '900'];
            $normal = [];
            $italic = [];

            // 0,400; 0,700; 1,400
            // Add normal and italic weights
            foreach ($weights as $weight) {
                $normal[] = '0,' . $weight;
                $italic[] = '1,' . $weight;
            }

            $styles = [
                implode(';', $normal),
                implode(';', $italic),
            ];

            return sprintf('family=%s:ital,wght@%s', $fontFamily, implode(';', $styles));
        }
    }, array_unique($fontFamilies));

    $googleFontUrl = 'https://fonts.googleapis.com/css2?' . implode('&', $fontQueries) . '&display=swap';

    /**
     * @var string
     */
    return bdox_run_filters('breakdance_google_fonts_url', $googleFontUrl);
}
