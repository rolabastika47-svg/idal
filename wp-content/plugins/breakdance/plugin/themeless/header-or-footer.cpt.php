<?php

namespace Breakdance\Themeless;

/**
 * @return void
 */
function register_header_post_type()
{
    \register_post_type(
        BREAKDANCE_HEADER_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => esc_html__('Headers', 'breakdance'),
                    'singular_name' => esc_html__('Header', 'breakdance'),
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_HEADER_POST_TYPE);
}


/**
 * @return void
 */
function register_footer_post_type()
{

    \register_post_type(
        BREAKDANCE_FOOTER_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => esc_html__('Footers', 'breakdance'),
                    'singular_name' => esc_html__('Footer', 'breakdance'),
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_FOOTER_POST_TYPE);
}
