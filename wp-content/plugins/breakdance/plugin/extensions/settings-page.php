<?php

namespace Breakdance\Extensions;

use function Breakdance\Util\is_post_request;

add_action(
    'breakdance_register_admin_settings_page_register_tabs',
    '\Breakdance\Extensions\register'
);

function register()
{
    if (BREAKDANCE_MODE !== 'oxygen') return;

    \Breakdance\Admin\SettingsPage\addTab(
        esc_html__('Extensions', 'breakdance'),
        'extensions',
        '\Breakdance\Extensions\tab',
        5000
    );
}

function updateSettings()
{
    $extensions = ExtensionsController::getExtensions();
    /** @var array<string, string> $keys */
    $keys = filter_input(INPUT_POST, 'license_keys', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!$keys) {
      return;
    }

    foreach ($extensions as $extension) {
        $licenseKey = $keys[$extension->slug] ?? '';

        if (!empty($licenseKey) && !str_contains($licenseKey, '*')) {
          $extension->setLicenseKey($licenseKey);
        } else if (empty($licenseKey)) {
          $extension->setLicenseKey('');
        }
    }
}

function tab()
{
    $nonce_action = 'breakdance_admin_extensions';
    $extensions = ExtensionsController::getExtensions();

    if (is_post_request() && check_admin_referer($nonce_action)) {
        updateSettings();
    }
    ?>
    <h2><?php esc_html_e('Extensions', 'breakdance'); ?></h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <table class="form-table" role="presentation">
            <tbody>
            <?php
            foreach($extensions as $extension) {
              $extension->fetchLicenseKeyStatus();
              $licenseKeyStatus = $extension->getLicenseStatus();
            ?>
            <tr>
                <th scope="row">
                    <?php
                        /* translators: %s: Extension name */
                        printf(esc_html__('%s - License Key', 'breakdance'), esc_html($extension->name));
                    ?>
                </th>

                <td>
                    <input name="license_keys[<?php echo $extension->slug; ?>]" type="password" id="<?php echo $extension->slug; ?>_license_key" value="<?php echo esc_attr($extension->getMaskedLicenseKey()); ?>" class="large-text" autocomplete="off" />

                    <?php
                    // TODO: I18N
                    $messages = [
                        'valid' => sprintf(
                            /* translators: %s: status text wrapped in styled HTML */
                            __('Your license key is %s.', 'breakdance'),
                            '<b style="color: #46B450">' . __('active', 'breakdance') . '</b>'
                        ),
                        'invalid' => sprintf(
                            /* translators: %s: status text wrapped in styled HTML */
                            __('Your license key is %s.', 'breakdance'),
                            '<b style="color: #DC3232">' . __('invalid', 'breakdance') . '</b>'
                        ),
                        'inactive' => sprintf(
                            /* translators: %s: status text wrapped in styled HTML */
                            __('Your license key is %s.', 'breakdance'),
                            '<b style="color: #DC3232">' . __('inactive', 'breakdance') . '</b>'
                        ),
                        'expired' => sprintf(
                            /* translators: %s: status text wrapped in styled HTML */
                            __('Your license key is %s.', 'breakdance'),
                            '<b style="color: #DC3232">' . __('expired', 'breakdance') . '</b>'
                        ),
                    ];

                    if (isset($messages[$licenseKeyStatus])) : ?>
                        <p class="description"><?php echo wp_kses_post($messages[$licenseKeyStatus]); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>

            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>">
        </p>
    </form>
    <?php
}
