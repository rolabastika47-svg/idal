<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

function registerBordersSections() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\borders",
        controlSection(
            'borders',
            __('Borders', 'breakdance'),
            [
                responsiveControl("radius", __("Radius", 'breakdance'), ['type' => "border_radius", "layout" => "vertical"]),
                responsiveControl("border", __("Styling", 'breakdance'), ['type' => "border_complex", "layout" => "vertical"]),
                responsiveControl("shadow", __("Shadow", 'breakdance'), ['type' => "shadow", "layout" => "vertical"]),

            ],
        ),
        true
    );
}

add_action('init', 'Breakdance\Elements\PresetSections\registerBordersSections');
