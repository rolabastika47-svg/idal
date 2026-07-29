<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

function registerBordersWithoutShadowsSections() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\borders_without_shadows",
        controlSection(
            'borders_without_shadows',
            __('Borders Without Shadows', 'breakdance'),
            [
                responsiveControl("radius", __("Radius", 'breakdance'), ['type' => "border_radius", "layout" => "vertical"]),
                responsiveControl("border", __("Styling", 'breakdance'), ['type' => "border_complex", "layout" => "vertical"]),
            ],
        ),
        true
    );
}

add_action('init', 'Breakdance\Elements\PresetSections\registerBordersWithoutShadowsSections');
