<?php

namespace Breakdance\BreakdanceOxygen;

/**
 * @return void
 */
function addDefaultHiddenElements()
{
    if (BREAKDANCE_MODE !== 'oxygen') return;

    /** @var mixed $current */
    $current = \Breakdance\Data\get_global_option('builder_hidden_elements');

    if (is_array($current) && count($current) > 0) {
        return;
    }

    $excludedElements = [
        'EssentialElements\Section', // Container element can be used instead.
        'EssentialElements\Div', // The Container element is already a div and much simpler to use.
        'EssentialElements\Heading', // Text element can be used instead
        'EssentialElements\Text', // There is already a Text element in Oxygen
        'EssentialElements\TextLink', // Text Link element can be used instead
        'EssentialElements\WrapperLink', // Container Link element can be used instead
        'EssentialElements\Icon', // SVG Icon element can be used instead
        'EssentialElements\RichText', // Rich Text element can be used instead
        'EssentialElements\CodeBlock', // HTML Code, PHP Code, CSS Code, or JavaScript Code elements can be used instead
        'EssentialElements\Image2', // Image element can be used instead
    ];

    \Breakdance\Data\set_global_option('builder_hidden_elements', $excludedElements);
}
