<?php
/**
 * Recruitment ACF Field Validation
 *
 * Validates opening_date and closing_date field values for recruitments.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Normalize date value and convert to timestamp for comparison.
 * Handles multiple date formats: Y-m-d, Ymd (numeric), d/m/Y
 *
 * @param mixed $date_value The date value to normalize.
 * @return int|false Timestamp if valid date, false otherwise.
 */
function omsar_normalize_date_for_comparison($date_value) {
    if (empty($date_value)) {
        return false;
    }
    
    // Handle array format
    if (is_array($date_value)) {
        $date_value = reset($date_value);
    }
    
    // Convert to string
    $date_value = trim((string) $date_value);
    
    if (empty($date_value)) {
        return false;
    }
    
    // Try different date formats
    $timestamp = false;
    
    // Format 1: Ymd (numeric, e.g., 20251231)
    if (is_numeric($date_value) && strlen($date_value) == 8) {
        $date_obj = DateTime::createFromFormat('Ymd', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }
    
    // Format 2: Y-m-d (e.g., 2025-12-31)
    if ($timestamp === false) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }
    
    // Format 3: d/m/Y (e.g., 31/12/2025)
    if ($timestamp === false) {
        $date_obj = DateTime::createFromFormat('d/m/Y', $date_value);
        if ($date_obj) {
            $timestamp = $date_obj->getTimestamp();
        }
    }
    
    return $timestamp !== false ? $timestamp : false;
}

/**
 * Helper function to get the other date field value from POST data or database
 * 
 * @param string $field_name The field name to get ('opening_date' or 'closing_date')
 * @param int $post_id The post ID
 * @return mixed The date value or empty string
 */
function omsar_get_other_date_value($field_name, $post_id = 0) {
    $date_value = '';
    
    // Method 1: Get from $_POST['acf'] array (current form submission)
    if (isset($_POST['acf']) && is_array($_POST['acf'])) {
        foreach ($_POST['acf'] as $field_key => $field_value) {
            $field_obj = acf_get_field($field_key);
            if ($field_obj && $field_obj['name'] === $field_name) {
                $date_value = $field_value;
                break;
            }
        }
    }
    
    // Method 2: Get from database if not in POST (for existing posts)
    if (empty($date_value) && $post_id) {
        $date_value = get_field($field_name, $post_id);
    }
    
    return $date_value;
}

/**
 * Check whether validation should run for the current recruitments form.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function omsar_should_validate_recruitment_dates($post_id = 0) {
    if ($post_id) {
        $post = get_post($post_id);
        return $post && $post->post_type === 'recruitments';
    }

    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : (isset($_GET['post_type']) ? $_GET['post_type'] : '');
    return $post_type === 'recruitments';
}

add_filter('acf/validate_value/name=opening_date', 'omsar_validate_opening_date', 10, 4);
function omsar_validate_opening_date($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }

    $post_id = isset($field['value']) ? null : acf_get_form_data('post_id');
    if (!$post_id) {
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
    }

    if (!omsar_should_validate_recruitment_dates($post_id)) {
        return $valid;
    }

    if (!empty($value)) {
        $closing_date_value = omsar_get_other_date_value('closing_date', $post_id);

        if (!empty($closing_date_value)) {
            $opening_timestamp = omsar_normalize_date_for_comparison($value);
            $closing_timestamp = omsar_normalize_date_for_comparison($closing_date_value);

            if ($opening_timestamp !== false && $closing_timestamp !== false && $opening_timestamp >= $closing_timestamp) {
                return __('Opening date must be before closing date.', 'omsar');
            }
        }
    }

    return $valid;
}

add_filter('acf/validate_value/name=closing_date', 'omsar_validate_closing_date', 10, 4);
function omsar_validate_closing_date($valid, $value, $field, $input) {
    if ($valid !== true) {
        return $valid;
    }

    $post_id = isset($field['value']) ? null : acf_get_form_data('post_id');
    if (!$post_id) {
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
    }

    if (!omsar_should_validate_recruitment_dates($post_id)) {
        return $valid;
    }

    if (!empty($value)) {
        $opening_date_value = omsar_get_other_date_value('opening_date', $post_id);

        if (!empty($opening_date_value)) {
            $opening_timestamp = omsar_normalize_date_for_comparison($opening_date_value);
            $closing_timestamp = omsar_normalize_date_for_comparison($value);

            if ($opening_timestamp !== false && $closing_timestamp !== false && $closing_timestamp <= $opening_timestamp) {
                return __('Closing date must be after opening date.', 'omsar');
            }
        }
    }

    return $valid;
}

/**
 * Enqueue admin JavaScript for recruitment date validation feedback.
 */
