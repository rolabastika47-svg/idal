<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control
 */
function CONTAINERS_SECTION()
{

    return controlSection('containers', __('Containers', 'breakdance'), [
        c('sections', __('Sections', 'breakdance'), [
            control('container_width', __('Container Width', 'breakdance'), ['type' => 'unit']),
            control('vertical_padding', __('Vertical Padding', 'breakdance'), ['type' => 'unit'], true),
            control('horizontal_padding', __('Horizontal Padding', 'breakdance'), ['type' => 'unit'], true),
        ], ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']], false, false, []),
        control('column_gap', __('Column Gap', 'breakdance'), ['type' => 'unit'], true),
    ]);
}

/**
 * @return string
 */
function CONTAINERS_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/containers.css.twig');
}
