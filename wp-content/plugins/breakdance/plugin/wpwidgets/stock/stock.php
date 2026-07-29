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
                'slug' => 'wp_text',
                'name' => __('Text', 'breakdance'),
                'className' => 'WP_Widget_Text',
                'category' => "wp_general",
                'controls' => [
                    control(
                        'title',
                        __('Title', 'breakdance'),
                        ['type' => 'text']
                    ),
                    control(
                        'text',
                        __('Text', 'breakdance'),
                        ['type' => 'text']
                    )
                ]
            ]
        );

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_archives',
            'name' => __('Archives', 'breakdance'),
            'className' => 'WP_Widget_Archives',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'dropdown',
                    __('Display as dropdown', 'breakdance'),
                    ['type' => 'toggle']
                ),
                control(
                    'count',
                    __('Show post count', 'breakdance'),
                    ['type' => 'toggle']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_categories',
            'name' => __('Categories', 'breakdance'),
            'className' => 'WP_Widget_Categories',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'dropdown',
                    __('Display as dropdown', 'breakdance'),
                    ['type' => 'toggle']
                ),
                control(
                    'hierarchical',
                    __('Show hierarchy', 'breakdance'),
                    ['type' => 'toggle']
                ),
                control(
                    'count',
                    __('Show post count', 'breakdance'),
                    ['type' => 'toggle']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_calendar',
            'name' => __('Calendar', 'breakdance'),
            'className' => 'WP_Widget_Calendar',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_recent_comments',
            'name' => __('Recent Comments', 'breakdance'),
            'className' => 'WP_Widget_Recent_Comments',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'number',
                    __('Number of comments to show', 'breakdance'),
                    ['type' => 'number']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_tag_cloud',
            'name' => __('Tag Cloud', 'breakdance'),
            'className' => 'WP_Widget_Tag_Cloud',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'taxonomy',
                    __('Taxonomy', 'breakdance'),
                    [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'items' => [
                            ['value' => 'category', 'text' => __('Categories', 'breakdance')],
                            ['value' => 'post_tag', 'text' => __('Tags', 'breakdance')],
                            ['value' => 'link_category', 'text' => __('Link Categories', 'breakdance')]
                        ]
                    ]
                ),
                control(
                    'count',
                    __('Show tag count', 'breakdance'),
                    ['type' => 'toggle']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_rss',
            'name' => __('RSS', 'breakdance'),
            'className' => 'WP_Widget_RSS',
            'category' => "wp_general",
            'controls' => [
                control(
                    'url',
                    __('URL', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'items',
                    __('Items', 'breakdance'),
                    [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'items' => array_map(static function($i) {
                            return ['text' => strval($i), 'value' => strval($i)];
                        }, range(1, 20))
                    ]
                ),
                control(
                    'show_summary',
                    __('Display item content', 'breakdance'),
                    ['type' => 'toggle']
                ),
                control(
                    'show_author',
                    __('Display item author', 'breakdance'),
                    ['type' => 'toggle']
                ),
                control(
                    'show_date',
                    __('Display item date', 'breakdance'),
                    ['type' => 'toggle']
                )
            ]
        ]);

        \Breakdance\WPWidgets\register([
            'slug' => 'wp_recent_posts',
            'name' => __('Recent Posts', 'breakdance'),
            'className' => 'WP_Widget_Recent_Posts',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'number',
                    __('Number of posts to show', 'breakdance'),
                    ['type' => 'number']
                ),
                control(
                    'show_date',
                    __('Show date', 'breakdance'),
                    ['type' => 'toggle']
                ),
            ]
        ]);


        \Breakdance\WPWidgets\register([
            'slug' => 'wp_pages',
            'name' => __('Pages', 'breakdance'),
            'className' => 'WP_Widget_Pages',
            'category' => "wp_general",
            'controls' => [
                control(
                    'title',
                    __('Title', 'breakdance'),
                    ['type' => 'text']
                ),
                control(
                    'sortby',
                    __('Sort by', 'breakdance'),
                    ['type' => 'dropdown']
                ),
                control(
                    'exclude',
                    __('Exclude', 'breakdance'),
                    ['type' => 'text', 'items' => [
                        ['value' => 'post_title', 'text' => __('Page Title', 'breakdance')],
                        ['value' => 'menu_order', 'text' => __('Page Order', 'breakdance')],
                        ['value' => 'ID', 'text' => __('Page ID', 'breakdance')]
                    ]]
                ),
            ]
        ]);

        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("wp_general");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wp_widget",
            controlSection(
                'wp_widget',
                __('WP Widget', 'breakdance'),
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
