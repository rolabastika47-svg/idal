<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
    "EssentialElements\\grid",
    controlSection('grid', __('Grid', 'breakdance'), [
        responsiveControl(
            "enable",
            __("Enable Grid", 'breakdance'),
            [
                'type' => 'button_bar',
                'layout' => 'inline',
                'items' => [
                    '0' => [
                        'text' => __('True', 'breakdance'),
                        'label' => __('Label', 'breakdance'),
                        'value' => 'true',
                        'icon' => 'CheckSquareIcon'
                    ]
                ]
            ],
        ),
        controlSection(
            'columns',
            __('Columns', 'breakdance'),
            [
                responsiveControl(
                    "template",
                    __("Template", 'breakdance'),
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                            'text' => __('Auto', 'breakdance'),
                            'label' => __('Label', 'breakdance'),
                            'value' => 'auto'
                            ],
                            '1' => [
                            'text' => __('Custom', 'breakdance'),
                            'value' => 'custom'
                            ]
                        ]
                    ],
                ),
                responsiveControl(
                    "auto_fit",
                    __("Auto-Fit", 'breakdance'),
                    [
                        'type' => 'button_bar',
                        'layout' => 'inline',
                        'items' => [
                            '0' => [
                                'text' => __('True', 'breakdance'),
                                'label' => __('Label', 'breakdance'),
                                'value' => 'true',
                                'icon' => 'CheckSquareIcon'
                            ]
                        ],
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "columns",
                    __("Columns", 'breakdance'),
                    [
                        'type' => 'number',
                        'layout' => 'inline',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "min_width",
                    __("Min-Width", 'breakdance'),
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "max_width",
                    __("Max-Width", 'breakdance'),
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "columns_template",
                    __("Column Template", 'breakdance'),
                    [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "gap",
                    __("Gap", 'breakdance'),
                    [
                        'type' => 'unit',
                        'layout' => 'inline'
                    ],
                ),
                responsiveControl(
                    "horizontal_alignment",
                    __("Horizontal Alignment", 'breakdance'),
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                                'text' => __('Start', 'breakdance'),
                                'label' => __('Start', 'breakdance'),
                                'value' => 'start',
                                'icon' => 'AlignLeftIcon'
                            ],
                            '1' => [
                                'text' => __('Center', 'breakdance'),
                                'label' => __('Center', 'breakdance'),
                                'value' => 'center',
                                'icon' => 'MinimizeIcon'
                            ],
                            '2' => [
                                'text' => __('End', 'breakdance'),
                                'label' => __('End', 'breakdance'),
                                'value' => 'end',
                                'icon' => 'AlignRightIcon'
                            ],
                            '3' => [
                                'text' => __('Stretch', 'breakdance'),
                                'label' => __('Stretch', 'breakdance'),
                                'value' => 'stretch',
                                'icon' => 'LeftAndRightArrowsIcon'
                            ]
                        ]
                    ],
                ),
            ],
        ),
        controlSection(
            'rows',
            __('Rows', 'breakdance'),
            [
                responsiveControl(
                    "template",
                    __("Template", 'breakdance'),
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                            'text' => __('Auto', 'breakdance'),
                            'label' => __('Label', 'breakdance'),
                            'value' => 'auto'
                            ],
                            '1' => [
                            'text' => __('Custom', 'breakdance'),
                            'value' => 'custom'
                            ]
                        ]
                    ],
                ),
                responsiveControl(
                    "rows",
                    __("Rows", 'breakdance'),
                    [
                        'type' => 'number',
                        'layout' => 'inline',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "min_height",
                    __("Min-Height", 'breakdance'),
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "max_height",
                    __("Max-Height", 'breakdance'),
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "gap",
                    __("Gap", 'breakdance'),
                    [
                        'type' => 'unit',
                        'layout' => 'inline'
                    ],
                ),
                responsiveControl(
                    "vertical_alignment",
                    __("Vertical Alignment", 'breakdance'),
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                                'text' => __('Start', 'breakdance'),
                                'label' => __('Start', 'breakdance'),
                                'value' => 'start',
                                'icon' => 'FlexAlignTopIcon'
                            ],
                            '1' => [
                                'text' => __('Center', 'breakdance'),
                                'label' => __('Center', 'breakdance'),
                                'value' => 'center',
                                'icon' => 'MinimizeIcon'
                            ],
                            '2' => [
                                'text' => __('End', 'breakdance'),
                                'label' => __('End', 'breakdance'),
                                'value' => 'end',
                                'icon' => 'FlexAlignBottomIcon'
                            ],
                            '3' => [
                                'text' => __('Stretch', 'breakdance'),
                                'label' => __('Stretch', 'breakdance'),
                                'value' => 'stretch',
                                'icon' => 'UpAndDownArrowsIcon'
                            ]
                        ]
                    ],
                ),
            ]
        )
    ]),
    true
    );
});
