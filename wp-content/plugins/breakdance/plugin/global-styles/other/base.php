<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Data\get_global_option;
use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\PresetSections\getPresetSection;

/**
 * @return Control
 */
function OTHER_SECTION()
{
    $controls = [
        c(
            "transition_duration",
            __("Transition Duration", 'breakdance'),
            [],
            ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms']]],
            false,
            false,
            [],
        )
    ];

    $skipLinkControls = c(
        "skip_link",
        __("Skip Link", 'breakdance'),
        [getPresetSection(
            "EssentialElements\\typography_with_hoverable_everything",
            __("Typography", 'breakdance'),
            "typography",
            ['type' => 'popout']
        ), c(
            "background_color",
            __("Background Color", 'breakdance'),
            [],
            ['type' => 'color', 'layout' => 'inline'],
            false,
            true,
            [],

        ), c(
            "nudge_x",
            __("Nudge X", 'breakdance'),
            [],
            ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 10, 'step' => 1]],
            true,
            false,
            [],
        ), c(
            "nudge_y",
            __("Nudge Y", 'breakdance'),
            [],
            ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 10, 'step' => 1]],
            true,
            false,
            [],
        ), getPresetSection(
            "EssentialElements\\spacing_all",
            __("Spacing", 'breakdance'),
            "spacing",
            ['type' => 'popout']
        )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],

    );

    if (get_global_option('enable_skip_link')) {
        $controls[] = $skipLinkControls;
    }

    return c(
        "other",
        __("Other", 'breakdance'),
        $controls,
        ['type' => 'section'],
        false,
        false,
        [],
    );
}

/**
 * @return string
 */
function SKIP_LINK_CSS()
{
    return (string) file_get_contents(dirname(__FILE__) . '/skip-link.css.twig');
}

/**
 * @return string
 */
function OTHER_DEFAULT_CSS()
{
    $other = (string) file_get_contents(__DIR__ . '/other-default-css.css.twig');
    $skipLink = SKIP_LINK_CSS();
    return $other . $skipLink;
}

/**
 * @return string
 */
function GLOBAL_CSS_VARS()
{
    return (string) file_get_contents(__DIR__ . '/global-css-vars.css.twig');
}
