<?php

namespace Breakdance\Variables;

use function Breakdance\BreakdanceOxygen\Selectors\getOxySelectors;
use function Breakdance\BreakdanceOxygen\Selectors\replaceVariablesInString;
use function Breakdance\Data\get_global_option;
use function Breakdance\Render\formatCss;
use function Breakdance\Render\getCss;

/**
 * @return string
 */
function cssTemplate()
{
    $macro = (string) file_get_contents(dirname(__FILE__) . "/template/macro.twig");
    $template = (string) file_get_contents(dirname(__FILE__) . "/template/global.twig");
    return $macro . "\n\n" . $template;
}

/**
 * @return string[]
 */
function propertyPathsToWhitelistInFlatProps()
{
    return [
        'variables[].type',
        'variables[].label',
        'variables[].cssVariableName',
    ];
}

/**
 * @param OxygenVariable[] $variables
 * @param PropertiesData $globalSettings
 * @return string
 */
function cssForVariables($variables, $globalSettings)
{
    $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    $cssTemplate = cssTemplate();

    $css = getCss(
        $cssTemplate,
        ['variables' => $variables],
        $breakpoints,
        [],
        propertyPathsToWhitelistInFlatProps(),
    );

    $css = replaceVariablesInString($css, $variables);
    return formatCss($css);
}

/**
 * @return OxygenVariable[]
 */
function getVariables()
{
    /**
     * @psalm-suppress MixedAssignment
     */
    $variables_json_string = \Breakdance\Data\get_global_option('variables_json_string');

    if (!$variables_json_string) {
        $variables = [];
    } else {
        // TODO: Migration code, swap the whole conditional with the commented out code one week from now. (now = 2024-01-24)
        /**
         * @var OxygenVariable[]
         */
        $variables = is_array($variables_json_string) ? $variables_json_string : json_decode((string) $variables_json_string, true);
    }

    // $variables = $variables_json_string ?: [];

    /**
     * @var OxygenVariable[]
     */
    $variables = bdox_run_filters('breakdance_variables', $variables);

    return $variables;
}

/**
 * @return string[]
 */
function getVariablesCollections()
{
    /**
     * @psalm-suppress MixedAssignment
     * @var string[]|null
     */
    $collections = get_global_option('variables_collections_json_string');

    // Auto-fill collections from variables array if empty
    if (!$collections) {
        $variables = getVariables();

        if (count($variables) > 0) {
            /** @var string[] $collections */
            $collections = array_values(array_unique(array_column($variables, 'collection')));
            return $collections;
        }
    }

    return $collections ?: [];
}

/**
 * @return array{template:string,variables:OxygenVariable[]}
 */
function getVariablesDataForBuilder()
{
    return [
        'template' => cssTemplate(),
        'variables' => getVariables(),
        'collections'=> getVariablesCollections(),
        'propertyPathsToWhitelistInFlatProps' => propertyPathsToWhitelistInFlatProps(),
    ];
}
