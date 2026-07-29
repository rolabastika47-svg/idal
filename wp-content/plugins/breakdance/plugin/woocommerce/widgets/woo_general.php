<?php

namespace Breakdance\WooCommerce\Widgets;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use Breakdance\WPWidgets\WidgetsController;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\WPWidgets\getWidgetControlSections;
use function Breakdance\WPWidgets\getWidgetsAsDropdownItems;

add_action(
    'init',
    function() {
        \Breakdance\WPWidgets\register(
            [
                'slug' => 'wc_products_by_rating_list',
                'name' => __('Products By Rating List', 'breakdance'),
                'className' => 'WC_Widget_Top_Rated_Products',
                'category' => "woo_general",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    ),
                    control(
                        'number',
                        __('Number of products', 'breakdance'),
                        ['type' => 'number']
                    )
                ]
            ]
        );


        \Breakdance\WPWidgets\register([
            'slug' => 'wc_product_search',
            'name' => __('Product Search', 'breakdance'),
            'className' => 'WC_Widget_Product_Search',
            'category' => "woo_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wc_product_categories',
            'name' => __('Product Categories', 'breakdance'),
            'className' => 'WC_Widget_Product_Categories',
            'category' => "woo_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'orderby',
                    __('Order by', 'breakdance'),
                    [
                        'type' => 'dropdown',
                        'items' => [
                            ['value' => 'order', 'text' => __('Category order', 'breakdance')],
                            ['value' => 'name', 'text' => __('Name', 'breakdance')],
                        ]
                    ]
                ),
                control(
                    'dropdown',
                    __('Show as dropdown', 'breakdance'),
                    [
                        'type' => 'toggle'
                    ]
                ),
                control(
                    'count',
                    __('Show product counts', 'breakdance'),
                    [
                        'type' => 'toggle'
                    ]
                ),
                control(
                    'hierarchical',
                    __('Show hierarchy', 'breakdance'),
                    [
                        'type' => 'toggle'
                    ]
                ),
                control(
                    'show_children_only',
                    __('Only show children', 'breakdance'),
                    [
                        'type' => 'toggle'
                    ]
                ),
                control(
                    'hide_empty',
                    __('Hide empty categories', 'breakdance'),
                    [
                        'type' => 'toggle'
                    ]
                ),
                control(
                    'max_depth',
                    __('Maximum depth', 'breakdance'),
                    [
                        'type' => 'text'
                    ]
                ),
            ]
        ]);

        // \Breakdance\WPWidgets\register([
        //     'slug' => 'wc_product_tag_Cloud',
        //     'name' => 'Product Tag Cloud',
        //     'className' => 'WC_Widget_Product_Tag_Cloud',
        //     'category' => "woo_general",
        //     'controls' => [
        //         control(
        //             'title',
        //             'Title',
        //             ['type' => 'text']
        //         )
        //     ]
        // ]);

        // \Breakdance\WPWidgets\register([
        //     'slug' => 'wc_recently_viewed_products',
        //     'name' => 'Recently Viewed Products',
        //     'className' => 'WC_Widget_Recently_Viewed',
        //     'category' => "woo_general",
        //     'controls' => [
        //         control(
        //             'title',
        //             'Title',
        //             ['type' => 'text']
        //         ),
        //         control(
        //             'number',
        //             'Number of products',
        //             ['type' => 'number']
        //         )
        //     ]
        // ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wc_recently_reviews',
            'name' => __('Recent Reviews', 'breakdance'),
            'className' => 'WC_Widget_Recent_Reviews',
            'category' => "woo_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'number',
                    __('Number', 'breakdance'),
                    ['type' => 'number']
                )
            ]
        ]);

        // \Breakdance\WPWidgets\register([
        //     'slug' => 'wc_products_list',
        //     'name' => 'Products List',
        //     'className' => 'WC_Widget_Products',
        //     'category' => "woo_general",
        //     'controls' => [
        //         control(
        //             'title',
        //             'Title',
        //             ['type' => 'text']
        //         ),
        //         control(
        //             'number',
        //             'Number of products',
        //             ['type' => 'number']
        //         ),
        //         control(
        //             'show',
        //             'Show',
        //             ['type' => 'dropdown', 'items' => [
        //                 ['value' => '', 'text' => 'All products'],
        //                 ['value' => 'featured', 'text' => 'Featured products'],
        //                 ['value' => 'onsale', 'text' => 'On-sale products']
        //             ]]
        //         ),
        //         control(
        //             'order',
        //             'Order',
        //             ['type' => 'dropdown', 'items' => [
        //                 ['value' => 'asc', 'text' => 'Ascending'],
        //                 ['value' => 'desc', 'text' => 'Descending'],
        //             ]]
        //         ),
        //         control(
        //             'hide_free',
        //             'Hide free products',
        //             ['type' => 'toggle']
        //         ),
        //         control(
        //             'show_hidden',
        //             'Show hidden products',
        //             ['type' => 'toggle']
        //         ),
        //     ]
        // ]);


        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("woo_general");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\woo_widget",
            controlSection(
                'woo_widget',
                __('Woo Widget', 'breakdance'),
                array_merge(
                    [
                        control(
                            'widget',
                            __('Widget', 'breakdance'),
                            ['type' => 'dropdown', 'items' => getWidgetsAsDropdownItems($widgets), 'layout' => 'vertical'],
                        )
                    ],
                    getWidgetControlSections($widgets)
                )
            ),
            true
        );
    },
    1000 /* needed because WidgetsController::getInstance()->widgets is also populated
    on init, so we need to run this after it's already populated */
);
