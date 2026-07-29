<?php

namespace Breakdance\Elements\FilteredGets;

/**
 * @param \Breakdance\Elements\Element $element
 * @return string
 */
function cssTemplate($element)
{
    /** @var array<array-key, string> $cache */
    static $cache = [];

    $cache_key = $element::slug();

    if (isset($cache[$cache_key])) {
        return $cache[$cache_key];
    }

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var string
     */
    // BEWARE: JS relies on the text '\n{#%---' to exist to cleanup CSS.
    $cssTemplateWithComment = $element::cssTemplate() . "\n{#%--- Auto Generated Twig Code: ---%#}";

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @psalm-suppress TooManyArguments
     * @var string
     */
    $result = bdox_run_filters('breakdance_element_css_template', $cssTemplateWithComment, $element);
    $cache[$cache_key] = $result;

    return $cache[$cache_key];
}

/**
 * @return ElementAttribute[]
 */
function externalAttributes()
{
    static $cache = null;

    if ($cache !== null) {
        /**
         * @var ElementAttribute[]
         */
        return $cache;
    }

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var ElementAttribute[]
     */
    $cache = bdox_run_filters(
        'breakdance_element_attributes',
        []
    );
    return $cache;
}


/**
 * @return ElementDependenciesAndConditions[]
 */
function externalDependencies()
{
    static $cache = null;

    if ($cache !== null) {
        /**
         * @var ElementDependenciesAndConditions[]
         */
        return $cache;
    }

    /**
     * @psalm-suppress InvalidStaticInvocation
     * @var ElementDependenciesAndConditions[]
     */
    $cache = bdox_run_filters(
        'breakdance_element_dependencies',
        []
    );
    return $cache;
}

/**
 * @psalm-suppress MixedReturnTypeCoercion
 * @return BuilderActions[]|false
 */
function externalActions()
{
    static $cache = null;
    if ($cache !== null) {
        /**
         * @var BuilderActions[]|false
         */
        return $cache;
    }

    /**
     * @var BuilderActions[]|false
     */
    $allExternalActions = bdox_run_filters('breakdance_element_actions', []);

    if (!$allExternalActions || count($allExternalActions) === 0) {
        $cache = [];
        return $cache;
    }

    $cache = array_merge_recursive(...$allExternalActions);

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     * @var BuilderActions[]|false
     */
    return $cache;
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return mixed
 */
function defaultProperties($element)
{
    return bdox_run_filters('breakdance_element_default_properties', $element::defaultProperties());
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return BuilderElementControls
 */
function controls($element)
{
    /**
     * @psalm-suppress InvalidStaticInvocation
     * @psalm-suppress TooManyArguments
     * @var BuilderElementControls
     */
    return bdox_run_filters('breakdance_element_controls', [
        'contentSections' => $element::contentControls(),
        'designSections' => $element::designControls(),
        'settingsSections' => $element::settingsControls(),
    ], $element);
}

/**
 * @param \Breakdance\Elements\Element $element
 * @param string[] $visibleElements
 * @return mixed
 */
function addPanelRules($element, $visibleElements)
{
    /** @var mixed $addPanelRules */
    $addPanelRules = $element::addPanelRules();

    /*
        Hide the element if it's not in the visible elements array.

        Why not remove the element from the array instead?
        Because we want the element to be rendered in the builder, but hidden in the add panel.
    */

    if (!in_array($element, $visibleElements)) {
        if (!is_array($addPanelRules)) {
            $addPanelRules = [];
        }

        $addPanelRules['alwaysHide'] = true;
    }

    return $addPanelRules;
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return string[]|false
 */
function propertyPathsToWhitelistInFlatProps($element)
{
    $props = $element::propertyPathsToWhitelistInFlatProps();

    /**
     * @var string[]|false
     */
    return bdox_run_filters('breakdance_element_property_paths_to_whitelist_in_flat_props', $props ?: []);
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return DynamicPropertyPath[]
 */
function dynamicPropertyPaths($element)
{

    /**
     * @var DynamicPropertyPath[]
     */

    $paths = $element::dynamicPropertyPaths();

    /*
    we use ? : instead of ?? because: https://github.com/soflyy/breakdance/issues/6114
    */

    /**
     * @var DynamicPropertyPath[]
     */
    return bdox_run_filters('breakdance_element_dynamic_property_paths', $paths ? $paths : []);
}

/**
 * @param \Breakdance\Elements\Element $element
 * @return string|false
 */
function name($element)
{
    /**
     * @psalm-suppress InvalidStaticInvocation
     * @psalm-suppress TooManyArguments
     * @var string|false
     */
    return bdox_run_filters('breakdance_element_name', $element::name(), $element);
}
