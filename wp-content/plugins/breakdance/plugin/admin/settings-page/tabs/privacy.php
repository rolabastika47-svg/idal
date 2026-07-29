<?php

namespace Breakdance\Admin\SettingsPage\PrivacyTab;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\PrivacyTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(esc_html__('Privacy', 'breakdance'), 'privacy', '\Breakdance\Admin\SettingsPage\PrivacyTab\tab', 1200);
}

function tab()
{
    $nonce_action = 'breakdance_admin_privacy_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (filter_input(INPUT_POST, 'disable_page_and_session_tracking_cookies')) {
            \Breakdance\Data\set_global_option('breakdance_settings_disable_view_tracking_cookies', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_disable_view_tracking_cookies', false);
        }

        if (filter_input(INPUT_POST, 'enable_tracking')) {
            \Breakdance\Data\set_global_option('enable_tracking', 'yes');
        } else {
            \Breakdance\Data\set_global_option('enable_tracking', 'no');
        }

        if (filter_input(INPUT_POST, 'hide_partner_discounts_page')) {
            \Breakdance\Data\set_global_option('settings_hide_partner_discounts', 'yes');
        } else {
            \Breakdance\Data\set_global_option('settings_hide_partner_discounts', 'no');
        }
    }

    /** @var string|false $disable_page_and_session_tracking_cookies */
    $disable_page_and_session_tracking_cookies = \Breakdance\Data\get_global_option('breakdance_settings_disable_view_tracking_cookies');

    /** @var string|false $enable_tracking */
    $enable_tracking = \Breakdance\Data\get_global_option('enable_tracking');

    /** @var string|false $hide_partner_discounts */
    $hide_partner_discounts = \Breakdance\Data\get_global_option('settings_hide_partner_discounts');

    $plugin_name = __bdox('plugin_name');
?>

    <h2><?php esc_html_e('Privacy', 'breakdance'); ?></h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <?php if (!bdox_run_filters('breakdance_disable_track_view_and_session_counts', false)) { ?>
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Disable Page & Session Cookies', 'breakdance'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="disable_page_and_session_tracking_cookies">
                                <input type="checkbox" <?php echo $disable_page_and_session_tracking_cookies ? 'checked' : ''; ?> name="disable_page_and_session_tracking_cookies" value="yes" id="disable_page_and_session_tracking_cookies"> <?php esc_html_e('Disable', 'breakdance'); ?>
                            </label>
                        </fieldset>
                        <p class="description">
                            <?php
                                /* translators: 1: Plugin name, 2: Condition name in <code> tags, 3: Another condition name in <code> tags */
                                printf(esc_html__('%1$s provides conditions to show or hide certain content based on page view and session count. Enabling this option will prevent %1$s from setting the tracking cookies necessary for this functionality. The %2$s and %3$s conditions will not work.', 'breakdance'), $plugin_name, '<code>' . esc_html__('Page View Count', 'breakdance') . '</code>', '<code>' . esc_html__('Session Count', 'breakdance') . '</code>');
                            ?>
                        </p>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row">
                        <?php /* translators: %s: Plugin name */ ?>
                        <?php printf(esc_html__('Improve %s', 'breakdance'), $plugin_name); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_tracking">
                                <input type="checkbox" <?php echo $enable_tracking === 'yes' ? 'checked' : ''; ?> name="enable_tracking" value="yes" id="enable_tracking">
                                <?php
                                    /* translators: %s: Plugin name */
                                    printf(esc_html__('Yes, I want to help improve %s', 'breakdance'), $plugin_name);
                                ?>
                            </label>
                        </fieldset>
                        <p class="description">
                            <?php
                                /* translators: 1: Opening <a> tag, 2: Closing </a> tag, 3: Plugin name */
                                printf(esc_html__('By allowing us to %1$scollect usage data%2$s, you help us make %3$s better for everyone.', 'breakdance'), '<a href="https://breakdance.com/documentation/settings/privacy/" target="_blank">', '</a>', $plugin_name);
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Partner Discounts
                    </th>
                    <td>
                        <fieldset>
                            <label for="hide_partner_discounts_page">
                                <input type="checkbox" <?php echo $hide_partner_discounts === 'yes' ? 'checked' : ''; ?> name="hide_partner_discounts_page" value="yes" id="hide_partner_discounts_page"> Hide the Partner Discounts page from the admin menu
                            </label>
                        </fieldset>
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
?>
