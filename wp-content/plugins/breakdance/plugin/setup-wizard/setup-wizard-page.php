<?php

namespace Breakdance\SetupWizard;

use Breakdance\Licensing\LicenseKeyManager;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_admin_menu', function () {
    add_submenu_page('options.php', esc_html__('Setup Wizard', 'breakdance'), esc_html__('Setup Wizard', 'breakdance'), 'manage_options', 'breakdance_setup_wizard', "Breakdance\SetupWizard\display_setup_wizard");
});

/**
 * @param array{disable_theme?: bool, disable_bloat?: bool, analyticswp?: bool, enable_tracking?: bool, hide_partner_discounts?: bool, key?: string|null} $settings
 * @return void
 */
function save_setup_wizard_settings($settings)
{
    /** @var bool $disable_theme */
    $disable_theme = $settings['disable_theme'] ?? true;
    /** @var bool $disable_bloat */
    $disable_bloat = $settings['disable_bloat'] ?? false;
    /** @var bool $analyticswp */
    $analyticswp = $settings['analyticswp'] ?? false;
    /** @var bool $enable_tracking */
    $enable_tracking = $settings['enable_tracking'] ?? true;
    /** @var bool $hide_partner_discounts */
    $hide_partner_discounts = $settings['hide_partner_discounts'] ?? false;
    /** @var string|null $key */
    $key = $settings['key'] ?? null;

    if ($disable_theme) {
        set_global_option('is_theme_disabled', 'yes');

        if ($disable_bloat) {
            set_global_option('breakdance_settings_bloat_eliminator', [
                'gutenberg-blocks-css',
                'rsd-links',
                'wlw-link',
                'rest-api',
                'shortlink',
                'rel-links',
                'wp-generator',
                'feed-links',
                'xml-rpc',
                'wp-emoji',
                'wp-oembed',
                'wp-dashicons',
            ]);
        } else {
            set_global_option('breakdance_settings_bloat_eliminator', []);
        }
    } else {
        set_global_option('is_theme_disabled', 'no');
        set_global_option('breakdance_settings_bloat_eliminator', []);
    }

    if ($key !== null) {
        $trimmed_key = trim((string) $key);
        LicenseKeyManager::getInstance()->changeLicenseKey($trimmed_key === '' ? null : $trimmed_key);
    }

    if ($analyticswp) {
        /** @psalm-suppress UndefinedFunction  */
        \Breakdance\Partners\AnalyticsWP\installAnalyticsWPPlugin();
    }

    // Save the tracking preference
    set_global_option('enable_tracking', $enable_tracking ? 'yes' : 'no');

    set_global_option('settings_hide_partner_discounts', $hide_partner_discounts ? 'yes' : 'no');

    // In Oxygen, we add default hidden elements so the user can later unhide them from Settings
    \Breakdance\BreakdanceOxygen\addDefaultHiddenElements();
}

