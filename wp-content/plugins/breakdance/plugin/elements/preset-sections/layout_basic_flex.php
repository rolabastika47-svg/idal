<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
    "EssentialElements\\layout_basic_flex",
    c(
        "layout_basic_flex",
        __("Layout (Basic Flex)", 'breakdance'),
        [
            c(
                "stack",
                __("Stack", 'breakdance'),
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'items' => [
                        ['text' => __('Vertical', 'breakdance'), 'label' => __('Label', 'breakdance'), 'value' => 'vertical'],
                        ['text' => __('Horizontal', 'breakdance'), 'value' => 'horizontal']
                    ]
                ],
                true,
                false
            ),
            c(
                "alignment",
                __("Alignment", 'breakdance'),
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'not equals',
                        'value' => 'horizontal'
                    ],
                    'items' => [
                        ['text' => __('Left', 'breakdance'), 'label' => __('Label', 'breakdance'), 'value' => 'left'],
                        ['text' => __('Center', 'breakdance'), 'value' => 'center'],
                        ['text' => __('Right', 'breakdance'), 'value' => 'right']
                    ]
                ],
                true,
                false
            ),
            c(
                "h_h_alignment",
                __("H Alignment", 'breakdance'),
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'equals',
                        'value' => 'horizontal'
                    ],
                    'items' => [
                        ['text' => __('Left', 'breakdance'), 'label' => __('Label', 'breakdance'), 'value' => 'left'],
                        ['text' => __('Center', 'breakdance'), 'value' => 'center'],
                        ['text' => __('Right', 'breakdance'), 'value' => 'right'],
                        ['text' => __('Space Around', 'breakdance'), 'value' => 'space_around'],
                        ['text' => __('Space Between', 'breakdance'), 'value' => 'space_between']
                    ]
                ],
                true,
                false
            ),
            c(
                "h_v_alignment",
                __("V Alignment", 'breakdance'),
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'items' => [
                        ['text' => __('Top', 'breakdance'), 'label' => __('Label', 'breakdance'), 'value' => 'top'],
                        ['text' => __('Middle', 'breakdance'), 'value' => 'middle'],
                        ['text' => __('Bottom', 'breakdance'), 'value' => 'bottom']
                    ],
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'equals',
                        'value' => 'horizontal']
                    ],
                true,
                false
            )
        ],
        [
            'type' => 'section',
        ],
        false,
        false
    ),
    true
    );
});
