<?php

namespace Breakdance\CustomCSS;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

/**
 * @return Control
 * TODO this needs to be a preset in itself so that we can re-use it
 */
function getResponsiveCssControl()
{
    return responsiveControl(
        'css',
        __('Custom CSS', 'breakdance'),
        [
            'placeholder' => "%%SELECTOR%% {\n  background-color: red; \n}",
            'codeOptions' => [
                'language' => 'css',
                'autofillOnEmpty' => "%%SELECTOR%% {
  PLACECURSORHERE
}",
                'tags' => ['selector']
            ],
            'type' => "code",
            'layout' => 'vertical'
        ]
    );
}

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\custom_css",
        controlSection('custom_css', __('Custom CSS', 'breakdance'), [
            getResponsiveCssControl(),
        ]),
        true
    );
});