function display_setup_wizard()
{
    $nonce_action = 'breakdance_admin_setup-wizard';
    /**
     * @var string|null $key_error
     */
    $key_error = null;

    $disable_theme = true;
    $analyticswp = false;
    $disable_bloat = false;
    $enable_tracking = true;
    $hide_partner_discounts = false;

    if (is_post_request() && check_admin_referer($nonce_action)) {
        $disable_theme = (bool) filter_input(INPUT_POST, 'disable_theme', FILTER_VALIDATE_BOOLEAN);
        $disable_bloat = (bool) filter_input(INPUT_POST, 'disable_bloat', FILTER_VALIDATE_BOOLEAN);
        $analyticswp = (bool) filter_input(INPUT_POST, 'analyticswp', FILTER_VALIDATE_BOOLEAN);
        $enable_tracking = (bool) filter_input(INPUT_POST, 'enable_tracking', FILTER_VALIDATE_BOOLEAN);
        $hide_partner_discounts = (bool) filter_input(INPUT_POST, 'hide_partner_discounts', FILTER_VALIDATE_BOOLEAN);

        /**
         * @var mixed|null|false $key_raw
         */
        $key_raw = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $key = ($key_raw !== null && $key_raw !== false) ? (string) $key_raw : null;

        save_setup_wizard_settings([
            'disable_theme' => $disable_theme,
            'disable_bloat' => $disable_bloat,
            'analyticswp' => $analyticswp,
            'enable_tracking' => $enable_tracking,
            'hide_partner_discounts' => $hide_partner_discounts,
            'key' => $key,
        ]);

        if ($key_error === null) {
            // wp_redirect doesn't work here because headers are already sent
            print('<script>window.location.href="admin.php?page=breakdance"</script>');
        }
    }
?>
    <style>
        .form-table {
            margin-top: 20px;
            max-width: 1100px;
        }

        .form-table th,
        .form-table td {
            border: 1px solid #d5d5d5;
            padding: 20px;
        }

        table.form-table {
            background-color: white;
            border-collapse: collapse;
        }

        .valign-th-middle {
            vertical-align: middle !important;
        }

        .padded-notice {
            margin-top: 20px !important;
            margin-bottom: 20px !important;
        }
    </style>
    <script>
        window.breakdanceSetupWizardShowBloatRow = (e) => {
            // const row = window.document.getElementById('disable_bloat_row');
            // if (row) {
            //     row.style.display = 'table-row';
            // }
        }
        window.breakdanceSetupWizardHideBloatRow = (e) => {
            // const row = window.document.getElementById('disable_bloat_row');
            // if (row) {
            //     row.style.display = 'none';
            // }
        }
    </script>
    <div class="wrap">
        <h1><?php esc_html_e('Breakdance Setup Wizard', 'breakdance'); ?></h1>

        <form action="" method="post">
            <?php
            wp_nonce_field($nonce_action); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e('Theme', 'breakdance'); ?></th>
                        <td>

                            <style>
                                .disable-theme-recommended {
                                    font-size: 0.65em;
                                    display: inline-block;
                                    padding: 5px;
                                    background-color: #d2f2b8;
                                    color: #31610a;
                                    line-height: 1;
                                    font-weight: 500;
                                    border-radius: 3px;
                                    position: relative;
                                    top: -2px;
                                    text-transform: uppercase;
                                }
                            </style>

                            <fieldset>
                                <label for="disable_theme__yes"><input type="radio" id="disable_theme__yes" name="disable_theme" value="true" onchange="breakdanceSetupWizardShowBloatRow()" <?= $disable_theme ? 'checked' : '' ?> />

                                    <?php esc_html_e('Disable Theme', 'breakdance'); ?> <span class='disable-theme-recommended'><?php esc_html_e('recommended', 'breakdance'); ?></span>
                                    <p class="description"><?php esc_html_e('Design every part of your site with Breakdance. Disabling your theme gives you the best performance and maximum flexibility.', 'breakdance'); ?></p>

                                </label><br />
                                <label for="disable_theme__no"><input type="radio" id="disable_theme__no" name="disable_theme" onchange="breakdanceSetupWizardHideBloatRow()" <?= $disable_theme ? '' : 'checked' ?> value="false" />

                                    <?php esc_html_e('Keep Theme', 'breakdance'); ?>
                                    <p class="description"><?php esc_html_e('The design of your existing site won\'t be affected. Your theme\'s styles may affect the design of Breakdance elements.', 'breakdance'); ?></p>

                                </label>
                            </fieldset>

                            <br />
                            <p class="description"><?php esc_html_e('You can change this at any time from Breakdance → Settings → Theme', 'breakdance'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr id="disable_bloat_row" style="<?= $disable_theme ? 'display: none' : 'display: none' ?>">
                        <th scope="row"><?php esc_html_e('Performance', 'breakdance'); ?></th>
                        <td>
                            <fieldset>
                                <label for="disable_bloat__yes"><input type="radio" id="disable_bloat__yes" name="disable_bloat" value="true" <?= $disable_bloat ? 'checked' : '' ?> /><?php esc_html_e('Clean Common Bloat - dashicons for logged out users, disable Gutenberg CSS, and disable WP Emoji JS.', 'breakdance'); ?></label><br />
                                <label for="disable_bloat__no"><input type="radio" id="disable_bloat__no" name="disable_bloat" value="false" <?= $disable_bloat ? '' : 'checked' ?> /><?php esc_html_e('No', 'breakdance'); ?></label><br />
                            </fieldset>

                            <p class='description'><?php esc_html_e('You can change this at any time from Breakdance → Settings → Performance.', 'breakdance'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('License Key', 'breakdance'); ?></th>
                        <td>
                            <fieldset>
                                <label for="key"><?php esc_html_e('License Key (optional)', 'breakdance'); ?></label><br />
                                <input type="text" id="key" style="width: 360px;" value="<?= strval($key ?? '') ?>" name="key" />
                                <p class='description'><?php
                                    /* translators: %s: Link to customer portal */
                                    printf(esc_html__('If you purchased Breakdance, enter your license key here. You can find your license key at %s.', 'breakdance'), '<a href="https://breakdance.com/portal" target="_blank">https://breakdance.com/portal</a>');
                                ?></p>
                            </fieldset>

                            <?php if ($key_error !== null): ?>
                                <div class="notice notice-error inline padded-notice">
                                    <p><?php
                                        /* translators: %s: Error message */
                                        printf(esc_html__('Failed to activate the key: %s', 'breakdance'), esc_html($key_error));
                                    ?></p>
                                </div>
                            <?php endif; ?>

                        </td>
                    </tr>


                    <tr>
                        <th scope="row"><?php esc_html_e('AnalyticsWP Plugin Integration', 'breakdance'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <label for="analyticswp__yes"><input type="radio" id="analyticswp__yes" name="analyticswp" value="true" <?= $analyticswp ? 'checked' : '' ?> />

                                    <?php esc_html_e('Install AnalyticsWP Plugin', 'breakdance'); ?>
                                    <p class="description"><?php esc_html_e('View the complete customer journey for visitors that submit Breakdance forms, or use the AnalyticsWP Event element to track user activity on a page.', 'breakdance'); ?></p>
                                    <p class="description">
                                        <?php
                                            /* translators: 1: Opening <a> tag for documentation, 2: Closing </a> tag, 3: Opening <a> tag for plugin homepage, 4: Closing </a> tag */
                                            printf(esc_html__('Read the %1$sBreakdance AnalyticsWP integration documentation%2$s or visit the %3$sAnalyticsWP Plugin Homepage%4$s for more information.', 'breakdance'), '<a href="https://breakdance.com/documentation/integrations/analyticswp/" target="_blank">', '</a>', '<a href="https://analyticswp.com/?utm_campaign=breakdance_setup_wizard" target="_blank">', '</a>');
                                        ?>
                                    </p>
                                </label><br />
                                <label for="analyticswp__no"><input type="radio" id="analyticswp__no" name="analyticswp" <?= $analyticswp ? '' : 'checked' ?> value="false" />

                                    <?php esc_html_e('Don\'t Install', 'breakdance'); ?>
                                    <p class="description"><?php esc_html_e('AnalyticsWP will not be automatically installed.', 'breakdance'); ?></p>

                                </label><br />
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e('Improve Breakdance', 'breakdance'); ?></th>
                        <td>
                            <fieldset>
                                <label for="enable_tracking">
                                    <input type="checkbox" id="enable_tracking" name="enable_tracking" value="true" <?= $enable_tracking ? 'checked' : '' ?> />
                                    <?php esc_html_e('Yes, I want to help improve Breakdance', 'breakdance'); ?>
                                </label>
                                <p class="description"><?php
                                    /* translators: 1: Opening <a> tag, 2: Closing </a> tag */
                                    printf(esc_html__('By allowing us to %1$scollect usage data%2$s, you help us make Breakdance better for everyone.', 'breakdance'), '<a href="https://breakdance.com/documentation/settings/privacy/" target="_blank">', '</a>');
                                ?></p>

                            </fieldset>

                            <p class="description"><?php esc_html_e('You can change this at any time from Breakdance → Settings → Privacy', 'breakdance'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Partner Discounts</th>
                        <td>
                            <fieldset>
                                <label for="hide_partner_discounts">
                                    <input type="checkbox" id="hide_partner_discounts" name="hide_partner_discounts" value="true" <?= $hide_partner_discounts ? 'checked' : '' ?> />
                                    Hide the Partner Discounts page from the admin menu
                                </label>
                                <p class="description">
                                    You can re-enable the Partner Discounts page later from <code>Breakdance &rarr; Settings &rarr; Privacy</code>.
                                </p>
                            </fieldset>
                        </td>
                    </tr>

                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Finish Setup', 'breakdance'); ?>" />
            </p>
        </form>
    </div>
<?php
}

?>
