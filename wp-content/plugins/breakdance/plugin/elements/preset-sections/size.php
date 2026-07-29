<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\size",
        controlSection('size', __('Size', 'breakdance'), [
            responsiveControl("width", __("Width", 'breakdance'), ['type' => "unit"]),
            responsiveControl("min_width", __("Min Width", 'breakdance'), ['type' => "unit"]),
            responsiveControl("max_width", __("Max Width", 'breakdance'), ['type' => "unit"]),
            responsiveControl("height", __("Height", 'breakdance'), ['type' => "unit"]),
            responsiveControl("min_height", __("Min Height", 'breakdance'), ['type' => "unit"]),
            responsiveControl("max_height", __("Max Height", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );
});
