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
 * @param \Breakdance\Elements\Element $element
 * @return Control|null
 */
function getTagHtmlControl($element)
{

    if (!$element::tagControlPath() && count($element::tagOptions())) {
        $dropdownItemsOfTagOptions = array_map(
            /**
             * @param string $tag
             * @return array{text: string, value: string}
             */
            function ($tag) {
                return [
                    'text' => $tag,
                    'value' => $tag,
                ];
            },
            $element::tagOptions()
        );

        return control(
            'tag',
            __('Tag', 'breakdance'),
            ['type' => 'dropdown', 'items' => $dropdownItemsOfTagOptions]
        );
    }

    return null;
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
