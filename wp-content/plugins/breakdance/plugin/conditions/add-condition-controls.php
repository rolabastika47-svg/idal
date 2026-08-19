<?php

namespace Breakdance\Conditions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_universal_controls', '\Breakdance\Conditions\addConditionControls', 69);

/**
 *
 * @param array $controls
 * @return array
 */
function addConditionControls($controls)
{

    $conditions = [
        control(
            'conditions',
            __('Only Show Element If', 'breakdance'),
            [
                'type' => 'condition',
                'layout' => 'vertical'
            ]
        ),
        control(
            'builder_preview',
            __('In-Builder Preview', 'breakdance'),
            [
                'type' => 'dropdown', 'items' => [
                    ['text' => __('Always Show', 'breakdance'), 'value' => 'show'],
                    ['text' => __('Always Hide', 'breakdance'), 'value' => 'hide'],
                ],
                'layout' => 'vertical'
            ]
        ),
    ];

    if (BREAKDANCE_MODE === 'oxygen') {
        array_unshift($conditions, control(
            'visible',
            __('Visible', 'breakdance'),
            [
                'type' => 'toggle',
                'layout' => 'inline',
                'toggleOptions' => [
                    'defaultValue' => true
                ]
            ]
        ));
    }

    /**
     * @psalm-suppress MixedArrayAssignment
     */
    $controls['settingsSections'][] = controlSection('conditions', __('Conditions', 'breakdance'), $conditions);

    return $controls;
}
