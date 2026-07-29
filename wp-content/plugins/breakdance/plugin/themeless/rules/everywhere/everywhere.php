<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_all_archives_as_template_previewable_items;
use function Breakdance\Themeless\get_posts_as_template_previewable_items;
use function Breakdance\Util\get_public_posts_excluding_templates_and_attachments;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerEverywhereEverywhereRules');

function registerEverywhereEverywhereRules()
{
    \Breakdance\Themeless\registerTemplateType(
        __('Everywhere', 'breakdance'),
        [
            'slug' => 'everywhere',
            'label' => __('Everywhere', 'breakdance'),
            'callback' => function (): bool {
                return true;
            },

            'templatePreviewableItems' => function () {
                $postTypes = get_public_posts_excluding_templates_and_attachments();

                return array_merge(
                    get_posts_as_template_previewable_items([
                        'post_type' => $postTypes,
                    ]),
                    get_all_archives_as_template_previewable_items()
                );
            },
            'defaultPriority' => TEMPLATE_PRIORITY_CATCH_ALL,
        ]
    );
}
