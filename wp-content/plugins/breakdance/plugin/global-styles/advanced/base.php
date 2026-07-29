<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

/**
 * @return Control
 */
function ADVANCED_SECTION()
{
    $cssSection = repeaterControl("stylesheets", __("CSS", 'breakdance'), [
        control('code', __('CSS Code', 'breakdance'), ['codeOptions' => ['language' => 'css'], 'type' => 'code', 'layout' => 'vertical']),
    ], ['repeaterOptions' => [
        'titleTemplate' => '{name}',
        'defaultTitle' => __('Stylesheet', 'breakdance'),
        'buttonName' => __('Add Stylesheet', 'breakdance'),
    ]]);

    $jsSection = repeaterControl("scripts", __("Scripts", 'breakdance'), [
        control('code', __('JavaScript Code', 'breakdance'), ['codeOptions' => ['language' => 'javascript'], 'type' => 'code', 'layout' => 'vertical']),
    ], ['repeaterOptions' => [
        'titleTemplate' => '{name}',
        'defaultTitle' => __('Javascript', 'breakdance'),
        'buttonName' => __('Add Javascript', 'breakdance'),
    ]]);

    $advanced = controlSection('code', __('Code', 'breakdance'), [
        $cssSection,
        $jsSection,
    ]);

    return $advanced;
}

/**
 * @return string
 */
function ADVANCED_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/advanced.css.twig');
}
