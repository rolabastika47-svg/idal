<?php

/**
 * @psalm-ignore-file
 */

namespace Breakdance\Singularity;

// Disable cache during development
define('FUTURELAYER_DISABLE_LIBRARY_CACHE', true);

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_design_library_v2',
        'Breakdance\Singularity\getDesignLibraryV2',
        'none',
        true,
        [
            'args' => [
                'library_id' => FILTER_VALIDATE_INT
            ]
        ]
    );
});

/**
 * Fetches the design library from FutureLayer app
 * Uses site_secret_key or anonymous_jwt for authentication
 * Caches the result for 24 hours
 *
 * @param int $library_id The design library ID from SiteStructure.config.libraryId
 * @return array Design library data or error
 */
function getDesignLibraryV2($library_id)
{
    // Library ID is required
    if (empty($library_id)) {
        return ['error' => 'Library ID is required (from SiteStructure.config.libraryId)'];
    }

    // Check if caching is disabled (for development)
    $cache_disabled = defined('FUTURELAYER_DISABLE_LIBRARY_CACHE') && FUTURELAYER_DISABLE_LIBRARY_CACHE;

    // Check cache first (unless disabled) - cache per library ID
    $cache_key = 'futurelayer_design_library_v2_' . $library_id;

    if (!$cache_disabled) {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
    }

    // Get FutureLayer app URL
    if (!function_exists('futurelayer_get_app_url_for_backend')) {
        return ['error' => 'FutureLayer plugin not active'];
    }

    // Get authentication (site_secret_key or anonymous_jwt)
    $auth = null;

    if (function_exists('futurelayer_get_site_secret_key')) {
        $site_secret_key = futurelayer_get_site_secret_key();
        if ($site_secret_key) {
            $auth = $site_secret_key;
        }
    }

    if (!$auth && function_exists('futurelayer_get_anonymous_jwt')) {
        $anonymous_jwt = futurelayer_get_anonymous_jwt();
        if ($anonymous_jwt) {
            $auth = $anonymous_jwt;
        }
    }

    if (!$auth) {
        return ['error' => 'No authentication available (site not connected and no anonymous JWT)'];
    }

    // Get library data
    $library_response = wp_remote_get(
        futurelayer_get_app_url_for_backend() . '/api/external/design-library/' . $library_id,
        [
            'headers' => [
                'Authorization' => 'Bearer ' . $auth,
                'Accept' => 'application/json'
            ],
            'timeout' => 30
        ]
    );

    if (is_wp_error($library_response)) {
        /** @var \WP_Error $library_response */
        return ['error' => 'Failed to get library data: ' . $library_response->get_error_message()];
    }

    $status_code = wp_remote_retrieve_response_code($library_response);
    if ($status_code !== 200) {
        $body = wp_remote_retrieve_body($library_response);
        return ['error' => "Failed to get library data (status $status_code): $body"];
    }

    $library_body = wp_remote_retrieve_body($library_response);
    $library_data = json_decode($library_body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid JSON in library response'];
    }

    // Cache for 24 hours (unless caching is disabled)
    if (!$cache_disabled) {
        /**
         * @psalm-suppress UndefinedConstant
         */
        set_transient($cache_key, $library_data, 24 * HOUR_IN_SECONDS);
    }

    return $library_data;
}
