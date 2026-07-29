<?php

namespace Breakdance\Admin\SettingsPage\LicenseTab;

use Breakdance\Licensing\LicenseKeyManager;
use function Breakdance\Licensing\get_option_receive_beta_updates;
use function Breakdance\Licensing\save_option_receive_beta_updates;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\LicenseTab\register');


function admin_notice(string $message, string $type = 'success'): void
{
?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php
}

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(esc_html__('License', 'breakdance'), 'license', '\Breakdance\Admin\SettingsPage\LicenseTab\tab', 1);
}

function tab()
{
    /** @var LicenseKeyManager $license_key_manager */
    $license_key_manager = LicenseKeyManager::getInstance();

    $license_key_manager->refetchStoredLicenseKeyValidityInfo();

    $nonce_action = 'breakdance_admin_license_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (isset($_POST['submit'])) {
            /**
             * @var mixed|null|false $key
             */
            $key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($key !== null && $key !== false) {
                $trimmed_key = trim((string) $key);

                $license_key_manager->changeLicenseKey($trimmed_key === '' ? null : $trimmed_key);
            }

            /** @var boolean|null $receive_beta_updates */
            $receive_beta_updates = filter_input(INPUT_POST, 'receive_beta_updates', FILTER_VALIDATE_BOOLEAN);
            if ($receive_beta_updates !== null) {
                save_option_receive_beta_updates($receive_beta_updates);
            }
        } elseif (isset($_POST['activate_license'])) {
            if (false !== $license_key_manager->activateLicense()) {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice(esc_html__('License was activated', 'breakdance'));
                });
            } else {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice(esc_html__('Failed to activate license', 'breakdance'), 'error');
                });
            }
        } elseif (isset($_POST['deactivate_license'])) {
            // TODO custom notices don't work within settings tabs
            if (false !== $license_key_manager->deactivateLicense()) {
                add_action('admin_notices', function () {
                    admin_notice(esc_html__('License was deactivated', 'breakdance'));
                });
            } else {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice(esc_html__('Failed to deactivate license', 'breakdance'), 'error');
                });
            }
        }
    }

    // load data for use in form
    $stored_key = $license_key_manager->getStoredLicenseKey();
    $license_info = $license_key_manager->getHumanReadableLicenseKeyInformation();
    $show_activate_license_btn = $license_key_manager->canLicenseBeActivated();
    $show_deactivate_license_btn = $license_key_manager->canLicenseBeDeactivated();
?>

    <h2><?php esc_html_e('License', 'breakdance'); ?></h2>
    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="key">
                            <?php esc_html_e('License Key', 'breakdance'); ?>
                        </label>
                    </th>
                    <td>
                        <input type='password' id='key' name='key' style='width: 300px;' value='<?= htmlspecialchars((string) $stored_key); ?>' />

                        <?php if ($show_activate_license_btn) : ?>
                            <input type="submit" class="button-secondary" name="activate_license" value="<?php esc_attr_e('Activate License', 'breakdance'); ?>">
                        <?php endif; ?>

                        <?php if ($show_deactivate_license_btn) : ?>
                            <input type="submit" class="button-secondary" name="deactivate_license" value="<?php esc_attr_e('Deactivate License', 'breakdance'); ?>">
                        <?php endif; ?>

                        <p class="description">
                        <dl>
                            <dt><b><?php esc_html_e('Product', 'breakdance'); ?></b></dt>
                            <dd><?= $license_info['product'] ?></dd>

                            <dt><b><?php esc_html_e('License Key Validity', 'breakdance'); ?></b></dt>
                            <dd><?= $license_info['is_valid'] ?></dd>

                            <dt><b><?php esc_html_e('Activation Status', 'breakdance'); ?></b></dt>
                            <dd><?= $license_info['activation_status'] ?></dd>

                            <dt><b><?php esc_html_e('Expires On', 'breakdance'); ?></b></dt>
                            <dd><?= $license_info['expires'] ?><?= $license_info['expires_in_human_readable']; ?></dd>

                            <!-- <dt><b><?php esc_html_e('Has License Been Paid For', 'breakdance'); ?></b></dt>
                        <dd><?= $license_info['has_license_been_paid_for'] ?></dd> -->
                        </dl>
                        </p>

                        <p class="description">

                            <?php

                            if (BREAKDANCE_MODE === 'breakdance') {
                                ?>
                                    <?php
                                        /* translators: 1: Opening <a> tag for breakdance.com, 2: Closing </a> tag, 3: Opening <a> tag for customer portal, 4: Closing </a> tag */
                                        printf(esc_html__('Visit %1$sbreakdance.com%2$s to purchase a license key. Already purchased? Find your license key in the %3$scustomer portal%4$s.', 'breakdance'), '<a href="https://breakdance.com/" target="_blank">', '</a>', '<a href="https://breakdance.com/portal" target="_blank">', '</a>');
                                    ?>
                                <?php
                            }

                            if (BREAKDANCE_MODE === 'oxygen') {
                                ?>
                                    <?php
                                        /* translators: 1: Opening <a> tag for oxygenbuilder.com, 2: Closing </a> tag, 3: Opening <a> tag for customer portal, 4: Closing </a> tag */
                                        printf(esc_html__('Visit %1$soxygenbuilder.com%2$s to purchase a license key. Already purchased? Find your license key in the %3$scustomer portal%4$s.', 'breakdance'), '<a href="https://oxygenbuilder.com/" target="_blank">', '</a>', '<a href="https://oxygenbuilder.com/portal" target="_blank">', '</a>');
                                    ?>
                                <?php
                            }

                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="key">
                            <?php esc_html_e('Beta Versions', 'breakdance'); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="receive_beta_updates">
                                <input type="hidden" name="receive_beta_updates" value="false">
                                <input type="checkbox" name="receive_beta_updates" id="receive_beta_updates" <?php echo get_option_receive_beta_updates() ? ' checked' : ''; ?> />
                                <span><?php esc_html_e('Receive beta version updates', 'breakdance'); ?></span>
                            </label>
                        </fieldset>

                        <p class="description"><?php esc_html_e('Checking this checkbox will opt you in to receive beta version updates. You can opt out at any time.', 'breakdance'); ?></p>
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
