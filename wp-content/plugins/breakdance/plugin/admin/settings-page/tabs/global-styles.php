<?php

namespace Breakdance\Admin\SettingsPage\GlobalStylesTab;

use function Breakdance\Admin\get_browse_mode_url_with_return_back_to_current_page;

add_action(
    'breakdance_register_admin_settings_page_register_tabs',
    '\Breakdance\Admin\SettingsPage\GlobalStylesTab\register'
);

function register()
{

    if (BREAKDANCE_MODE !== 'breakdance') {
        return;
    }

    \Breakdance\Admin\SettingsPage\addTab(
        esc_html__('Global Styles', 'breakdance'),
        'global_styles',
        '\Breakdance\Admin\SettingsPage\GlobalStylesTab\tab',
        100
    );
}

function tab()
{
?>
    <h2><?php esc_html_e('Global Styles', 'breakdance'); ?></h2>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <?php esc_html_e('Edit Global Styles', 'breakdance'); ?>
                </th>
                <td class='breakdance-launcher-row'>
                    <a class="breakdance-browse-mode-button" href="<?= get_browse_mode_url_with_return_back_to_current_page(); ?>"><?php esc_html_e('Launch Breakdance', 'breakdance'); ?></a>
                    <p class="description" style='margin-top: 25px;'><?php esc_html_e('Edit Global Settings & Selectors while browsing through your website.', 'breakdance'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>

    <style>
        .breakdance-launcher-row {
            padding: 40px !important;
        }

        .breakdance-browse-mode-button {
            --breakdance-launcher-primary-color: #ffc514;
            --breakdance-launcher-secondary-color: #f6b800;
            color: black !important;
            line-height: 1;
            border: none;
            cursor: pointer;
            background-color: var(--breakdance-launcher-primary-color);
            transition: 0.3s background-color ease;
            text-decoration: none !important;
            font-size: 13px;
            font-weight: 500;
            border-radius: 0px;
            padding: 10px 20px;
            margin-bottom: 16px;
        }
    </style>

<?php
}


?>
