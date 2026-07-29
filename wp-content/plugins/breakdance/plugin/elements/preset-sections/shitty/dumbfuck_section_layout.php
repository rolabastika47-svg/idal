<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\dumbfuck_section_layout",
        controlSection('dumbfuck_section_layout', __('Layout', 'breakdance'), [
            responsiveControl("stack_content", __("Stack Content", 'breakdance'), [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => __('Vertical', 'breakdance'), 'value' => 'vertical'),
                        array('text' => __('Horizontal', 'breakdance'), 'value' => 'horizontal'),
                    ],
            ]),
            responsiveControl("alignment_for_horizontal_layout", __("Alignment", 'breakdance'), [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => __('Space Around', 'breakdance'), 'value' => 'space-around'),
                        array('text' => __('Space Between', 'breakdance'), 'value' => 'space-between'),
                    ],
                'condition' => [
                    'path' => 'design.dumbfuck_section_layout.stack_content', // TODO
                    'operand' => 'equals',
                    'value' => 'horizontal',
                ],
            ]),
            responsiveControl("alignment_for_vertical_layout", __("Alignment", 'breakdance'), [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => __('Left', 'breakdance'), 'value' => 'flex-start'),
                        array('text' => __('Center', 'breakdance'), 'value' => 'center'),
                        array('text' => __('Right', 'breakdance'), 'value' => 'flex-end'),
                    ],
                'condition' => [
                    'path' => 'design.dumbfuck_section_layout.stack_content', // TODO
                    'operand' => 'equals',
                    'value' => 'vertical', /* this control should also be visible if no choice was made */
                ],
            ]),
        ]),
    );
});
