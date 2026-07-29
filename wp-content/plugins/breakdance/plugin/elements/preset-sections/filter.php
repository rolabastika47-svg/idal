<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\c;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

add_action('init', function () {
    $filterSection = controlSection("filter", __("Filter", 'breakdance'), [
            c('filters', __('Filter Items', 'breakdance'), [
                control("type", __("Type", 'breakdance'), [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'items' => [
                        '0' => ['value' => 'blur', 'text' => 'blur'],
                        '1' => ['text' => 'brightness', 'value' => 'brightness'],
                        '2' => ['text' => 'contrast', 'value' => 'contrast'],
                        '3' => ['text' => 'grayscale', 'value' => 'grayscale'],
                        '4' => ['text' => 'hue-rotate', 'value' => 'hue-rotate'],
                        '5' => ['text' => 'invert', 'value' => 'invert'],
                        '6' => ['text' => 'saturate', 'value' => 'saturate'],
                        '7' => ['text' => 'sepia', 'value' => 'sepia']
                    ]],
                    false,
                    [],
                ),
                control("blur_amount", __("Blur Amount", 'breakdance'), [
                    'type' => 'unit',
                    'layout' => 'inline',
                    'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'],
                    'rangeOptions' => ['min' => 1, 'max' => 100, 'step' => 1],
                    'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'blur']],
                    false,
                    [],
                ),
                control("amount", __("Amount", 'breakdance'), [
                    'type' => 'unit',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.type',
                        'operand' => 'is one of',
                        'value' => [
                            '0' => 'brightness',
                            '1' => 'contrast',
                            '2' => 'grayscale',
                            '3' => 'invert',
                            '4' => 'saturate',
                            '5' => 'sepia'
                        ]
                    ],
                    'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%'],
                    'rangeOptions' => ['min' => 0, 'max' => 200, 'step' => 1]],
                    false,
                    [],
                ),
                control("rotate", __("Rotate", 'breakdance'), [
                    'type' => 'unit',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.type',
                        'operand' => 'equals',
                        'value' => 'hue-rotate'
                    ],
                    'rangeOptions' => ['min' => 1, 'max' => 360, 'step' => 1],
                    'unitOptions' => [
                        'types' => ['0' => 'deg', '1' => 'custom'],
                        'defaultType' => 'deg'
                    ]],
                    false,
                    [],
                )],
            ['type' => 'repeater','layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{type}', 'defaultTitle' => __('Filter', 'breakdance'), 'buttonName' => __('Add Filter', 'breakdance')]],
            false,
            true
        )]
    );
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\filter",
        $filterSection,
        true
    );
});
