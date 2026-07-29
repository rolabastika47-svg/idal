<?php
/**
 * Recruitment Status Management
 *
 * Derives recruitment status from opening_date and closing_date ACF fields.
 * Sends email notifications when status changes to open (via save or cron).
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Store post IDs that need notification after save
 */
$omsar_pending_notifications = array();

/**
 * Check whether a recruitment date value is empty.
 *
 * @param mixed $date_value Raw date value.
 * @return bool
 */
function omsar_recruitment_date_is_empty($date_value) {
    if ($date_value === null || $date_value === false || $date_value === '') {
        return true;
    }

    if (is_array($date_value)) {
        $date_value = reset($date_value);
    }

    return trim((string) $date_value) === '';
}

/**
 * Parse a recruitment date value into a DateTime (midnight).
 *
 * @param mixed $date_value Raw date value.
 * @return DateTime|null
 */
function omsar_parse_recruitment_date_to_datetime($date_value) {
    if (omsar_recruitment_date_is_empty($date_value)) {
        return null;
    }

    if (is_array($date_value)) {
        $date_value = reset($date_value);
    }

    $date_value = trim((string) $date_value);
    $date_obj   = null;

    if (is_numeric($date_value) && strlen($date_value) === 8) {
        $date_obj = DateTime::createFromFormat('Ymd', $date_value);
    } else {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_value);
        if (!$date_obj) {
            $date_obj = DateTime::createFromFormat('d/m/Y', $date_value);
        }
    }

    if ($date_obj) {
        $date_obj->setTime(0, 0, 0);
    }

    return $date_obj ?: null;
}

/**
 * Compute recruitment status from opening and closing date values.
 *
 * Rules:
 * - Both dates empty → upcoming
 * - Today before opening date → upcoming
 * - Today after closing date → closed
 * - Otherwise → open
 *
 * @param mixed $opening_date Opening date value.
 * @param mixed $closing_date Closing date value.
 * @return string open|closed|upcoming
 */
function omsar_compute_recruitment_status_from_date_values($opening_date = null, $closing_date = null) {
    if (omsar_recruitment_date_is_empty($opening_date) && omsar_recruitment_date_is_empty($closing_date)) {
        return 'upcoming';
    }

    $today = new DateTime();
    $today->setTime(0, 0, 0);

    $opening_date_obj = omsar_parse_recruitment_date_to_datetime($opening_date);
    $closing_date_obj = omsar_parse_recruitment_date_to_datetime($closing_date);

    if ($opening_date_obj && $today < $opening_date_obj) {
        return 'upcoming';
    }

    if ($closing_date_obj && $today > $closing_date_obj) {
        return 'closed';
    }

    return 'open';
}

/**
 * Get recruitment status for a post from its date fields.
 *
 * @param int $recruitment_id Recruitment post ID.
 * @return string open|closed|upcoming
 */
function omsar_get_recruitment_status_from_acf($recruitment_id) {
    if (!$recruitment_id) {
        return 'upcoming';
    }

    $opening_date = function_exists('get_field') ? get_field('opening_date', $recruitment_id) : get_post_meta($recruitment_id, 'opening_date', true);
    $closing_date = function_exists('get_field') ? get_field('closing_date', $recruitment_id) : get_post_meta($recruitment_id, 'closing_date', true);

    return omsar_compute_recruitment_status_from_date_values($opening_date, $closing_date);
}

add_filter('acf/update_value/name=opening_date', 'omsar_check_recruitment_dates_status_change', 10, 3);
add_filter('acf/update_value/name=closing_date', 'omsar_check_recruitment_dates_status_change', 10, 3);

/**
 * Queue notifications when date changes cause status to become open.
 *
 * @param mixed $value  New field value.
 * @param int   $post_id Post ID.
 * @param array $field  ACF field array.
 * @return mixed
 */
function omsar_check_recruitment_dates_status_change($value, $post_id, $field) {
    global $omsar_pending_notifications;

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'recruitments') {
        return $value;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $value;
    }
    if (wp_is_post_revision($post_id)) {
        return $value;
    }
    if ($post->post_status !== 'publish') {
        return $value;
    }

    $field_name = isset($field['name']) ? $field['name'] : '';

    $old_opening = get_field('opening_date', $post_id);
    $old_closing = get_field('closing_date', $post_id);

    $new_opening = ($field_name === 'opening_date') ? $value : $old_opening;
    $new_closing = ($field_name === 'closing_date') ? $value : $old_closing;

    $previous_status = omsar_compute_recruitment_status_from_date_values($old_opening, $old_closing);
    $new_status      = omsar_compute_recruitment_status_from_date_values($new_opening, $new_closing);

    if ($new_status === 'open' && $previous_status !== 'open' && !in_array($post_id, $omsar_pending_notifications, true)) {
        $omsar_pending_notifications[] = $post_id;
    }

    return $value;
}

add_action('acf/save_post', 'omsar_send_pending_recruitment_notifications', 20);

/**
 * Send pending recruitment notifications after post is saved.
 *
 * @param int $post_id Post ID.
 */
function omsar_send_pending_recruitment_notifications($post_id) {
    global $omsar_pending_notifications;

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'recruitments') {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if ($post->post_status !== 'publish') {
        return;
    }

    if (!in_array($post_id, $omsar_pending_notifications, true)) {
        return;
    }

    $current_status = omsar_get_recruitment_status_from_acf($post_id);
    if ($current_status !== 'open') {
        $omsar_pending_notifications = array_values(array_diff($omsar_pending_notifications, array($post_id)));
        return;
    }

    $omsar_pending_notifications = array_values(array_diff($omsar_pending_notifications, array($post_id)));

    if (function_exists('omsar_notify_recruitment_subscribers')) {
        omsar_notify_recruitment_subscribers($post_id);
    }
}

/**
 * Status is computed from dates and is not stored in post meta.
 * Database-level filtering by status is not supported; use PHP filtering instead.
 *
 * @param string $status Status filter value.
 * @return array
 */
function omsar_get_recruitment_status_meta_query($status = 'all') {
    return array();
}
