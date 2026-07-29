<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\dumbfuck_column_layout",
        controlSection('dumbfuck_column_layout', __('Layout', 'breakdance'), [
            responsiveControl("alignment", __("Alignment", 'breakdance'), [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => __('Left', 'breakdance'), 'value' => 'flex-start'),
                        array('text' => __('Center', 'breakdance'), 'value' => 'center'),
                        array('text' => __('Right', 'breakdance'), 'value' => 'flex-end'),
                    ],
            ]),
            responsiveControl("vertical_alignment", __("Vertical Alignment", 'breakdance'), [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => __('top', 'breakdance'), 'value' => 'flex-start'),
                        array('text' => __('center', 'breakdance'), 'value' => 'center'),
                        array('text' => __('bottom', 'breakdance'), 'value' => 'flex-end'),
                    ],
            ]),
        ]),
        true
    );
});
