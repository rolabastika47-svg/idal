<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_shape_dividers',
        'Breakdance\Elements\PresetSections\get_divider_shapes',
        'edit',
        true
    );
});

add_action('init', function () {
    $shapeInputDropdown = controlSection('shape_dividers_section', __('Shape Dividers', 'breakdance'), [
        repeaterControl('dividers', __('Shape Dividers', 'breakdance'), [
            control(
                'shape',
                __('Shape', 'breakdance'),
                [
                    'type' => 'dropdown',
                    'dropdownOptions' => [
                        'populate' => [
                            'fetchDataAction' => 'breakdance_get_shape_dividers',
                        ],
                    ],
                ]
            ),
            control(
                'custom_shape',
                __('Custom Shape', 'breakdance'),
                [
                    'type' => 'text',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.shape',
                        'operand' => 'equals',
                        'value' => 'custom',
                    ],
                    'textOptions' => ['multiline' => true]
                ]
            ),
            control(
                'color',
                __('Color', 'breakdance'),
                ['type' => 'color']
            ),
            control(
                'flip_horizontally',
                __('Flip Horizontally', 'breakdance'),
                ['type' => 'button_bar', 'items' =>
                [
                    ['text' => __('Yes', 'breakdance'), 'value' => 'yes', 'icon' => 'LeftAndRightArrowsIcon'],
                ]]
            ),
            control(
                'position',
                __('Position', 'breakdance'),
                ['type' => 'button_bar', 'items' =>
                [
                    ['text' => __('Top', 'breakdance'), 'value' => 'top', 'icon' => 'ArrowUpToLine'],
                    ['text' => __('Bottom', 'breakdance'), 'value' => 'bottom', 'icon' => 'ArrowDownToLine'],
                ]]
            ),
            responsiveControl(
                'width',
                __('Width', 'breakdance'),
                ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%'], 'rangeOptions' => ['min' => 100, 'max' => 500, 'step' => 1]],
            ),
            responsiveControl(
                'height',
                __('Height', 'breakdance'),
                ['type' => 'unit']
            ),
            control(
                'bring_to_front',
                __('Display in front of content', 'breakdance'),
                ['type' => 'toggle']
            ),
        ]),
    ]);
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\Shape",
        $shapeInputDropdown,
        true
    );
});


/**
 * @param string $path
 * @return string[][]
 */
function get_divider_shapes($path = __DIR__)
{

    // Shape Divider variables
    $shape_files = array_diff(scandir($path . '/shape-dividers'), array(".", ".."));

    $svgs = [['text' => __('Custom', 'breakdance'), 'value' => 'custom']];

    foreach ($shape_files as $svg) {

        $file_contents = file_get_contents($path . "/shape-dividers/" . $svg);
        $file_name = explode('.', $svg);
        $file_name = $file_name[0];
        $svg_array = ['text' => $file_name, 'value' => $file_contents];

        $svgs[] = $svg_array;
    }

    /**
     * @var array{text:string,value:string}[]
     */
    $svgs = bdox_run_filters('breakdance_shape_dividers', $svgs);

    return $svgs;
}
