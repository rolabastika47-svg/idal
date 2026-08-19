<?php

namespace Breakdance\FutureLayer;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_universal_controls', 'Breakdance\FutureLayer\addControls', 70);

/**
 * @param Control[] $controls
 * @return Control[]
 */
function addControls($controls)
{
    if (!shouldShowFutureLayerControls()) {
        return $controls;
    }

    $controls['settingsSections'][] = controlSection(
        'futurelayer',
        'FutureLayer',
        [
            control(
                'more_descriptive_type',
                'More Descriptive Type',
                [
                    'type' => 'text',
                    'layout' => 'vertical',
                ]
            ),
            control(
                'allow_removal',
                'Allow Removal',
                [
                    'type' => 'toggle',
                ]
            ),
        ],
        ['isExternal' => true]
    );

    /** @var Control[] $controls */
    return $controls;
}

/**
 * @return bool
 */
function shouldShowFutureLayerControls()
{
    $whitelistedDomains = getWhitelistedDomains();
    /** @var string */
    $currentDomain = $_SERVER['HTTP_HOST'] ?? '';

    return in_array($currentDomain, $whitelistedDomains, true);
}

/**
 * @return string[]
 */
function getWhitelistedDomains()
{
    return [
        'breakdance3.local',
        'breakdance.local',
        'breakdancelibrary.com'
    ];
}
