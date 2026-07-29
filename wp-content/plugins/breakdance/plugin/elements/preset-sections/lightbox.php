<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
    "EssentialElements\\lightbox_design",
    controlSection("lightbox", __("Lightbox", 'breakdance'), [
        control(
            'info_notice', __('Info Notice', 'breakdance'),
            [
                'type' => 'alert_box',
                'layout' => 'vertical',
                'alertBoxOptions' => [
                    'style' => 'info',
                    'content' => '<p>' . __('View the page on the frontend to preview the lightbox.', 'breakdance') . '</p>'
                ]
            ]
        ),
        control("background", __("Background", 'breakdance'), [
            'type' => 'color',
            'layout' => 'inline',
            'colorOptions' => ['type' => 'solidAndGradient']
        ]),
        c(
            "controls",
            __("Controls", 'breakdance'),
            [],
            [
                'type' => 'color',
                'layout' => 'inline'
            ],
            false,
            true
        ),
        control(
            "thumbnails",
            __("Thumbnails", 'breakdance'),
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control('thumbnail', __('Thumbnail', 'breakdance'), [
            'type' => 'color',
            'condition' => ['path' => '%%CURRENTPATH%%.thumbnails', 'operand' => 'is set', 'value' => null]
        ]),
        control('thumbnail_active', __('Thumbnail Active', 'breakdance'), [
            'type' => 'color',
            'condition' => ['path' => '%%CURRENTPATH%%.thumbnails', 'operand' => 'is set', 'value' => null]
        ]),
        control(
            "animated_thumbnails",
            __("Animated Thumbnails", 'breakdance'),
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control(
            "autoplay",
            __("Autoplay", 'breakdance'),
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control(
            "speed",
            __("Speed", 'breakdance'),
            ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => [], 'defaultType' => 'ms'], 'condition' => ['path' => '%%CURRENTPATH%%.autoplay', 'operand' => 'is set', 'value' => null]]
        ),
        control(
            "show_download_button",
            __("Show Download Button", 'breakdance'),
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control(
            "disable_autoplay_on_first_video",
            __("Disable Autoplay on First Video", 'breakdance'),
            ['type' => 'toggle', 'layout' => 'inline'],
        ),

//        control(
//            "autoplay_videos",
//            "Autoplay Videos",
//            ['type' => 'toggle', 'layout' => 'inline'],
//        ),
        ],
    ),
    true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\lightbox_single_design",
        controlSection("lightbox", __("Lightbox", 'breakdance'), [
            control("background", __("Background", 'breakdance'), [
                'type' => 'color',
                'layout' => 'inline',
                'colorOptions' => ['type' => 'solidAndGradient']
            ]),
            c(
                "controls",
                __("Controls", 'breakdance'),
                [],
                [
                    'type' => 'color',
                    'layout' => 'inline'
                ],
                false,
                true
            )],
        ),
        true
    );
});
