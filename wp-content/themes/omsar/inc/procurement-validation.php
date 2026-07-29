<?php
/**
 * Procurement ACF Field Validation
 *
 * Validates opening date (publication_custom_date) and closing date (submission_deadline) field values.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Normalize date value and convert to timestamp for comparison.
 *
 * @param mixed $date_value The date value to normalize.
 * @return int|false
 */
function omsar_normalize_procurement_date_for_comparison($date_value) {
    if (empty($date_value)) {
        return false;
    }

    if (is_array($date_value)) {
        $date_value = reset($date_value);
    }

    $date_value = trim((string) $date_value);
    if ($date_value === '') {
        return false;
    }

    $timestamp = false;

    if (is_numeric($date_value) && strlen($date_value) === 8) {
        $date_obj = DateTime::createFromFormat('Ymd', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }

    if ($timestamp === false) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }

    if ($timestamp === false) {
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }

    return $timestamp !== false ? $timestamp : false;
}

/**
 * Get the other procurement date field from POST or database.
 *
 * @param string $field_name Field name.
 * @param int    $post_id    Post ID.
 * @return mixed
 */
function omsar_get_other_procurement_date_value($field_name, $post_id = 0) {
    $date_value = '';

    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $field_value) {
            $field_obj = acf_get_field($field_key);
            if ($field_obj && $field_obj['name'] === $field_name) {
                $date_value = $field_value;
                break;
            }
        }
    }

    if (empty($date_value) && $post_id) {
        $date_value = get_field($field_name, $post_id);
    }

    return $date_value;
}

/**
 * Check if a publication date is after today (site timezone).
 *
 * @param mixed $date_value Raw date value.
 * @return bool
 */
function omsar_procurement_publication_date_is_in_future($date_value) {
    $publication_timestamp = omsar_normalize_procurement_date_for_comparison($date_value);
    if ($publication_timestamp === false) {
        return false;
    }

    $today_timestamp = omsar_normalize_procurement_date_for_comparison(current_time('Y-m-d'));
    if ($today_timestamp === false) {
        return false;
    }

    return $publication_timestamp > $today_timestamp;
}

/**
 * Limit publication date picker to today or earlier in admin.
 *
 * @param array $field ACF field settings.
 * @return array
 */
add_filter('acf/load_field/name=publication_custom_date', 'omsar_procurement_limit_publication_date_field');
function omsar_procurement_limit_publication_date_field($field) {
    if (!is_admin()) {
        return $field;
    }

    $field['max_date'] = current_time('Ymd');
    return $field;
}

/**
 * Restrict publication date calendar selection to today or earlier.
 *
 * @param array $args  Date picker args.
 * @param array $field ACF field settings.
 * @return array
 */
add_filter('acf/fields/date_picker/date_picker_args', 'omsar_procurement_publication_date_picker_args', 10, 2);
function omsar_procurement_publication_date_picker_args($args, $field) {
    if (!is_admin() || !isset($field['name']) || $field['name'] !== 'publication_custom_date') {
        return $args;
    }

    $args['maxDate'] = current_time('Y-m-d');
    return $args;
}

