<?php

namespace Breakdance\Setup;

use function Breakdance\BreakdanceOxygen\Strings\__bdox;
use function Breakdance\Util\validateUrl;
use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;

/**
 * Replace old URL with the new URL in all Breakdance pages.
 * @param string $from
 * @param string $to
 * @return array{postMeta: int, preferences: bool}|\WP_Error
 */
function replaceUrls($from, $to)
{
    $errors = new \WP_Error();
    /** @var \wpdb $wpdb */
    global $wpdb;

    $from = trim($from);
    $to = trim($to);

    $fromEsc = str_replace('/', '\\\/', $from);
    $toEsc = str_replace('/', '\\\/', $to);

    if ($from === $to) {
        $errors->add('same_urls', __('The URLs must be different.', 'breakdance'));
    }

    if (!validateUrl($from)) {
        $errors->add('invalid_from', __('The `From` URL is invalid.', 'breakdance'));
    }

    if (!validateUrl($to)) {
        $errors->add('invalid_to', __('The `To` URL is invalid.', 'breakdance'));
    }

    if ($errors->has_errors()) {
        return $errors;
    }

    /**
     * We cannot use $wpdb->prepare because it removes backslashes.
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress MixedMethodCall
     * @var int|false
     */
    $rowsAffected = $wpdb->query(
        "UPDATE {$wpdb->postmeta} " .
            "SET `meta_value` = REPLACE(`meta_value`, '" . $fromEsc . "', '" . $toEsc . "') " .
            "WHERE `meta_key` = '" . __bdox('_meta_prefix') . "data'"
    );

    // Using global options to get and set the values because we may regenerate the font files after this
    // and using "query" seems to cache it or something, or take some time before taking effect and the regeneration doesn't always work
    /** @var string|false $preferences */
    $preferences = get_global_option('preferences');
    $preferencesAffected = false;

    if ($preferences) {
        $preferencesUpdated = str_replace($from, $to, $preferences);
        $preferencesAffected = $preferences !== $preferencesUpdated;

        if ($preferencesAffected) {
            set_global_option('preferences', $preferencesUpdated);
        }
    }

    if ($rowsAffected === false) {
        $errors->add('unknown', __('Failed to replace URLs in post data', 'breakdance'));
        return $errors;
    }

    /**
     * @psalm-suppress TooManyArguments
     * @var int|false
     */
    $rowsAffected += (int) apply_filters('breakdance-replace-urls', 0, $from, $to);

    return ['postMeta' => $rowsAffected, 'preferences' => $preferencesAffected];
}

// Listen for changes in the `siteurl` option.
add_action(
    'update_option_siteurl',
    /**
     * @param string $from
     * @param string $to
     * @return void
     */
    function ($from, $to) {
        set_transient('breakdance-old-site-url', $from);
        set_transient('breakdance-new-site-url', $to);
    },
    10,
    2
);

function replaceUrlAdminNotice()
{
    $screen = get_current_screen();
    /** @var string */
    $from = get_transient('breakdance-old-site-url');
    /** @var string */
    $to = get_transient('breakdance-new-site-url');

    if (!$screen || $screen->id != 'options-general' || !$to) return;

    delete_transient('breakdance-old-site-url');
    delete_transient('breakdance-new-site-url');

?>
    <div class="notice notice-warning is-dismissible">
        <h2 class="notice-title">Breakdance</h2>
        <p><?php esc_html_e('We detected you\'ve changed the site URL.', 'breakdance'); ?></p>
        <p><?php esc_html_e('Old URL:', 'breakdance'); ?> <code><?php echo esc_html($from); ?></code></p>
        <p><?php esc_html_e('New URL:', 'breakdance'); ?> <code><?php echo esc_html($to); ?></code></p>

        <p><?php esc_html_e('Would you like to run the Replace URL Tool now?', 'breakdance'); ?></p>
        <p>
            <a class="button" href="<?php echo esc_url(get_admin_url() . 'admin.php?page=' . __bdox('admin_page_settings_slug') . '&tab=tools&from=' . urlencode($from) . '&to=' . urlencode($to)); ?>">
                <?php esc_html_e('Yes, Take Me There', 'breakdance'); ?>
            </a>
        </p>
    </div>
<?php
}
add_action('admin_notices', 'Breakdance\Setup\replaceUrlAdminNotice');
