<?php

namespace Breakdance\Elements\UniversalControls;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;


/**
 * @return Control
 */
function getAttributesHtmlControl()
{
    return repeaterControl(
        'attributes',
        __('Attributes', 'breakdance'),
        [
            control('name', __('Name', 'breakdance'),
                [
                    'type' => 'text',
                    'layout' => 'vertical',
                    'textOptions' => ['validationFunctionName' => 'validateHtmlAttributeName']
                ]
            ),
            control('value', __('Value', 'breakdance'),
                [
                    'type' => 'text',
                    'layout' => 'vertical',
                ],
                false,
                [],
                [],
                [
                    'accepts' => 'string'
                ]
            ),
        ],
        [
            'repeaterOptions' => [
                'titleTemplate' => '{name}',
                'defaultTitle' => __('Attribute', 'breakdance'),
                'buttonName' => __('Add attribute', 'breakdance')
            ],
        ]
    );
}


/**
 * @return Control
 */
function getTagHtmlControl()
{
    return control(
        'tag',
        __('Tag', 'breakdance'),
        [
            'type' => 'dropdown',
            'dropdownOptions' => [
                'populate' => [
                    'path' => '$element.htmlTag.options',
                ]
            ],
            'condition' => [
                [
                    [
                        'path' => '$element.htmlTag.options',
                        'operand' => 'is set',
                    ],
                    [
                        'path' => '$element.htmlTag.pathToControl',
                        'operand' => 'equals',
                        'value' => false
                    ]
                ]
            ]
        ]
    );
}

/**
 * @return Control
 */
function getIdHtmlControl()
{
    return control(
        'id',
        __('ID', 'breakdance'),
        [
            'type' => 'text',
            'layout' => 'vertical',
            'placeholder' => __('my-awesome-element', 'breakdance'),
            'textOptions' => [
                'validationFunctionName' => 'validateHtmlId'
            ]
        ]
    );
}
