<?php
/**
 * Procurement Status Management
 *
 * Derives procurement notice status from publication_custom_date, submission_deadline,
 * and the is_cancelled ACF field.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Normalize is_cancelled to boolean.
 *
 * @param mixed $is_cancelled Raw field value.
 * @return bool
 */
function omsar_procurement_is_cancelled($is_cancelled) {
    if (is_array($is_cancelled)) {
        $is_cancelled = reset($is_cancelled);
    }

    return filter_var($is_cancelled, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Compute procurement status from field values.
 *
 * Rules:
 * - is_cancelled true → cancelled
 * - Both dates empty → closed
 * - Today before publication date → closed
 * - Today after submission deadline → closed
 * - Otherwise → open
 *
 * @param mixed $publication_date   Publication / opening date.
 * @param mixed $submission_deadline Submission deadline / closing date.
 * @param mixed $is_cancelled       Cancelled flag.
 * @return string open|closed|cancelled
 */
function omsar_compute_procurement_status_from_values($publication_date = null, $submission_deadline = null, $is_cancelled = false) {
    if (omsar_procurement_is_cancelled($is_cancelled)) {
        return 'cancelled';
    }

    $date_is_empty = function_exists('omsar_recruitment_date_is_empty')
        ? 'omsar_recruitment_date_is_empty'
        : null;
    $parse_date = function_exists('omsar_parse_recruitment_date_to_datetime')
        ? 'omsar_parse_recruitment_date_to_datetime'
        : null;

    $pub_empty = $date_is_empty ? $date_is_empty($publication_date) : empty($publication_date);
    $deadline_empty = $date_is_empty ? $date_is_empty($submission_deadline) : empty($submission_deadline);

    if ($pub_empty && $deadline_empty) {
        return 'closed';
    }

    $today = new DateTime();
    $today->setTime(0, 0, 0);

    $pub_obj = $parse_date ? $parse_date($publication_date) : null;
    $deadline_obj = $parse_date ? $parse_date($submission_deadline) : null;

    if ($pub_obj && $today < $pub_obj) {
        return 'closed';
    }

    if ($deadline_obj && $today > $deadline_obj) {
        return 'closed';
    }

    return 'open';
}

/**
 * Get procurement status for a post.
 *
 * @param int $procurement_id Post ID.
 * @return string open|closed|cancelled
 */
function omsar_get_procurement_status($procurement_id) {
    if (!$procurement_id) {
        return 'closed';
    }

    if (function_exists('get_field')) {
        $publication_date    = get_field('publication_custom_date', $procurement_id);
        $submission_deadline = get_field('submission_deadline', $procurement_id);
        $is_cancelled        = get_field('is_cancelled', $procurement_id);
    } else {
        $publication_date    = get_post_meta($procurement_id, 'publication_custom_date', true);
        $submission_deadline = get_post_meta($procurement_id, 'submission_deadline', true);
        $is_cancelled        = get_post_meta($procurement_id, 'is_cancelled', true);
    }

    return omsar_compute_procurement_status_from_values($publication_date, $submission_deadline, $is_cancelled);
}

/**
 * Backward-compatible helper — accepts a post ID or raw field values.
 *
 * @param mixed $procurement_id_or_publication Post ID or publication date.
 * @param mixed $submission_deadline           Submission deadline when passing raw dates.
 * @param mixed $is_cancelled                  Cancelled flag when passing raw dates.
 * @return string open|closed|cancelled
 */
function omsar_calculate_procurement_status($procurement_id_or_publication = null, $submission_deadline = null, $is_cancelled = null) {
    if ($procurement_id_or_publication && is_numeric($procurement_id_or_publication)) {
        $post = get_post((int) $procurement_id_or_publication);
        if ($post && $post->post_type === 'procurement_notices') {
            return omsar_get_procurement_status((int) $procurement_id_or_publication);
        }
    }

    return omsar_compute_procurement_status_from_values(
        $procurement_id_or_publication,
        $submission_deadline,
        $is_cancelled
    );
}

/**
 * Parse publication_custom_date to timestamp for sorting.
 *
 * @param mixed $publication_custom_date Raw date value.
 * @return int
 */
function omsar_get_procurement_publication_timestamp($publication_custom_date) {
    if (function_exists('omsar_parse_recruitment_date_to_datetime')) {
        $date_obj = omsar_parse_recruitment_date_to_datetime($publication_custom_date);
        return $date_obj ? $date_obj->getTimestamp() : 0;
    }

    if (empty($publication_custom_date)) {
        return 0;
    }

    $publication_date_str = trim((string) $publication_custom_date);

    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $publication_date_str)) {
        $date_obj = DateTime::createFromFormat('d/m/Y', $publication_date_str);
        return ($date_obj !== false) ? $date_obj->getTimestamp() : 0;
    }

    if (is_numeric($publication_custom_date) && strlen($publication_date_str) === 8) {
        $date_obj = DateTime::createFromFormat('Ymd', $publication_date_str);
        return ($date_obj !== false) ? $date_obj->getTimestamp() : 0;
    }

    if (is_numeric($publication_custom_date)) {
        return (int) $publication_custom_date;
    }

    $timestamp = strtotime($publication_custom_date);
    return ($timestamp !== false) ? $timestamp : 0;
}
