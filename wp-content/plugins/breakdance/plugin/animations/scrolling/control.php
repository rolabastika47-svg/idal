<?php

namespace Breakdance\Animations\Scrolling;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control
 */
function controls()
{
    $sectionType = BREAKDANCE_MODE === 'oxygen' ? 'accordion' : 'popout';

    /** @var Control */
    return controlSection(
        'scrolling_animation',
        __('Scrolling Animation', 'breakdance'),
        [
            control('enabled', __('Enabled', 'breakdance'), [
                'type' => 'toggle'
            ]),
            controlSection(
                'x',
                __('Horizontal Position', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'y',
                __('Vertical Position', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'opacity',
                __('Opacity', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'blur',
                __('Blur', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'rotation',
                __('Rotation', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'scale',
                __('Scale', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection('color',
                __('Color', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'background_color',
                __('Background Color', 'breakdance'),
                [
                    control('start', __('Start', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('middle', __('Middle', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('end', __('End', 'breakdance'), [
                        'type' => 'color'
                    ]),
                    control('trigger', __('Trigger', 'breakdance'), [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => [__('Bottom', 'breakdance'), __('Top', 'breakdance')]
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'advanced',
                __('Advanced', 'breakdance'),
                [
                    control('ease', __('Ease', 'breakdance'), [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'items' => array_map(function ($easing) {
                            return [
                                'text' => ucwords(str_replace(
                                    '.',
                                    ' ',
                                    $easing
                                )),
                                'value' => $easing,
                            ];
                        }, EASING_TYPES),
                    ]),
                    control('scrub', __('Scrub', 'breakdance'), [
                        'type' => 'unit',
                        'layout' => 'inline',
                        'unitOptions' => ['types' => ['ms', 's'], 'defaultType' => 'ms'],
                        'rangeOptions' => ['min' => 100, 'max' => 2000, 'step' => 100]
                    ]),
                    control('origin', __('Transform Origin', 'breakdance'), [
                        'type' => 'focus_point',
                        'layout' => 'vertical',
                        'focusPointOptions' => [
                            'gridMode' => true
                        ]
                    ]),
                    control('relative_to', __('Relative To', 'breakdance'), [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            ['text' => __('Viewport', 'breakdance'), 'value' => 'viewport'],
                            ['text' => __('Page', 'breakdance'), 'value' => 'page'],
                            ['text' => __('Custom', 'breakdance'), 'value' => 'custom'],
                        ],
                    ]),
                    control('relative_selector', __('Custom Selector', 'breakdance'), [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'condition' => ['path' => '%%CURRENTPATH%%.relative_to', 'operand' => 'equals', 'value' => 'custom']
                    ]),
                    control('disable_at', __('Disable At', 'breakdance'), [
                        'type' => 'breakpoint_dropdown',
                        'layout' => 'vertical',
                    ]),
                    control('debug', __('Debug', 'breakdance'), [
                        'type' => 'toggle',
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            )
        ],
        ['isExternal' => true],
        $sectionType
    );
}
