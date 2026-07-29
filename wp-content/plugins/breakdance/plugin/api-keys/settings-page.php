<?php

namespace Breakdance\APIKeys;

use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', function () {
    if (count(APIKeysController::getInstance()->apiKeys) > 0) {
        \Breakdance\Admin\SettingsPage\addTab(esc_html__('API Keys', 'breakdance'), "api_keys", '\Breakdance\APIKeys\tab', 1100);
    }
});

function tab()
{
    $nonce_action = 'breakdance_admin_api-keys_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        /** @var string[] $keys */
        $keys = filter_input(INPUT_POST, 'apiKeys', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        if (!$keys) {
            return;
        }

        if (isset($keys[BREAKDANCE_FACEBOOK_APP_ID_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_FACEBOOK_APP_ID_NAME],
                \Breakdance\APIKeys\validateFacebookAppId($keys[BREAKDANCE_FACEBOOK_APP_ID_NAME]),
                'Facebook app ID'
            );
        }

        if (isset($keys[BREAKDANCE_DISCORD_WEBHOOK_URL_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_DISCORD_WEBHOOK_URL_NAME],
                \Breakdance\Forms\Actions\Discord::validateApiKey($keys[BREAKDANCE_DISCORD_WEBHOOK_URL_NAME]),
                'Discord'
            );
        }

        if (isset($keys[BREAKDANCE_SLACK_WEBHOOK_URL_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_SLACK_WEBHOOK_URL_NAME],
                \Breakdance\Forms\Actions\Slack::validateApiKey($keys[BREAKDANCE_SLACK_WEBHOOK_URL_NAME]),
                'Discord'
            );
        }

        if (isset($keys[BREAKDANCE_MAILCHIMP_API_KEY_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_MAILCHIMP_API_KEY_NAME],
                \Breakdance\Forms\Actions\MailChimp::validateApiKey($keys[BREAKDANCE_MAILCHIMP_API_KEY_NAME]),
                'MailChimp'
            );
        }

        if (isset($keys[BREAKDANCE_GETRESPONSE_API_KEY_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_GETRESPONSE_API_KEY_NAME],
                \Breakdance\Forms\Actions\GetResponse::validateApiKey($keys[BREAKDANCE_GETRESPONSE_API_KEY_NAME]),
                "GetResponse"
            );
        }

        if (isset($keys[BREAKDANCE_CONVERTKIT_API_KEY_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_CONVERTKIT_API_KEY_NAME],
                \Breakdance\Forms\Actions\ConvertKit::validateApiKey($keys[BREAKDANCE_CONVERTKIT_API_KEY_NAME]),
                'ConvertKit'
            );
        }

        if (isset($keys[BREAKDANCE_MAILERLITE_API_KEY_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_MAILERLITE_API_KEY_NAME],
                \Breakdance\Forms\Actions\MailerLite::validateApiKey($keys[BREAKDANCE_MAILERLITE_API_KEY_NAME]),
                'MailerLite'
            );
        }

        if (isset($keys[BREAKDANCE_DRIP_API_KEY_NAME])) {
            showFormApiKeyErrorIfInvalid(
                $keys[BREAKDANCE_DRIP_API_KEY_NAME],
                \Breakdance\Forms\Actions\Drip::validateApiKey($keys[BREAKDANCE_DRIP_API_KEY_NAME]),
                "Drip"
            );
        }


        showFormApiKeyErrorIfInvalid(
            $keys[BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME] ?? null,
            \Breakdance\Forms\Actions\ActiveCampaign::validateApiKey(
                [
                    'apiKey' => $keys[BREAKDANCE_ACTIVECAMPAIGN_API_KEY_NAME] ?? null,
                    'apiUrl' => $keys[BREAKDANCE_ACTIVECAMPAIGN_URL_NAME] ?? null
                ]
            ),
            "Drip"
        );

        \Breakdance\APIKeys\setAllKeys($keys);
    }

    $instance = \Breakdance\APIKeys\APIKeysController::getInstance();
?>
    <h2><?php esc_html_e('API Keys', 'breakdance'); ?></h2>
    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <table class="form-table">
            <tbody>
                <?php
                foreach ($instance->apiKeys as $apiKey) :
                    $name = "apiKeys[{$apiKey['slug']}]";
                    $value = \Breakdance\APIKeys\getKey($apiKey['slug']);
                ?>
                    <tr>
                        <th><label for="<?php echo $name; ?>"><?php echo esc_html($apiKey['name']); ?></label></th>
                        <td>
                            <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr($value); ?>" class="large-text" spellcheck="false">

                            <?php if (isset($apiKey['description'])) { ?>
                                <p class="description">
                                    <?php echo esc_html($apiKey['description']); ?>
                                </p>
                            <?php } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'breakdance'); ?>">
        </p>
    </form>
<?php
}

/**
 * @param ?string $key
 * @param ActionSuccess|ActionError $validationPing
 * @param string $integrationName
 */
function showFormApiKeyErrorIfInvalid($key, $validationPing, $integrationName)
{
    if (!isset($key) || !strlen($key) > 0) {
        return;
    }

    if ($validationPing['type'] !== 'success') {
        /* translators: %s: Integration name (e.g., "Mailchimp", "Stripe") */
        $errorMessage = sprintf(esc_html__('Invalid %s API Key', 'breakdance'), esc_html($integrationName));
        echo '<div class="notice notice-error is-dismissible"><p>' . $errorMessage . '</p></div>';
    }
}
