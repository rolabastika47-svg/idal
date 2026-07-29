<?php

namespace Breakdance\HeaderFooterTrackingCode\SettingsTab;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;
use function Breakdance\AJAX\get_nonce_key_for_ajax_requests;
use function Breakdance\AJAX\get_nonce_for_ajax_requests;
use function Breakdance\Partners\CodeBox\isCodeBoxInstalled;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\HeaderFooterTrackingCode\SettingsTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(
        esc_html__('Custom Code', 'breakdance'),
        'header_footer',
        '\Breakdance\HeaderFooterTrackingCode\SettingsTab\tab',
        1300
    );
}

function tab()
{
    // Enqueue and localize the settings JS for AJAX handling
    wp_enqueue_script(
        'breakdance-tracking-code-settings',
        plugin_dir_url(__FILE__) . 'tracking-code-settings.js',
        ['jquery'],
        '1.0',
        true
    );
    wp_localize_script(
        'breakdance-tracking-code-settings',
        'breakdanceAjax',
        [
            'url'      => admin_url('admin-ajax.php'),
            'nonceKey' => get_nonce_key_for_ajax_requests(),
            'nonce'    => get_nonce_for_ajax_requests(),
        ]
    );

    $tracking_code_header = (string) get_global_option('breakdance_settings_tracking_code_header');
    $tracking_code_footer = (string) get_global_option('breakdance_settings_tracking_code_footer');

    $nonce_action = 'breakdance_admin_custom-code_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        $tracking_code_header = (string) filter_input(INPUT_POST, 'tracking_code_header');
        set_global_option('breakdance_settings_tracking_code_header', $tracking_code_header);

        $tracking_code_footer = (string) filter_input(INPUT_POST, 'tracking_code_footer');
        set_global_option('breakdance_settings_tracking_code_footer', $tracking_code_footer);
    }

?>

    <h2><?php esc_html_e('Custom Code', 'breakdance'); ?></h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <?php

        if (!defined('BREAKDANCE_ESSENTIAL_NOTICES_ONLY')) {
            \Breakdance\Partners\AnalyticsWP\noticeForCustomCodeScreen();
        }

        ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="tracking_code_header"><?php esc_html_e('Header Code', 'breakdance'); ?></label></th>
                    <td>

                        <fieldset>
                            <p>
                            <div id="tracking_code_header_container">
                                <textarea name="tracking_code_header" rows="10" cols="50" class="large-text code" id="tracking_code_header"><?php echo htmlspecialchars((string) $tracking_code_header); ?></textarea>
                                <?php if (isCodeBoxInstalled()) { ?>
                                    <button type="button" id="send_to_codebox_header" class="button"><?php esc_html_e('Send to CodeBox', 'breakdance'); ?></button>
                                    <button type="button" id="open_codebox_header" class="button"><?php esc_html_e('Open CodeBox', 'breakdance'); ?></button>
                                <?php } ?>
                                <button type="button" id="clear_codebox_header" class="button"><?php esc_html_e('Clear Code', 'breakdance'); ?></button>
                            </div>
                            </p>
                        </fieldset>
                        <p class="description">
                            <?php esc_html_e('This code will be inserted inside the <head> tag.', 'breakdance'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="tracking_code_footer"><?php esc_html_e('Footer Code', 'breakdance'); ?></label>
                    </th>
                    <td>

                        <fieldset>
                            <p>
                            <div id="tracking_code_footer_container">
                                <textarea name="tracking_code_footer" rows="10" cols="50" class="large-text code" id="tracking_code_footer"><?php echo htmlspecialchars((string) $tracking_code_footer); ?></textarea>
                                <?php if (isCodeBoxInstalled()) { ?>
                                    <button type="button" id="send_to_codebox_footer" class="button"><?php esc_html_e('Send to CodeBox', 'breakdance'); ?></button>
                                    <button type="button" id="open_codebox_footer" class="button"><?php esc_html_e('Open CodeBox', 'breakdance'); ?></button>
                                <?php } ?>
                                <button type="button" id="clear_codebox_footer" class="button"><?php esc_html_e('Clear Code', 'breakdance'); ?></button>
                            </div>
                            </p>
                        </fieldset>
                        <p class="description">
                            <?php esc_html_e('This code will be inserted near the closing </body> tag.', 'breakdance'); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>">
        </p>


    </form>

<?php
}

add_action('admin_enqueue_scripts', function () {
    // Only load on your settings page. You may need to adjust the screen ID or $_GET['page'] check:
    if (! isset($_GET['page']) || $_GET['page'] !== 'breakdance-settings') {
        return;
    }
    if (isset($_GET['tab']) && $_GET['tab'] === 'header_footer') {
        wp_enqueue_script(
            'breakdance-tracking-code-settings',
            plugin_dir_url(__FILE__) . 'tracking-code-settings.js',
            [], // no jQuery dependency needed
            '1.0',
            true
        );
        wp_localize_script(
            'breakdance-tracking-code-settings',
            'breakdanceAjax',
            [
                'url'      => admin_url('admin-ajax.php'),
                'nonceKey' => get_nonce_key_for_ajax_requests(),
                'nonce'    => get_nonce_for_ajax_requests(),
            ]
        );
    }
});

?>
