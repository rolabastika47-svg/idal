<?php

namespace Breakdance\GlobalSettings;

/**
 * @return string
 */
function ATOMS_TEMPLATE()
{
    return (string) file_get_contents(__DIR__ . '/../../elements/atom-default-css/atom-default-css.css.twig');
}
