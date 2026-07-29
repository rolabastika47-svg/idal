<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\PresetSections\getPresetSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;
use function Breakdance\Elements\responsiveControlWithHover;

/**
 * @return Control
 */
function TYPOGRAPHY_PRESETS_SECTION()
{
    return controlSection(
        'global_typography',
        __('Presets', 'breakdance'),
        [
            repeaterControl(
                'typography_presets',
                __('Typography Presets', 'breakdance'),
                [
                    control(
                        'preset',
                        __('Preset Name', 'breakdance'),
                        ['type' => 'typography_preset', 'layout' => 'vertical']
                    ),
                    control(
                        'custom',
                        __('Typography', 'breakdance'),
                        [
                            'type' => 'custom_typography',
                            'layout' => 'vertical',
                            'noLabel' => true,
                            'customTypographyOptions' => [
                                'hoverableEverything' => false,
                                'enableMediaQueries' => true,
                            ],
                        ]
                    ),
                ],
                [
                    'repeaterOptions' => [
                        'noDuplicate' => true,
                        'titleTemplate' => '{preset?.label}',
                        'defaultTitle' => __('Preset', 'breakdance'),
                        'buttonName' => __('Add Preset', 'breakdance'),
                        'defaultNewValue' => [
                            'preset' => [
                                'label' => '',
                                'id' => '',
                            ],
                        ],
                        'deleteConfirm' => true,
                        'deleteText' => __('Are you sure you want to delete this preset?', 'breakdance'),
                    ],
                ]
            ),
        ],
        null,
        'popout'
    );
}

/**
 * @return Control
 */
function TYPOGRAPHY_SECTION()
{
    return c(
        "typography",
        __("Typography", 'breakdance'),
        [c(
            "heading_font",
            __("Heading Font", 'breakdance'),
            [],
            ['type' => 'font_family', 'layout' => 'inline'],
            false,
            false,
            [],
        ), c(
            "body_font",
            __("Body Font", 'breakdance'),
            [],
            ['type' => 'font_family', 'layout' => 'inline'],
            false,
            false,
            [],
        ),
            c(
                "base_size",
                __("Base Size", 'breakdance'),
                [],
                ['type' => 'unit', 'layout' => 'inline'],
                true,
                false,
                [],
            ),
            c(
                "ratio",
                __("Ratio", 'breakdance'),
                [],
                ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 2, 'step' => 0.01]],
                true,
                false,
                [],
            ),
            c(
                "advanced",
                __("Advanced", 'breakdance'),
                [getPresetSection("EssentialElements\\typography", __('Body', 'breakdance'), 'body', ['type' => 'popout']),
                    c(
                        "headings",
                        __("Headings", 'breakdance'),
                        [
                            getPresetSection("EssentialElements\\typography", __('All Headings', 'breakdance'), 'all_headings', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H1', 'breakdance'), 'h1', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H2', 'breakdance'), 'h2', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H3', 'breakdance'), 'h3', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H4', 'breakdance'), 'h4', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H5', 'breakdance'), 'h5', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", __('H6', 'breakdance'), 'h6', ['type' => 'popout']),
                        ],
                        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
                        false,
                        false,
                        [],
                    ),
                    controlSection(
                        'links',
                        __('Links', 'breakdance'),
                        [
                            responsiveControlWithHover("color", __("Color", 'breakdance'), ['type' => "color"]),
                            responsiveControl('font_weight', __('Font Weight', 'breakdance'), [
                                'type' => 'dropdown',
                                'items' =>
                                [
                                    array('text' => '100', 'value' => '100'),
                                    array('text' => '200', 'value' => '200'),
                                    array('text' => '300', 'value' => '300'),
                                    array('text' => '400', 'value' => '400'),
                                    array('text' => '500', 'value' => '500'),
                                    array('text' => '600', 'value' => '600'),
                                    array('text' => '700', 'value' => '700'),
                                    array('text' => '800', 'value' => '800'),
                                    array('text' => '900', 'value' => '900'),
                                ],
                            ]),
                            controlSection('decoration', __('Decoration', 'breakdance'), [
                                responsiveControlWithHover(
                                    "style",
                                    __("Style", 'breakdance'),
                                    [
                                        'type' => "dropdown",
                                        'items' => [
                                            ['text' => 'none', 'value' => 'none'],
                                            ['text' => 'solid', 'value' => 'solid'],
                                            ['text' => 'double', 'value' => 'double'],
                                            ['text' => 'dotted', 'value' => 'dotted'],
                                            ['text' => 'dashed', 'value' => 'dashed'],
                                            ['text' => 'wavy', 'value' => 'wavy'],
                                        ],
                                    ]
                                ),
                                responsiveControlWithHover("color", __("Color", 'breakdance'), [
                                    'type' => 'color',
                                ]),
                                responsiveControlWithHover("line", __("Line", 'breakdance'), [
                                    'type' => "dropdown",
                                    'items' => [
                                        ['text' => 'underline', 'value' => 'underline'],
                                        ['text' => 'none', 'value' => 'none'],
                                        ['text' => 'overline', 'value' => 'overline'],
                                        ['text' => 'line-through', 'value' => 'line-through'],
                                    ],
                                ]),
                                responsiveControlWithHover("thickness", __("Thickness", 'breakdance'), [
                                    'type' => "unit",
                                ]),
                            ], null, 'popout'),

                        ],
                        null,
                        'popout'
                    ), ],
                ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
                false,
                false,
                [],
            ), TYPOGRAPHY_PRESETS_SECTION()],
        ['type' => 'section'],
        false,
        false,
        [],
    );
}

/**
 * @return string
 */
function TYPOGRAPHY_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/typography.css.twig') . "\n" . GLOBAL_TYPOGRAPHY_TEMPLATE();
}

/**
 * @return string
 */
function GLOBAL_TYPOGRAPHY_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-typography.css.twig');
}

/**
 * @return string
 */
function typographyPathToPresets()
{
    return 'settings.typography.global_typography.typography_presets';
}

/**
 * @return string[]
 */
function typographyPropertyPathsToWhitelistInFlatProps()
{
    return [typographyPathToPresets() . "[].preset.id"];
}
