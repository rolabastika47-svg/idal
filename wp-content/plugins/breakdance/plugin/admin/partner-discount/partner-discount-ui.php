<?php

// @psalm-ignore-file

namespace Breakdance\Admin;

require_once __DIR__ . '/partner-discount-sdk.php';

// Render the Breakdance discounts
function render_breakdance_partner_discounts_page()
{
    echo '<div class="wrap">';
    echo render_partner_discount_ui(
        get_breakdance_partner_discounts_page(),
        get_breakdance_brand_colors()
    );
    echo '</div>';
}

// Render the Oxygen discounts
function render_oxygen_partner_discounts_page()
{
    echo '<div class="wrap">';
    echo render_partner_discount_ui(
        get_oxygen_partner_discounts_page(),
        get_oxygen_brand_colors()
    );
    echo '</div>';
}

// Set the Breakdance colours
function get_breakdance_brand_colors()
{
    return [
        'primary-color' => '#ffc514',
        'primary-color-hover' => '#e6ac00',
        'btn-text-color' => '#222',
        'btn-text-color-hover' => '#222'
    ];
}

// Set the Oxygen colours
function get_oxygen_brand_colors()
{
    return [
        'primary-color' => '#5137c3',
        'primary-color-hover' => '#4532a1',
        'btn-text-color' => '#fff',
        'btn-text-color-hover' => '#fff'
    ];
}

// Set the Breakdance discounts
function get_breakdance_partner_discounts_page()
{
    return [
        [
            'name' => 'AnalyticsWP',
            'desc' => __('This privacy-compliant WordPress analytics plugin gives detailed insights into user behavior beyond what traditional tools can provide, and has a dedicated integration for WooCommerce.', 'breakdance'),
            'code' => 'breakdance2024',
            'discount' => '20%',
            'link' => 'https://analyticswp.com/pricing/?wt_coupon=breakdance2024',
            'image' => 'https://breakdance.com/wp-content/uploads/2025/06/awp-Icon.svg'
        ],
        [
            'name' => 'WPCodeBox',
            'desc' => __('Save code from inside Breakdance to WPCodebox in one click. Use cloud snippets to share across your sites and explore the Code Snippet Repository full of tested snippets.', 'breakdance'),
            'code' => 'KMWOV0WBKJ',
            'discount' => '20%',
            'link' => 'https://wpcodebox.com/',
            'image' => 'https://breakdance.com/wp-content/uploads/2025/06/WPCodeBox-Logo-Small-Dark.webp'
        ],
        [
            'name' => 'WP All Import',
            'desc' => __('The #1 data import and export solution for WordPress and WooCommerce - by Soflyy, the creators of Oxygen.', 'breakdance'),
            'code' => 'soflyybreakdance',
            'discount' => '20%',
            'link' => 'https://www.wpallimport.com/?edd_action=add_to_cart&download_id=10870034&edd_options%5Bprice_id%5D=1&discount=soflyybreakdance',
            'image' => 'https://breakdance.com/wp-content/uploads/2025/06/wpai-icon.png'
        ],
        [
            'name' => 'Oxygen',
            'desc' => __('Created by the same team behind WP All Import, Oxygen is the go-to WordPress website builder for highly advanced users & developers who love to code.', 'breakdance'),
            'code' => 'BD20',
            'discount' => '20%',
            'link' => 'https://oxygenbuilder.com/checkout/?edd_action=add_to_cart&download_id=4790638&discount=BD20',
            'image' => 'https://breakdance.com/wp-content/uploads/2025/06/logo-minimal-black.webp'
        ],
        // [
        //     'name' => 'Meta Box',
        //     'desc' => 'Meta Box is a WordPress custom fields plugin for flexible content management using custom post types and custom fields.',
        //     'code' => 'BREAKDANCE20',
        //     'discount' => '20%',
        //     'link' => 'https://metabox.io/pricing/',
        //     'image' => 'https://breakdance.com/wp-content/uploads/2025/06/metabox-logo-square.webp'
        // ] //,
        //[
        //    'name' => 'Slim SEO',
        //    'desc' => 'Premium SEO plugins that are lightweight, performant, and support Meta Box & page builders. Built by the same team at MetaBox.io.',
        //    'code' => 'BREAKDANCE20',
        //    'discount' => '20%',
        //    'link' => 'https://wpslimseo.com/products/',
        //    'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/slimseo-logo-square.png'
        //]
    ];
}

// Set the Oxygen discounts
function get_oxygen_partner_discounts_page()
{
    return [
        [
            'name' => 'AnalyticsWP',
            'desc' => __('This privacy-compliant WordPress analytics plugin gives detailed insights into user behavior beyond what traditional tools can provide, and has a dedicated integration for WooCommerce.', 'breakdance'),
            'code' => 'oxygen2024',
            'discount' => '20%',
            'link' => 'https://analyticswp.com/pricing/?wt_coupon=oxygen2024',
            'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/awp-Icon.svg'
        ],
        [
            'name' => 'Breakdance',
            'desc' => __('Created by the same team behind WP All Import, Breakdance is a modern visual site builder for WordPress that combines professional power with drag & drop ease of use.', 'breakdance'),
            'code' => 'OXY20',
            'discount' => '20%',
            'link' => 'https://breakdance.com/checkout?edd_action=add_to_cart&discount=OXY20&download_id=14&edd_options%5Bprice_id%5D=1',
            'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/breakdance-icon.png'
        ],
        [
            'name' => 'WP All Import',
            'desc' => __('The #1 data import and export solution for WordPress and WooCommerce - by Soflyy, the creators of Oxygen.', 'breakdance'),
            'code' => 'soflyyoxygen',
            'discount' => '20%',
            'link' => 'https://www.wpallimport.com/?edd_action=add_to_cart&download_id=10870034&edd_options%5Bprice_id%5D=1&discount=soflyyoxygen',
            'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/wpai-icon.png'
        ],
        [
            'name' => 'WPCodeBox',
            'desc' => __('Save code from inside Breakdance to WPCodebox in one click. Use cloud snippets to share across your sites and explore the Code Snippet Repository full of tested snippets.', 'breakdance'),
            'code' => 'KMWOV0WBKJ',
            'discount' => '20%',
            'link' => 'https://wpcodebox.com/',
            'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/WPCodeBox-Logo-Small-Dark.webp'
        ],
        // [
        //     'name' => 'Meta Box',
        //     'desc' => 'Meta Box is a WordPress custom fields plugin for flexible content management using custom post types and custom fields.',
        //     'code' => 'BREAKDANCE20',
        //     'discount' => '20%',
        //     'link' => 'https://metabox.io/pricing/',
        //     'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/metabox-logo-square.webp'
        // ]
        //,
        //[
        //    'name' => 'Slim SEO',
        //    'desc' => 'Premium SEO plugins that are lightweight, performant, and support Meta Box & page builders. Built by the same team at MetaBox.io.',
        //    'code' => 'BREAKDANCE20',
        //    'discount' => '20%',
        //    'link' => 'https://wpslimseo.com/products/',
        //    'image' => 'https://oxygenbuilder.com/wp-content/uploads/2025/06/slimseo-logo-square.png'
        //]
    ];
}
