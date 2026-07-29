<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\PresetSections\getPresetSection;
use function Breakdance\Elements\repeaterControl;

/**
 * @return Control
 */
function BUTTONS_SECTION()
{
    return controlSection(
        'buttons',
        __('Buttons', 'breakdance'),
        [
            getPresetSection("EssentialElements\\AtomV1CustomButtonDesignNoResponsive", __("Primary", 'breakdance'), "primary", ['type' => 'popout']),
            getPresetSection("EssentialElements\\AtomV1CustomButtonDesignNoResponsive", __("Secondary", 'breakdance'), "secondary", ['type' => 'popout']),
            BUTTON_PRESETS()
        ]
    );
}

/**
 * @return Control
 * @throws \Exception
 */
function BUTTON_PRESETS()
{
    return controlSection(
        'button_presets',
        __('Presets', 'breakdance'),
        [
            repeaterControl(
                'button_presets',
                __('Button Presets', 'breakdance'),
                [
                    control(
                        'name',
                        __('Preset Name', 'breakdance'),
                        ['type' => 'text', 'layout' => 'vertical']
                    ),
                    getPresetSection("EssentialElements\\AtomV1CustomButtonDesign", __("Styles", 'breakdance'), "styles", ['type' => 'popout'])
                ],
                [
                    'repeaterOptions' => [
                        'noDuplicate' => true,
                        'titleTemplate' => '{name}',
                        'defaultTitle' => __('Preset', 'breakdance'),
                        'buttonName' => __('Add Preset', 'breakdance'),
                        'defaultNewValue' => [
                            'id' => '{uuid}',
                            /* translators: preset number */
                            'name' => sprintf(__('Preset %s', 'breakdance'), '{count}'),
                        ],
                        'deleteConfirm' => true,
                        'deleteText' => __('Are you sure you want to delete this preset?', 'breakdance'),
                    ],
                ]
            ),
        ],
        null,
        'popout'
    );
}

/**
 * @return string
 */
function BUTTONS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-buttons.css.twig');
}
