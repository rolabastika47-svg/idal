<?php

namespace Breakdance\WooCommerce\Widgets;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use Breakdance\WPWidgets\WidgetsController;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\WPWidgets\getWidgetControlSections;
use function Breakdance\WPWidgets\getWidgetsAsDropdownItems;

require_once __DIR__ . "/woo_filters_attribute_filter_get_attribute_array.php";

add_action(
    'init',
    function() {
        \Breakdance\WPWidgets\register(
            [
                'slug' => 'active_filters',
                'name' => __('Active Filters', 'breakdance'),
                'className' => 'WC_Widget_Layered_Nav_Filters',
                'category' => "woo_filter",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    )
                ]
            ]
        );

        \Breakdance\WPWidgets\register(
            [
                'slug' => 'price_filter',
                'name' => __('Price Filter', 'breakdance'),
                'className' => 'WC_Widget_Price_Filter',
                'category' => "woo_filter",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    )
                ]
            ]
        );


        \Breakdance\WPWidgets\register(
            [
                'slug' => 'rating_filter',
                'name' => __('Rating Filter', 'breakdance'),
                'className' => 'WC_Widget_Rating_Filter',
                'category' => "woo_filter",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    )
                ]
            ]
        );

        \Breakdance\WPWidgets\register(
            [
                'slug' => 'attribute_filter',
                'name' => __('Filter By Attribute', 'breakdance'),
                'className' => 'WC_Widget_Layered_Nav',
                'category' => "woo_filter",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    ),
                    control(
                        'attribute',
                        __('Attribute', 'breakdance'),
                        [
                            'type' => 'dropdown',
                            'items' => attsWrapperFunc(),
                        ]
                    ),
                    control(
                        'display_type',
                        __('Display Type', 'breakdance'),
                        ['type' => 'dropdown', 'items' => [
                            ['text' => __('list', 'breakdance'), 'value' => 'list'],
                            ['text' => __('dropdown', 'breakdance'), 'value' => 'dropdown'],
                        ]]
                    ),
                    control(
                        'query_type',
                        __('Query Type', 'breakdance'),
                        ['type' => 'dropdown', 'items' => [
                            ['text' => __('and', 'breakdance'), 'value' => 'and'],
                            ['text' => __('or', 'breakdance'), 'value' => 'or'],
                        ]]
                    )
                ]
            ]
        );

        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("woo_filter");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\shop_filters",
            controlSection(
                'shop_filters',
                __('Shop Filters', 'breakdance'),
                [
                    repeaterControl('widgets', __('Widgets', 'breakdance'),
                        array_merge(
                            [
                                control(
                                    'widget',
                                    __('Widget', 'breakdance'),
                                    ['type' => 'dropdown', 'items' => getWidgetsAsDropdownItems($widgets), 'layout' => 'vertical'],
                                )
                            ],
                            getWidgetControlSections($widgets),
                        ),
                        [
                            'repeaterOptions' => [
                                'titleTemplate' => '{widget}', 'defaultTitle' => __('Filter', 'breakdance'), 'buttonName' => __('Add Filter', 'breakdance')
                            ]
                        ]
                    )
                ]
            ),
            true
        );
    },
    1000 /* needed because WidgetsController::getInstance()->widgets is also populated
    on init, so we need to run this after it's already populated */
);


/**
 * @psalm-suppress MixedInferredReturnType
 * @return array
 */
function attsWrapperFunc() {
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress UndefinedFunction
     */
    return attribute_filter_get_attribute_array();
}
