<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;


/**
 * @param boolean $hoverable
 * @param boolean $enableMediaQueries
 * @return Control
 */
function getTypographyEffectsSection($hoverable, $enableMediaQueries) {

    $strokeWidthControl = control(
        'stroke_width',
        __('Stroke Width', 'breakdance'),
        [
            'type' => 'unit'
        ],
        $enableMediaQueries
    );

    $strokeColorControl = control(
        'stroke_color',
        __('Stroke Color', 'breakdance'),
        [
            'type' => 'color'
        ],
        $enableMediaQueries
    );

    $fillControl = control(
        'fill',
        __('Fill', 'breakdance'),
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => __('Transparent', 'breakdance'), 'value' => 'transparent'],
                ['text' => __('Gradient', 'breakdance'), 'value' => 'gradient'],
                ['text' => __('Image', 'breakdance'), 'value' => 'image'],
            ]
        ],
        $enableMediaQueries
    );

    $fillGradientControl = control(
        'gradient',
        __('Gradient', 'breakdance'),
        [
            'type' => 'color',
            'colorOptions' => ['type' => 'gradientOnly'],
            'layout' => 'vertical',
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'gradient',
            ]
        ],
        $enableMediaQueries
    );

    $fillImageControl = control(
        'image',
        __('Image', 'breakdance'),
        [
            'type' => 'wpmedia',
            'layout' => 'vertical',
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageSizeControl = control(
        'image_size',
        __('Size', 'breakdance'),
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => __('Cover', 'breakdance'), 'value' => 'cover'],
                ['text' => __('Contain', 'breakdance'), 'value' => 'contain'],
                ['text' => __('Auto', 'breakdance'), 'value' => 'auto'],
                ['text' => __('Custom', 'breakdance'), 'value' => 'custom'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomSizeHeightControl = control(
        'image_height',
        __('Height', 'breakdance'),
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_size',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomSizeWidthControl = control(
        'image_width',
        __('Width', 'breakdance'),
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_size',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageRepeatControl = control(
        'image_repeat',
        __('Repeat', 'breakdance'),
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => __("no-repeat", 'breakdance'), 'value' => 'no-repeat'],
                ['text' => __("repeat", 'breakdance'), 'value' => 'repeat'],
                ['text' => __("repeat-x", 'breakdance'), 'value' => 'repeat-x'],
                ['text' => __("repeat-y", 'breakdance'), 'value' => 'repeat-y'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImagePositionControl = control(
        'image_position',
        __('Position', 'breakdance'),
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => __('Left Top', 'breakdance'), 'value' => 'left top'],
                ['text' => __('Left Center', 'breakdance'), 'value' => 'left center'],
                ['text' => __('Left Bottom', 'breakdance'), 'value' => 'left bottom'],
                ['text' => __('Right Top', 'breakdance'), 'value' => 'right top'],
                ['text' => __('Right Center', 'breakdance'), 'value' => 'right center'],
                ['text' => __('Right Bottom', 'breakdance'), 'value' => 'right bottom'],
                ['text' => __('Center Top', 'breakdance'), 'value' => 'center top'],
                ['text' => __('Center Center', 'breakdance'), 'value' => 'center center'],
                ['text' => __('Center Bottom', 'breakdance'), 'value' => 'center bottom'],
                ['text' => __('Custom', 'breakdance'), 'value' => 'custom'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomPositionTopControl = control(
        'image_top',
        __('Top', 'breakdance'),
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_position',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomPositionLeftControl = control(
        'image_left',
        __('Left', 'breakdance'),
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_position',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $textShadowControl = control(
        'text_shadow',
        __('Text Shadow', 'breakdance'),
        [
            'type' => 'shadow',
            'layout' => 'vertical',
            'shadowOptions' => [
                'type' => 'text'
            ]
        ],
        $enableMediaQueries
    );


    if ($hoverable) {
        $strokeWidthControl['enableHover'] = true;
        $strokeColorControl['enableHover'] = true;
        $fillGradientControl['enableHover'] = true;
        $fillImageControl['enableHover'] = true;
        $textShadowControl['enableHover'] = true;
    }

    return controlSection('effects', __('Effects', 'breakdance'), [
        $strokeWidthControl,
        $strokeColorControl,
        $fillControl,
        $fillGradientControl,
        $fillImageControl,
        $fillImageSizeControl,
        $fillImageCustomSizeHeightControl,
        $fillImageCustomSizeWidthControl,
        $fillImageRepeatControl,
        $fillImagePositionControl,
        $fillImageCustomPositionTopControl,
        $fillImageCustomPositionLeftControl,
        $textShadowControl,
    ],
    null, 'popout');

}

/**
 * @param array{includeColor?:boolean,includeAlign?:boolean,includeEffects?:boolean,hoverableColorAndEffects?:boolean,hoverableEverything?:boolean} $args
 * @return Control
 */
function getTypographySection($args) {

    $args = array_merge(
        [
            'includeColor' => false,
            'includeAlign' => false,
            'includeEffects' => false,
            'hoverableColorAndEffects' => false,
            'hoverableEverything' => false,
            'enableMediaQueries' => true,
        ],
        $args
    );

    $effectsSection = getTypographyEffectsSection($args['hoverableColorAndEffects'] || $args['hoverableEverything'], $args['enableMediaQueries']);

    $typographySection = controlSection(
        'typography',
        __('Typography', 'breakdance'),
        []
    );

    // COLOR
    if ($args['includeColor']) {
        $colorControl = control(
            "color",
            __("Color", 'breakdance'),
            [
                'type' => "color",
                "layout" => "inline",
                'colorOptions' => ['type' => 'solidOnly']
            ],
            $args['enableMediaQueries']
        );

        if ($args['hoverableColorAndEffects'] || $args['hoverableEverything']) {
            $colorControl['enableHover'] = true;
        }

        $typographySection['children'][] = $colorControl;
    }


    // TEXT ALIGN
    if ($args['includeAlign']) {
        $alignControl = control(
            'text_align',
            __('Alignment', 'breakdance'),
            [
                'type' => 'button_bar',
                'layout' => 'inline',
                'items' => [
                    ['text' => __('Left', 'breakdance'), 'value' => 'left', 'icon' => 'AlignLeftIcon'],
                    ['text' => __('Center', 'breakdance'), 'value' => 'center', 'icon' => 'AlignCenterIcon'],
                    ['text' => __('Right', 'breakdance'), 'value' => 'right', 'icon' => 'AlignRightIcon'],
                    ['text' => __('Justify', 'breakdance'), 'value' => 'justify', 'icon' => 'AlignJustifyIcon'],
                ],
            ],
            true
        );

        // alignControl is deliberately not hoverable, even when $args['hoverableEverything'] is true. It makes no sense to have hoverable alignment.
        $typographySection['children'][] = $alignControl;
    }


    // TYPOGRAPHY
    $typographyControl = control(
        'typography',
        __('Typography', 'breakdance'),
        [
            'type' => 'typography',
            'layout' => 'vertical',
            'noLabel' => true,
            'typographyOptions' => [
                'customTypographyOptions' => [
                    'hoverableEverything' => $args['hoverableEverything'],
                    'enableMediaQueries' => $args['enableMediaQueries'],
                ]
            ]
        ]
    );

    $typographySection['children'][] = $typographyControl;


    // EFFECTS
    if ($args['includeEffects']) {
        $typographySection['children'][] = $effectsSection;
    }

    return $typographySection;


}






function registerTypographySections() {

    /*
    i'm pretty sure we don't need all of these
    we'll figure out what we need as we build elements though
    */

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_nothing",
        getTypographySection(
            [
            ]
        ),
        true
    );


    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => false,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    // -----



    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_hoverable_color",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align_with_hoverable_color",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => false,
                'includeAlign' => true,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_with_hoverable_color_and_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align_with_hoverable_color_and_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    // ------

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_without_media_queries",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false,
                'enableMediaQueries' => false
            ]
        ),
        true
    );
}


add_action('init', 'Breakdance\Elements\PresetSections\registerTypographySections');
