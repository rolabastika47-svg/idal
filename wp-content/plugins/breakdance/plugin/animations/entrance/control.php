<?php

namespace Breakdance\Animations\Entrance;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

// https://github.com/michalsnik/aos

/**
 * @return Control
 */
function controls()
{
    $sectionType = BREAKDANCE_MODE === 'oxygen' ? 'accordion' : 'popout';

    /** @var Control */
    return controlSection(
        'entrance_animation',
        __('Entrance Animation', 'breakdance'),
        [
            control('animation_type', __('Animation Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => ANIMATION_TYPES,
            ]),
            control('duration', __('Duration', 'breakdance'), [
                'type' => 'unit',
                'unitOptions' => [
                    'types' => ['ms', 's']
                ],
                'rangeOptions' => [
                    'min' => 0,
                    'max' => 3000,
                    'step' => 50,
                ],
                'condition' => ['path' => '%%CURRENTPATH%%.animation_type', 'operand' => 'is set']
            ]),
            control('delay', __('Delay', 'breakdance'), [
                'type' => 'unit',
                'unitOptions' => [
                    'types' => ['ms', 's']
                ],
                'rangeOptions' => [
                    'min' => 0,
                    'max' => 3000,
                    'step' => 50,
                ],
                'condition' => ['path' => '%%CURRENTPATH%%.animation_type', 'operand' => 'is set']
            ]),
            controlSection(
                'advanced',
                __('Advanced', 'breakdance'),
                [
                    control('distance', __('Distance', 'breakdance'), [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 300,
                            'step' => 1,
                        ],
                        'condition' => [
                            'path' => 'settings.animations.entrance_animation.animation_type',
                            'operand' => 'is one of',
                            'value' => [
                                'slideUp', 'slideDown', 'slideLeft', 'slideRight',
                                'flipUp', 'flipDown', 'flipLeft', 'flipRight',
                            ]
                        ]
                    ]),
                    control('offset', __('Offset', 'breakdance'), [
                        'type' => 'unit',
                        'unitOptions' => [
                            'types' => ['px']
                        ],
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ]),
                    control('once', __('Animate only once', 'breakdance'), [
                        'type' => 'toggle'
                    ]),
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
//                    control('anchorPlacement', 'Anchor Placement', [
//                        'type' => 'dropdown',
//                        'layout' => 'vertical',
//                        'items' => array_map(function ($anchor_placement) {
//                            return [
//                                'text' => ucwords(str_replace(
//                                    '-',
//                                    ' ',
//                                    $anchor_placement
//                                )),
//                                'value' => $anchor_placement,
//                            ];
//                        }, ANCHOR_PLACEMENTS),
//                    ]),
                    control('disable_at', __('Disable At', 'breakdance'), [
                        'type' => 'breakpoint_dropdown',
                        'layout' => 'vertical',
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.animation_type', 'operand' => 'is set']],
                'popout'
            ),
        ],
        ['isExternal' => true],
        $sectionType
    );
}
