<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_all_archives_as_template_previewable_items;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerArchiveAllRules');

function registerArchiveAllRules()
{

    \Breakdance\Themeless\registerTemplateType(
        __('Archive', 'breakdance'),
        [
            'slug' => 'all-archives',
            'label' => __('All Archives', 'breakdance'),
            'callback' =>
            function (): bool {
                return is_archive();
            },

            'templatePreviewableItems' =>
            function () {
                return get_all_archives_as_template_previewable_items();
            },
            'defaultPriority' => TEMPLATE_PRIORITY_ALL_ARCHIVE_OR_ALL_SINGLE
        ]
    );
}
