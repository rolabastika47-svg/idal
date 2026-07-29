<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\form",
        controlSection('form', __('Form', 'breakdance'), [
            control('theme', __('Theme', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Default', 'breakdance'), 'value' => 'default'],
                    ['text' => __('Material Design', 'breakdance'), 'value' => 'material'],
                    ['text' => __('Material Design Outlined', 'breakdance'), 'value' => 'material-outlined'],
                ],
            ]),
            control('primary_color', __('Primary Color', 'breakdance'), [
                'type' => 'color'
            ]),
            control('secondary_color', __('Secondary Color', 'breakdance'), [
                'type' => 'color'
            ]),
            control('text_color', __('Text Color', 'breakdance'), [
                'type' => 'color'
            ]),
            control('border_color', __('Border Color', 'breakdance'), [
                'type' => 'color'
            ]),
        ]),
        false
    );
});