add_action('admin_enqueue_scripts', 'omsar_enqueue_recruitment_validation_js');
function omsar_enqueue_recruitment_validation_js($hook) {
    // Only load on post edit pages
    if (!in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }
    
    // Get post type
    $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
    if ($post_id) {
        $post_type = get_post_type($post_id);
    } else {
        // For new posts, check the post_type parameter
        $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
    }
    
    // Only load for recruitments post type
    if ($post_type !== 'recruitments') {
        return;
    }
    
    $script = "
    jQuery(document).ready(function($) {
        function validateDateOrder() {
            var openingDateField = $('[data-name=\"opening_date\"]');
            var closingDateField = $('[data-name=\"closing_date\"]');

            if (openingDateField.length === 0) {
                $('.acf-field').each(function() {
                    var labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.indexOf('opening date') !== -1) {
                        openingDateField = $(this).find('input, select');
                        return false;
                    }
                });
            }

            if (closingDateField.length === 0) {
                $('.acf-field').each(function() {
                    var labelText = $(this).find('label').text().toLowerCase();
                    if (labelText.indexOf('closing date') !== -1) {
                        closingDateField = $(this).find('input, select');
                        return false;
                    }
                });
            }

            var openingDate = openingDateField.length > 0 ? openingDateField.val() : '';
            var closingDate = closingDateField.length > 0 ? closingDateField.val() : '';

            openingDateField.closest('.acf-field').find('.acf-date-order-error').remove();
            closingDateField.closest('.acf-field').find('.acf-date-order-error').remove();
            openingDateField.closest('.acf-field').removeClass('acf-error');
            closingDateField.closest('.acf-field').removeClass('acf-error');

            if (openingDate && closingDate) {
                var openingTimestamp = parseDateToTimestamp(openingDate);
                var closingTimestamp = parseDateToTimestamp(closingDate);

                if (openingTimestamp && closingTimestamp && closingTimestamp <= openingTimestamp) {
                    var errorMsg = '<span class=\"acf-date-order-error\" style=\"color: #dc3232; display: block; margin-top: 5px; font-size: 12px;\">Closing date must be after opening date.</span>';
                    closingDateField.closest('.acf-field').addClass('acf-error');
                    closingDateField.closest('.acf-field').find('.acf-input').append(errorMsg);

                    var errorMsg2 = '<span class=\"acf-date-order-error\" style=\"color: #dc3232; display: block; margin-top: 5px; font-size: 12px;\">Opening date must be before closing date.</span>';
                    openingDateField.closest('.acf-field').addClass('acf-error');
                    openingDateField.closest('.acf-field').find('.acf-input').append(errorMsg2);
                }
            }
        }

        function parseDateToTimestamp(dateStr) {
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

        validateDateOrder();
        setTimeout(validateDateOrder, 500);
        setTimeout(validateDateOrder, 1000);

        $(document).on('change', '[data-name=\"opening_date\"], [data-name=\"closing_date\"]', validateDateOrder);

        if (typeof acf !== 'undefined') {
            acf.addAction('ready_field/type=date_picker', function(field) {
                if (field.get('name') === 'opening_date' || field.get('name') === 'closing_date') {
                    validateDateOrder();
                }
            });

            acf.addAction('change', function(field) {
                if (field.get('name') === 'opening_date' || field.get('name') === 'closing_date') {
                    setTimeout(validateDateOrder, 100);
                }
            });
        }
    });
    ";
    
    wp_add_inline_script('jquery', $script);
}
