<?php

namespace Breakdance\Animations;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_universal_controls', 'Breakdance\Animations\addControls', 69);

/**
 * @param Control[] $controls
 * @return Control[]
 */
function addControls($controls)
{
    /** @psalm-suppress MixedAssignment */
    $controls['settingsSections'][] = controlSection(
        'animations',
        'Animations',
        [
            \Breakdance\Animations\Scrolling\controls(),
            \Breakdance\Animations\Entrance\controls(),
            \Breakdance\Animations\Sticky\controls(),
        ],
        ['isExternal' => true]
    );

    /** @var Control[] $controls */
    return $controls;
}
