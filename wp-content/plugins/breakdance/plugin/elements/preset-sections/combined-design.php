<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\controlSection;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\combined_design",
        controlSection(
            'combined_design',
            __('Design', 'breakdance'),
            [
                getPresetSection("EssentialElements\\typography", __('Typography', 'breakdance'), 'typography', ['type' => 'popout']),
                getPresetSection("EssentialElements\\background", __('Background', 'breakdance'), 'background', ['type' => 'popout']),
                getPresetSection("EssentialElements\\spacing_padding_all", __('Spacing', 'breakdance'), 'spacing', ['type' => 'popout']),
                getPresetSection("EssentialElements\\borders", __('Borders', 'breakdance'), 'borders', ['type' => 'popout']),
            ]
        ),
        true
    );
});
