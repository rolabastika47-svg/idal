<?php

namespace Breakdance\Animations\Sticky;

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
        'sticky',
        __('Sticky', 'breakdance'),
        [
            control('position', __('Position', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'inline',
                'items' => [
                    ['text' => __('Top', 'breakdance'), 'value' => 'top'],
                    ['text' => __('Center', 'breakdance'), 'value' => 'center'],
                    ['text' => __('Bottom', 'breakdance'), 'value' => 'bottom'],
                ],
            ]),
            control('offset', __('Offset', 'breakdance'), [
                'type' => 'unit',
                'layout' => 'inline',
                'multiple' => true,
                'unitOptions' => ['types' => ['px']],
                'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1],
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ], true),
            control('relative_to', __('Relative To', 'breakdance'), [
                'type' => 'button_bar',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Parent', 'breakdance'), 'value' => 'parent'],
                    ['text' => __('Viewport', 'breakdance'), 'value' => 'viewport'],
                    ['text' => __('Custom', 'breakdance'), 'value' => 'custom'],
                ],
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ]),
            control('relative_selector', __('Custom Selector', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => ['path' => '%%CURRENTPATH%%.relative_to', 'operand' => 'equals', 'value' => 'custom'],
            ]),
            control('disable_at', __('Disable At', 'breakdance'), [
                'type' => 'breakpoint_dropdown',
                'layout' => 'vertical',
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ]),
        ],
        ['isExternal' => true],
        $sectionType
    );
}