/**
 * Check whether validation should run for procurement notices.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function omsar_should_validate_procurement_dates($post_id = 0) {
    if ($post_id) {
        $post = get_post($post_id);
        return $post && $post->post_type === 'procurement_notices';
    }

    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : (isset($_GET['post_type']) ? $_GET['post_type'] : '');
    return $post_type === 'procurement_notices';
}

add_filter('acf/validate_value/name=publication_custom_date', 'omsar_validate_publication_custom_date', 10, 4);
function omsar_validate_publication_custom_date($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }

    $post_id = isset($field['value']) ? null : acf_get_form_data('post_id');
    if (!$post_id) {
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
    }

    if (!omsar_should_validate_procurement_dates($post_id)) {
        return $valid;
    }

    if (!empty($value)) {
        if (omsar_procurement_publication_date_is_in_future($value)) {
            return __('Opening date cannot be in the future.', 'omsar');
        }

        $submission_deadline_value = omsar_get_other_procurement_date_value('submission_deadline', $post_id);

        if (!empty($submission_deadline_value)) {
            $publication_timestamp = omsar_normalize_procurement_date_for_comparison($value);
            $submission_timestamp = omsar_normalize_procurement_date_for_comparison($submission_deadline_value);

            if ($publication_timestamp !== false && $submission_timestamp !== false && $publication_timestamp >= $submission_timestamp) {
                return __('Opening date must be before closing date.', 'omsar');
            }
        }
    }

    return $valid;
}

add_filter('acf/validate_value/name=submission_deadline', 'omsar_validate_submission_deadline', 10, 4);
function omsar_validate_submission_deadline($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }

    $post_id = isset($field['value']) ? null : acf_get_form_data('post_id');
    if (!$post_id) {
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
    }

    if (!omsar_should_validate_procurement_dates($post_id)) {
        return $valid;
    }

    if (!empty($value)) {
        $publication_date_value = omsar_get_other_procurement_date_value('publication_custom_date', $post_id);

        if (!empty($publication_date_value)) {
            $publication_timestamp = omsar_normalize_procurement_date_for_comparison($publication_date_value);
            $submission_timestamp = omsar_normalize_procurement_date_for_comparison($value);

            if ($publication_timestamp !== false && $submission_timestamp !== false && $submission_timestamp <= $publication_timestamp) {
                return __('Closing date must be after opening date.', 'omsar');
            }
        }
    }

    return $valid;
}

add_action('admin_enqueue_scripts', 'omsar_enqueue_procurement_validation_js');
function omsar_enqueue_procurement_validation_js($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
    $post_type = $post_id ? get_post_type($post_id) : (isset($_GET['post_type']) ? $_GET['post_type'] : 'post');

    if ($post_type !== 'procurement_notices') {
        return;
    }

    $script = "
    jQuery(document).ready(function($) {
        function getTodayTimestamp() {
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            return today.getTime();
        }

        function validatePublicationDateNotFuture() {
            var publicationDateField = $('[data-name=\"publication_custom_date\"]');

            if (publicationDateField.length === 0) {
                $('.acf-field').each(function() {
                    var labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.indexOf('opening') !== -1 && labelText.indexOf('date') !== -1) {
                        publicationDateField = $(this).find('input, select');
                        return false;
                    }
                    if (labelText.indexOf('publication') !== -1 && labelText.indexOf('date') !== -1) {
                        publicationDateField = $(this).find('input, select');
                        return false;
                    }
                });
            }

            var publicationDate = publicationDateField.length > 0 ? publicationDateField.val() : '';

            publicationDateField.closest('.acf-field').find('.acf-date-future-error').remove();
            publicationDateField.closest('.acf-field').removeClass('acf-error');

            if (publicationDate) {
                var publicationTimestamp = parseProcurementDateToTimestamp(publicationDate);
                var todayTimestamp = getTodayTimestamp();

                if (publicationTimestamp && publicationTimestamp > todayTimestamp) {
                    var errorMsg = '<span class=\"acf-date-future-error\" style=\"color: #dc3232; display: block; margin-top: 5px; font-size: 12px;\">Opening date cannot be in the future.</span>';
                    publicationDateField.closest('.acf-field').addClass('acf-error');
                    publicationDateField.closest('.acf-field').find('.acf-input').append(errorMsg);
                }
            }
        }

        function validateProcurementDateOrder() {
            var publicationDateField = $('[data-name=\"publication_custom_date\"]');
            var submissionDeadlineField = $('[data-name=\"submission_deadline\"]');

            if (publicationDateField.length === 0) {
                $('.acf-field').each(function() {
                    var labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.indexOf('opening') !== -1 && labelText.indexOf('date') !== -1) {
                        publicationDateField = $(this).find('input, select');
                        return false;
                    }
                    if (labelText.indexOf('publication') !== -1 && labelText.indexOf('date') !== -1) {
                        publicationDateField = $(this).find('input, select');
                        return false;
                    }
                });
            }

            if (submissionDeadlineField.length === 0) {
                $('.acf-field').each(function() {
                    var labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.indexOf('closing') !== -1 && labelText.indexOf('date') !== -1) {
                        submissionDeadlineField = $(this).find('input, select');
                        return false;
                    }
                    if (labelText.indexOf('submission') !== -1 && (labelText.indexOf('deadline') !== -1 || labelText.indexOf('date') !== -1)) {
                        submissionDeadlineField = $(this).find('input, select');
                        return false;
                    }
                });
            }

            var publicationDate = publicationDateField.length > 0 ? publicationDateField.val() : '';
            var submissionDeadline = submissionDeadlineField.length > 0 ? submissionDeadlineField.val() : '';

            publicationDateField.closest('.acf-field').find('.acf-date-order-error').remove();
            submissionDeadlineField.closest('.acf-field').find('.acf-date-order-error').remove();
            publicationDateField.closest('.acf-field').removeClass('acf-error');
            submissionDeadlineField.closest('.acf-field').removeClass('acf-error');

            if (publicationDate && submissionDeadline) {
                var publicationTimestamp = parseProcurementDateToTimestamp(publicationDate);
                var submissionTimestamp = parseProcurementDateToTimestamp(submissionDeadline);

                if (publicationTimestamp && submissionTimestamp && submissionTimestamp <= publicationTimestamp) {
                    var errorMsg = '<span class=\"acf-date-order-error\" style=\"color: #dc3232; display: block; margin-top: 5px; font-size: 12px;\">Closing date must be after opening date.</span>';
                    submissionDeadlineField.closest('.acf-field').addClass('acf-error');
                    submissionDeadlineField.closest('.acf-field').find('.acf-input').append(errorMsg);

                    var errorMsg2 = '<span class=\"acf-date-order-error\" style=\"color: #dc3232; display: block; margin-top: 5px; font-size: 12px;\">Opening date must be before closing date.</span>';
                    publicationDateField.closest('.acf-field').addClass('acf-error');
                    publicationDateField.closest('.acf-field').find('.acf-input').append(errorMsg2);
                }
            }
        }

        function parseProcurementDateToTimestamp(dateStr) {
            if (!dateStr) return null;

            if (/^\\d{8}$/.test(dateStr)) {
                var year = dateStr.substring(0, 4);
                var month = dateStr.substring(4, 6);
                var day = dateStr.substring(6, 8);
                var date = new Date(year, month - 1, day);
                if (!isNaN(date.getTime())) {
                    return date.getTime();
                }
            }

            if (/^\\d{4}-\\d{2}-\\d{2}$/.test(dateStr)) {
                var parts = dateStr.split('-');
                var date = new Date(parts[0], parts[1] - 1, parts[2]);
                if (!isNaN(date.getTime())) {
                    return date.getTime();
                }
            }

            if (/^\\d{2}\\/\\d{2}\\/\\d{4}$/.test(dateStr)) {
                var parts = dateStr.split('/');
                var date = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(date.getTime())) {
                    return date.getTime();
                }
            }

            var date = new Date(dateStr);
            if (!isNaN(date.getTime())) {
                return date.getTime();
            }

            return null;
        }

        function runProcurementDateValidation() {
            validatePublicationDateNotFuture();
            validateProcurementDateOrder();
        }

        runProcurementDateValidation();
        setTimeout(runProcurementDateValidation, 500);
        setTimeout(runProcurementDateValidation, 1000);

        $(document).on('change', '[data-name=\"publication_custom_date\"], [data-name=\"submission_deadline\"]', runProcurementDateValidation);

        if (typeof acf !== 'undefined') {
            acf.addAction('ready_field/type=date_picker', function(field) {
                if (field.get('name') === 'publication_custom_date') {
                    var picker = field.datePicker && field.datePicker();
                    if (picker && picker.datepicker) {
                        picker.datepicker('option', 'maxDate', 0);
                    }
                    runProcurementDateValidation();
                }

                if (field.get('name') === 'submission_deadline') {
                    runProcurementDateValidation();
                }
            });

            acf.addAction('change', function(field) {
                if (field.get('name') === 'publication_custom_date' || field.get('name') === 'submission_deadline') {
                    setTimeout(runProcurementDateValidation, 100);
                }
            });
        }
    });
    ";

    wp_add_inline_script('jquery', $script);
}
