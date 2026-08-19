<?php

namespace Breakdance\GoogleFontsPlugin;

class VariableGoogleFontsRegistry
{
    use \Breakdance\Singleton;

    /**
     * @var array<string, GoogleFont>
     */
    private $fonts = [];

    protected function __construct()
    {
        foreach (getFontListFromFile() as $font) {
            if (isVariableGoogleFont($font)) {
                $this->fonts[$font['family']] = $font;
            }
        }
    }

    /**
     * @param string $fontFamily
     * @return GoogleFont|null
     */
    public function get($fontFamily)
    {
        return $this->fonts[$fontFamily] ?? null;
    }
}


/**
 * @param GoogleFont $font
 * @return bool
 */
function isVariableGoogleFont($font) {
    return isset($font['axes']) && count($font['axes']) > 0;
}

/**
 * @param GoogleFont|null $variableFont
 * @return array<float|int, float|int>|null
 */
function getWeightAxisRangeFromVariableFont($variableFont) {
    if (!$variableFont) return null;

    $axes = $variableFont['axes'] ?? [];
    foreach ($axes as $axis) {
        if ($axis['tag'] === 'wght' && !empty($axis['start']) && !empty($axis['end'])) {
            return [$axis['start'], $axis['end']];
        }
    }

    return null;
}

/**
 * Format a variable font axis value for a Google Fonts URL.
 *
 * Preserves fractional axis bounds (e.g. Noto Sans has a `wdth` axis that
 * starts at 62.5) while dropping unnecessary trailing zeros (e.g. 100.0 -> 100).
 * Using `%d` here would truncate 62.5 to 62, which is below the axis's valid
 * range and causes the Google Fonts API to reject the whole request.
 *
 * @param float|int $value
 * @return string
 */
function formatAxisValue($value)
{
    $formatted = rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    return $formatted === '' ? '0' : $formatted;
}

/**
 * @param GoogleFont $variableFont
 * @return string
 */
function buildGoogleVariableFontUrl($variableFont)
{
    // Build variable font query
    $axes = array_column($variableFont['axes'], 'tag');
    $axisRanges = array_map(function ($axis) {
        return sprintf('%s..%s', formatAxisValue($axis['start']), formatAxisValue($axis['end']));
    }, $variableFont['axes']);

    $hasItalic = in_array('italic', $variableFont['variants']);

    if ($hasItalic) {
        // Add italic axis and create values for both normal and italic styles
        array_unshift($axes, 'ital');
        $axisRangesString = implode(',', $axisRanges);
        $values = [
            '0,' . $axisRangesString,  // Normal style
            '1,' . $axisRangesString   // Italic style
        ];
        $valuesString = implode(';', $values);
    } else {
        // No italic support - just use the axis ranges
        $valuesString = implode(',', $axisRanges);
    }

    $axesString = implode(',', $axes);
    return sprintf('family=%s:%s@%s', $variableFont['family'], $axesString, $valuesString);
}

/**
 * @param string $fontFamily
 * @return GoogleFont|null
 */
function getVariableGoogleFont($fontFamily) {
    return VariableGoogleFontsRegistry::getInstance()->get($fontFamily);
}
