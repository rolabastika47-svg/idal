<?php

/**
 * @var array $propertiesData
 */

$templateToRenderId = \Breakdance\Themeless\ThemelessController::getInstance()->popHierarchy();

add_filter('breakdance_render_wrap_in_main_tag', '__return_false');

if ($templateToRenderId) {
    echo (string) \Breakdance\Render\render($templateToRenderId);
} else {
    while (have_posts()) {
        the_post();
        the_content();
    }
}

remove_filter('breakdance_render_wrap_in_main_tag', '__return_false');
