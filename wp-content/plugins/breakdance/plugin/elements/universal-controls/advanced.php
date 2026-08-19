<?php

namespace Breakdance\Elements\UniversalControls;

use function Breakdance\CustomCSS\getResponsiveCssControl;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control[]
 */
function cssControls() {
    return [
        getResponsiveCssControl(),
        control(
            'classes',
            __('Classes', 'breakdance'),
            ['type' => 'class', 'layout' => 'vertical']
        )
    ];
}

/**
 * @return Control[]
 */
function htmlControls()
{
    $htmlControls = [];

    $htmlControls[] = getIdHtmlControl();
    $htmlControls[] = getAttributesHtmlControl();

    if (BREAKDANCE_MODE === 'oxygen') {
        return $htmlControls;
    }

    $htmlControls[] = getTagHtmlControl();
    return $htmlControls;
}

/**
 * @return Control[]
 */
function draftForSections() {
    return [
        control(
            "draft",
            __("Draft", "breakdance"),
            [
                'type' => 'toggle',
                'layout' => 'inline',
                'condition' => [
                    'path' => '$element.slug',
                    'operand' => 'equals',
                    'value' => 'EssentialElements\\Section',
                ]
            ],
        )
    ];
}

/**
 * @return Control[]
 */
function wrapperStylesControls()
{
    $modifiedClassControls = \Breakdance\ClassesSelectors\controls();

    array_pop($modifiedClassControls); // remove the custom CSS section. lol.

    $modifiedClassControls = array_map(function($controlSection) {
        /**
         * @psalm-suppress MixedArrayAssignment
         */
        $controlSection['options']['sectionOptions']['type'] = 'popout';
        return $controlSection;
    }, $modifiedClassControls);

    $section = controlSection(
        'wrapper',
        __('Wrapper', 'breakdance'),
        $modifiedClassControls,
        ['isExternal' => true],
        'popout'
    );

    $section2 = controlSection(
        'wrapper_hover',
        __('Wrapper Hover', 'breakdance'),
        $modifiedClassControls,
        ['isExternal' => true],
        'popout'
    );

    return [$section, $section2];

}

add_filter('breakdance_universal_controls', '\Breakdance\Elements\UniversalControls\addAdvancedControlsToElement', 69);

/**
 * @param BuilderElementControls $controls
 * @return BuilderElementControls
 */
function addAdvancedControlsToElement($controls)
{
    $advancedControls = array_merge(
        cssControls(),
        wrapperStylesControls(),
        htmlControls(),
        draftForSections()
    );

    if (BREAKDANCE_MODE === 'oxygen') {
        $advancedControls = array_merge(
            htmlControls()
        );
    }

    $controls['settingsSections'][] = controlSection(
        'advanced',
        __('Advanced', 'breakdance'),
        $advancedControls
    );
    return $controls;
}

add_filter('breakdance_universal_css_template', '\Breakdance\Elements\UniversalControls\template', 100, 1);

/**
 * @param string $cssTemplate
 * @return string
 */
function template($cssTemplate)
{
    return $cssTemplate . "\n\n" . '
    %%SELECTOR%% {
        {{ macros.classOrSelectorProperties(
            settings.advanced.wrapper.background,
            settings.advanced.wrapper.layout,
            settings.advanced.wrapper.size,
            settings.advanced.wrapper.typography,
            globalSettings,
            settings.advanced.wrapper.spacing,
            settings.advanced.wrapper.borders,
            settings.advanced.wrapper.effects) }}
    }

    %%SELECTOR%%:hover {
        {{ macros.classOrSelectorProperties(
            settings.advanced.wrapper_hover.background,
            settings.advanced.wrapper_hover.layout,
            settings.advanced.wrapper_hover.size,
            settings.advanced.wrapper_hover.typography,
            globalSettings,
            settings.advanced.wrapper_hover.spacing,
            settings.advanced.wrapper_hover.borders,
            settings.advanced.wrapper_hover.effects) }}
    }
    ' . "\n\n" . '{{ settings.advanced.css }}' . "\n";
}
