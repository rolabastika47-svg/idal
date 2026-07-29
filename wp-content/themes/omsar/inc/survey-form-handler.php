<?php
/**
 * Survey Form Handler
 *
 * Handles verification code check and form submission.
 * Verification code is stored in Theme Options as survey_verification_code.
 * Submissions are saved as posts of type 'survey_submission'.
 *
 * Note: If your Survey Submissions plugin uses a different post type slug (e.g. survey_submissions),
 * change the OMSAR_SURVEY_POST_TYPE constant in the filter below.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

define('OMSAR_SURVEY_POST_TYPE', 'survey_submissions');

/**
 * Fetch governorates from na_governorate table for survey form dropdown.
 * Returns array of { id, label } where label is name (Arabic) or name_en (English) based on current language.
 *
 * @return array<int, array{id: int, label: string}>
 */
function omsar_get_survey_governorates() {
    global $wpdb;
    $table = 'na_governorate';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return [];
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $use_arabic = ($current_lang === 'ar');
    $order_col = $use_arabic ? 'name' : 'name_en';
    $sql = "SELECT id, `name`, name_en FROM `{$table}` ORDER BY `{$order_col}` ASC";
    $rows = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : 0;
        $label = $use_arabic
            ? (isset($row['name']) ? $row['name'] : (isset($row['name_en']) ? $row['name_en'] : ''))
            : (isset($row['name_en']) ? $row['name_en'] : (isset($row['name']) ? $row['name'] : ''));
        if ($id && $label !== '') {
            $out[] = ['id' => $id, 'label' => $label];
        }
    }
    return $out;
}

/**
 * Get governorate label by id (for email display). Uses current language.
 *
 * @param int $governorate_id
 * @return string
 */
function omsar_get_governorate_label_by_id($governorate_id) {
    if (!$governorate_id) {
        return '';
    }
    global $wpdb;
    $table = 'na_governorate';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return (string) $governorate_id;
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $name_col = ($current_lang === 'ar') ? 'name' : 'name_en';
    $label = $wpdb->get_var($wpdb->prepare(
        "SELECT `{$name_col}` FROM `{$table}` WHERE id = %d",
        $governorate_id
    ));
    return is_string($label) ? $label : (string) $governorate_id;
}

/**
 * Fetch districts (levels) from na_level table for survey form dropdown.
 * Returns array of { id, governorate_id, label } for JS to filter by governorate_id.
 * Label is lvl1_name (Arabic) or lvl1_name_en (English) based on current language.
 *
 * @return array<int, array{id: int, governorate_id: int, label: string}>
 */
function omsar_get_survey_levels() {
    global $wpdb;
    $table = 'na_level';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return [];
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $use_arabic = ($current_lang === 'ar');
    $order_col = $use_arabic ? 'lvl1_name' : 'lvl1_name_en';
    $sql = "SELECT id, governorate_id, lvl1_name, lvl1_name_en FROM `{$table}` ORDER BY `{$order_col}` ASC";
    $rows = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : 0;
        $gov_id = isset($row['governorate_id']) ? (int) $row['governorate_id'] : 0;
        $label = $use_arabic
            ? (isset($row['lvl1_name']) ? $row['lvl1_name'] : (isset($row['lvl1_name_en']) ? $row['lvl1_name_en'] : ''))
            : (isset($row['lvl1_name_en']) ? $row['lvl1_name_en'] : (isset($row['lvl1_name']) ? $row['lvl1_name'] : ''));
        if ($id && $label !== '') {
            $out[] = ['id' => $id, 'governorate_id' => $gov_id, 'label' => $label];
        }
    }
    return $out;
}

/**
 * Get district (level) label by id (for email display). Uses current language.
 *
 * @param int $level_id
 * @return string
 */
function omsar_get_level_label_by_id($level_id) {
    if (!$level_id) {
        return '';
    }
    global $wpdb;
    $table = 'na_level';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return (string) $level_id;
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $name_col = ($current_lang === 'ar') ? 'lvl1_name' : 'lvl1_name_en';
    $label = $wpdb->get_var($wpdb->prepare(
        "SELECT `{$name_col}` FROM `{$table}` WHERE id = %d",
        $level_id
    ));
    return is_string($label) ? $label : (string) $level_id;
}

/**
 * Fetch cities from na_cities table for survey form dropdown.
 * Returns array of { id, label } where label is name_ar (Arabic) or name_en (English) based on current language.
 * Filtered by district_id.
 *
 * @param int $district_id District ID to filter cities
 * @return array<int, array{id: int, label: string}>
 */
function omsar_get_survey_cities($district_id) {
    global $wpdb;
    $table = 'na_cities';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return [];
    }
    if (!$district_id) {
        return [];
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $use_arabic = ($current_lang === 'ar');
    $order_col = $use_arabic ? 'name_ar' : 'name_en';
    $sql = $wpdb->prepare(
        "SELECT id, name_ar, name_en FROM `{$table}` WHERE district_id = %d ORDER BY `{$order_col}` ASC",
        $district_id
    );
    $rows = $wpdb->get_results($sql, ARRAY_A);
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : 0;
        $label = $use_arabic
            ? (isset($row['name_ar']) ? $row['name_ar'] : (isset($row['name_en']) ? $row['name_en'] : ''))
            : (isset($row['name_en']) ? $row['name_en'] : (isset($row['name_ar']) ? $row['name_ar'] : ''));
        if ($id && $label !== '') {
            $out[] = ['id' => $id, 'label' => $label];
        }
    }
    return $out;
}

/**
 * Get city label by id (for email display). Uses current language.
 *
 * @param int $city_id
 * @return string
 */
function omsar_get_city_label_by_id($city_id) {
    if (!$city_id) {
        return '';
    }
    global $wpdb;
    $table = 'na_cities';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return (string) $city_id;
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    $name_col = ($current_lang === 'ar') ? 'name_ar' : 'name_en';
    $label = $wpdb->get_var($wpdb->prepare(
        "SELECT `{$name_col}` FROM `{$table}` WHERE id = %d",
        $city_id
    ));
    return is_string($label) ? $label : (string) $city_id;
}

/**
 * Get the Survey success page URL for the current language (Polylang-aware).
 * Create one page per language with template "Survey Success" and link them as translations.
 *
 * @return string Empty string if no success page is set, otherwise the permalink.
 */
function omsar_get_survey_success_page_url() {
    $templates = array('templates/survey-success-page.php', 'survey-success-page.php');
    $page_id   = 0;
    foreach ($templates as $template) {
        $pages = get_pages(array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => $template,
            'number'     => 1,
            'post_status' => 'publish',
        ));
        if (!empty($pages)) {
            $page_id = (int) $pages[0]->ID;
            break;
        }
    }
    if (!$page_id) {
        return '';
    }
    if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
        $current_lang  = pll_current_language();
        $translated_id = pll_get_post($page_id, $current_lang);
        if ($translated_id && (int) $translated_id !== $page_id) {
            $page_id = (int) $translated_id;
        }
    }
    $url = get_permalink($page_id);
    return is_string($url) ? $url : '';
}

/**
 * Enqueue styles and scripts for survey form page
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template('templates/survey-form-page.php')) {
        return;
    }
    // Reuse complaint form styles for consistent form appearance
    $complaint_css = get_template_directory() . '/assets/css/complaint-inquiry-form.css';
    if (file_exists($complaint_css)) {
        wp_enqueue_style(
            'complaint-inquiry-form-css',
            get_template_directory_uri() . '/assets/css/complaint-inquiry-form.css',
            ['main-style-css'],
            filemtime($complaint_css)
        );
    }
    $survey_css = get_template_directory() . '/assets/css/survey-form.css';
    if (file_exists($survey_css)) {
        wp_enqueue_style(
            'omsar-survey-form-css',
            get_template_directory_uri() . '/assets/css/survey-form.css',
            file_exists($complaint_css) ? ['complaint-inquiry-form-css'] : ['main-style-css'],
            filemtime($survey_css)
        );
    }

    // Select2 for searchable governorate/district dropdowns (local assets)
    $select2_css = get_template_directory() . '/assets/css/select2.min.css';
    $select2_js = get_template_directory() . '/assets/js/select2.min.js';
    if (file_exists($select2_css)) {
        wp_enqueue_style(
            'select2-css',
            get_template_directory_uri() . '/assets/css/select2.min.css',
            [],
            filemtime($select2_css)
        );
    }
    if (file_exists($select2_js)) {
        wp_enqueue_script(
            'select2-js',
            get_template_directory_uri() . '/assets/js/select2.min.js',
            ['jquery'],
            filemtime($select2_js),
            true
        );
    }
    
    // Enqueue survey form JavaScript
    $survey_js = get_template_directory() . '/assets/js/survey-form.js';
    if (file_exists($survey_js)) {
        wp_enqueue_script(
            'omsar-survey-form-js',
            get_template_directory_uri() . '/assets/js/survey-form.js',
            ['jquery', 'select2-js'],
            filemtime($survey_js),
            true
        );
    }
    
    // Localize script for survey form JavaScript
    $script_handle = file_exists($survey_js) ? 'omsar-survey-form-js' : 'jquery';
    $survey_success_url = omsar_get_survey_success_page_url();
    wp_localize_script($script_handle, 'omsarSurvey', [
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'governorates'  => omsar_get_survey_governorates(),
        'districts'     => omsar_get_survey_levels(),
        'nonce'         => wp_create_nonce('omsar_survey_form_nonce'),
        'successPageUrl' => $survey_success_url ? $survey_success_url : '',
    ]);
    wp_localize_script($script_handle, 'omsarSurveyLabels', [
        'loading'   => function_exists('pll__') ? pll__('Loading...') : __('Loading...', 'omsar'),
        'selectGovernorate' => function_exists('pll__') ? pll__('Select governorate') : __('Select governorate', 'omsar'),
        'selectDistrict' => function_exists('pll__') ? pll__('Select district') : __('Select district', 'omsar'),
        'selectArea' => function_exists('pll__') ? pll__('Select area') : __('Select area', 'omsar'),
        'searchPlaceholder' => function_exists('pll__') ? pll__('Search...') : __('Search...', 'omsar'),
        'invalidCode' => function_exists('pll__') ? pll__('Invalid verification code. Please try again.') : __('Invalid verification code. Please try again.', 'omsar'),
        'error'     => function_exists('pll__') ? pll__('An error occurred. Please try again.') : __('An error occurred. Please try again.', 'omsar'),
        'success'   => function_exists('pll__') ? pll__('Thank you for completing the survey.') : __('Thank you for completing the survey.', 'omsar'),
        'fillRequiredFields' => function_exists('pll__') ? pll__('Please fill in all required fields.') : __('Please fill in all required fields.', 'omsar'),
        'validNumberOfProperties' => function_exists('pll__') ? pll__('Please enter a valid number of properties.') : __('Please enter a valid number of properties.', 'omsar'),
        'fillAllPropertyInfo' => function_exists('pll__') ? pll__('Please fill in all property information before continuing.') : __('Please fill in all property information before continuing.', 'omsar'),
        'propertyLabel' => function_exists('pll__') ? pll__('Property') : __('Property', 'omsar'),
        'propertyLocation' => function_exists('pll__') ? pll__('Property location') : __('Property location', 'omsar'),
        'enterPropertyLocation' => function_exists('pll__') ? pll__('Enter property location') : __('Enter property location', 'omsar'),
        'governorate' => function_exists('pll__') ? pll__('Governorate') : __('Governorate', 'omsar'),
        'enterGovernorate' => function_exists('pll__') ? pll__('Enter governorate') : __('Enter governorate', 'omsar'),
        'district' => function_exists('pll__') ? pll__('District') : __('District', 'omsar'),
        'enterDistrict' => function_exists('pll__') ? pll__('Enter district') : __('Enter district', 'omsar'),
        'area' => function_exists('pll__') ? pll__('Area') : __('Area', 'omsar'),
        'enterArea' => function_exists('pll__') ? pll__('Enter area') : __('Enter area', 'omsar'),
        'propertyNumber' => function_exists('pll__') ? pll__('Property Number') : __('Property Number', 'omsar'),
        'enterPropertyNumber' => function_exists('pll__') ? pll__('Enter property number') : __('Enter property number', 'omsar'),
        'sectionNumber' => function_exists('pll__') ? pll__('Section Number') : __('Section Number', 'omsar'),
        'enterSectionNumber' => function_exists('pll__') ? pll__('Enter section number') : __('Enter section number', 'omsar'),
        'propertyAreaSqm' => function_exists('pll__') ? pll__('Property area in square meters') : __('Property area in square meters', 'omsar'),
        'enterPropertyArea' => function_exists('pll__') ? pll__('Enter property area') : __('Enter property area', 'omsar'),
        'propertyOwnershipType' => function_exists('pll__') ? pll__('Property ownership type') : __('Property ownership type', 'omsar'),
        'stateOwned' => function_exists('pll__') ? pll__('State-owned') : __('State-owned', 'omsar'),
        'rented' => function_exists('pll__') ? pll__('Rented') : __('Rented', 'omsar'),
        'rentalContractDetails' => function_exists('pll__') ? pll__('Rental contract details') : __('Rental contract details', 'omsar'),
        'dateOfFirstLease' => function_exists('pll__') ? pll__('Date of first lease') : __('Date of first lease', 'omsar'),
        'startDate' => function_exists('pll__') ? pll__('Start date') : __('Start date', 'omsar'),
        'endDate' => function_exists('pll__') ? pll__('End date') : __('End date', 'omsar'),
        'leaseValue' => function_exists('pll__') ? pll__('Lease value') : __('Lease value', 'omsar'),
        'enterLeaseValue' => function_exists('pll__') ? pll__('Enter lease value') : __('Enter lease value', 'omsar'),
        'exchangeRateUsed' => function_exists('pll__') ? pll__('Exchange rate used') : __('Exchange rate used', 'omsar'),
        'enterExchangeRate' => function_exists('pll__') ? pll__('Enter exchange rate') : __('Enter exchange rate', 'omsar'),
        'pricePerSquareMeter' => function_exists('pll__') ? pll__('Price per square meter') : __('Price per square meter', 'omsar'),
        'enterPricePerSquareMeter' => function_exists('pll__') ? pll__('Enter price per square meter') : __('Enter price per square meter', 'omsar'),
        'attachPropertyCertificate' => function_exists('pll__') ? pll__('Attach property certificate') : __('Attach property certificate', 'omsar'),
        'endDateMustBeLater' => function_exists('pll__') ? pll__('End date must be later than start date.') : __('End date must be later than start date.', 'omsar'),
        'firstLeaseDateMustBeBefore' => function_exists('pll__') ? pll__('Date of first lease must be before or equal to contract start date.') : __('Date of first lease must be before or equal to contract start date.', 'omsar'),
        'durationOfCurrentContract' => function_exists('pll__') ? pll__('Duration of the current contract') : __('Duration of the current contract', 'omsar'),
    ]);
});

/**
 * AJAX: Get cities by district ID
 */
add_action('wp_ajax_omsar_get_cities_by_district', 'omsar_get_cities_by_district');
add_action('wp_ajax_nopriv_omsar_get_cities_by_district', 'omsar_get_cities_by_district');

function omsar_get_cities_by_district() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'omsar_survey_form_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        return;
    }

    $district_id = isset($_POST['district_id']) ? absint($_POST['district_id']) : 0;
    
    if (!$district_id) {
        wp_send_json_error(['message' => 'Invalid district ID.']);
        return;
    }

    $cities = omsar_get_survey_cities($district_id);
    wp_send_json_success(['cities' => $cities]);
}

/**
 * AJAX: Verify survey code
 */
add_action('wp_ajax_omsar_verify_survey_code', 'omsar_verify_survey_code');
add_action('wp_ajax_nopriv_omsar_verify_survey_code', 'omsar_verify_survey_code');

function omsar_verify_survey_code() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'omsar_survey_verify_nonce')) {
        $msg = function_exists('pll__')
            ? pll__('Security check failed. Please refresh and try again.')
            : __('Security check failed. Please refresh and try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }

    $entered = isset($_POST['verification_code']) ? sanitize_text_field(wp_unslash($_POST['verification_code'])) : '';
    
    if (empty($entered)) {
        $msg = function_exists('pll__')
            ? pll__('Invalid verification code. Please try again.')
            : __('Invalid verification code. Please try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }

    // Get survey_repeater from Theme Options
    $survey_repeater = array();
    if (function_exists('get_field')) {
        $survey_repeater = get_field('survey_repeater', 'option');
    }
    
    // Check if survey_repeater exists and is an array
    if (!is_array($survey_repeater) || empty($survey_repeater)) {
        $msg = function_exists('pll__')
            ? pll__('Invalid verification code. Please try again.')
            : __('Invalid verification code. Please try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }

    // Loop through repeater to find matching verification code
    $code_found = false;
    foreach ($survey_repeater as $row) {
        $code = isset($row['survey_verification_code']) ? trim($row['survey_verification_code']) : '';
        if (!empty($code) && hash_equals($code, $entered)) {
            $code_found = true;
            break;
        }
    }

    if ($code_found) {
        wp_send_json_success();
        return;
    }

    $msg = function_exists('pll__')
        ? pll__('Invalid verification code. Please try again.')
        : __('Invalid verification code. Please try again.', 'omsar');
    wp_send_json_error(['message' => $msg]);
}

/**
 * AJAX: Submit survey form
 */
add_action('wp_ajax_omsar_submit_survey_form', 'omsar_submit_survey_form');
add_action('wp_ajax_nopriv_omsar_submit_survey_form', 'omsar_submit_survey_form');

function omsar_submit_survey_form() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'omsar_survey_form_nonce')) {
        $msg = function_exists('pll__')
            ? pll__('Security check failed. Please refresh and try again.')
            : __('Security check failed. Please refresh and try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }

    // General Information
    $administration_name = isset($_POST['administration_name']) ? sanitize_text_field(wp_unslash($_POST['administration_name'])) : '';
    $number_of_properties = isset($_POST['number_of_properties']) ? absint($_POST['number_of_properties']) : 0;
    
    // Survey Filler Information
    $full_name  = isset($_POST['survey_full_name']) ? sanitize_text_field(wp_unslash($_POST['survey_full_name'])) : '';
    $job_title  = isset($_POST['survey_job_title']) ? sanitize_text_field(wp_unslash($_POST['survey_job_title'])) : '';
    $phone      = isset($_POST['survey_phone']) ? sanitize_text_field(wp_unslash($_POST['survey_phone'])) : '';
    $email      = isset($_POST['survey_email']) ? sanitize_email(wp_unslash($_POST['survey_email'])) : '';
    $verification_code = isset($_POST['verification_code']) ? sanitize_text_field(wp_unslash($_POST['verification_code'])) : '';

    // Properties data
    $properties = array();
    if (isset($_POST['properties']) && is_array($_POST['properties'])) {
        foreach ($_POST['properties'] as $index => $property) {
            $ownership_type = isset($property['ownership_type']) ? sanitize_text_field(wp_unslash($property['ownership_type'])) : '';
            
            // Validate rental fields if ownership type is "rented"
            if ($ownership_type === 'rented') {
                $first_lease_date = isset($property['first_lease_date']) ? sanitize_text_field(wp_unslash($property['first_lease_date'])) : '';
                $contract_start = isset($property['contract_start']) ? sanitize_text_field(wp_unslash($property['contract_start'])) : '';
                $contract_end = isset($property['contract_end']) ? sanitize_text_field(wp_unslash($property['contract_end'])) : '';
                $lease_value = isset($property['lease_value']) ? trim(wp_unslash($property['lease_value'])) : '';
                $exchange_rate = isset($property['exchange_rate']) ? trim(wp_unslash($property['exchange_rate'])) : '';
                
                if (empty($first_lease_date) || empty($contract_start) || empty($contract_end) || empty($lease_value) || empty($exchange_rate)) {
                    $msg = function_exists('pll__')
                        ? pll__('Please fill in all required rental contract details for rented properties.')
                        : __('Please fill in all required rental contract details for rented properties.', 'omsar');
                    wp_send_json_error(['message' => $msg]);
                    return;
                }
                
                // Validate that first_lease_date is before or equal to contract_start
                if (!empty($first_lease_date) && !empty($contract_start)) {
                    $first_lease_timestamp = strtotime($first_lease_date);
                    $start_timestamp = strtotime($contract_start);
                    
                    if ($first_lease_timestamp > $start_timestamp) {
                        $msg = function_exists('pll__')
                            ? pll__('Date of first lease must be before or equal to contract start date.')
                            : __('Date of first lease must be before or equal to contract start date.', 'omsar');
                        wp_send_json_error(['message' => $msg]);
                        return;
                    }
                }
                
                // Validate that contract_end is later than contract_start
                if (!empty($contract_start) && !empty($contract_end)) {
                    $start_timestamp = strtotime($contract_start);
                    $end_timestamp = strtotime($contract_end);
                    
                    if ($end_timestamp <= $start_timestamp) {
                        $msg = function_exists('pll__')
                            ? pll__('Contract end date must be later than contract start date.')
                            : __('Contract end date must be later than contract start date.', 'omsar');
                        wp_send_json_error(['message' => $msg]);
                        return;
                    }
                }
            }
            
            $governorate_id = isset($property['governorate']) ? absint($property['governorate']) : 0;
            if ($governorate_id === 0) {
                $msg = function_exists('pll__')
                    ? pll__('Please select a governorate for each property.')
                    : __('Please select a governorate for each property.', 'omsar');
                wp_send_json_error(['message' => $msg]);
                return;
            }
            $district_id = isset($property['district']) ? absint($property['district']) : 0;
            if ($district_id === 0) {
                $msg = function_exists('pll__')
                    ? pll__('Please select a district for each property.')
                    : __('Please select a district for each property.', 'omsar');
                wp_send_json_error(['message' => $msg]);
                return;
            }
            $area_id = isset($property['area']) ? absint($property['area']) : 0;
            if ($area_id === 0) {
                $msg = function_exists('pll__')
                    ? pll__('Please select an area for each property.')
                    : __('Please select an area for each property.', 'omsar');
                wp_send_json_error(['message' => $msg]);
                return;
            }
            $properties[] = array(
                'governorate' => $governorate_id,
                'district' => $district_id,
                'area' => $area_id,
                'property_number' => isset($property['property_number']) ? sanitize_text_field(wp_unslash($property['property_number'])) : '',
                'section_number' => isset($property['section_number']) ? sanitize_text_field(wp_unslash($property['section_number'])) : '',
                'area_sqm' => isset($property['area_sqm']) ? floatval($property['area_sqm']) : 0,
                'ownership_type' => $ownership_type,
                'first_lease_date' => isset($property['first_lease_date']) ? sanitize_text_field(wp_unslash($property['first_lease_date'])) : '',
                'contract_start' => isset($property['contract_start']) ? sanitize_text_field(wp_unslash($property['contract_start'])) : '',
                'contract_end' => isset($property['contract_end']) ? sanitize_text_field(wp_unslash($property['contract_end'])) : '',
                'lease_value' => isset($property['lease_value']) ? floatval($property['lease_value']) : 0,
                'exchange_rate' => isset($property['exchange_rate']) ? floatval($property['exchange_rate']) : 0,
                'price_per_sqm' => isset($property['price_per_sqm']) ? floatval($property['price_per_sqm']) : 0,
            );
        }
    }

    $required_msg = function_exists('pll__')
        ? pll__('Please fill in all required fields.')
        : __('Please fill in all required fields.', 'omsar');
    $email_msg = function_exists('pll__')
        ? pll__('Please enter a valid email address.')
        : __('Please enter a valid email address.', 'omsar');
    $save_msg = function_exists('pll__')
        ? pll__('An error occurred while saving. Please try again.')
        : __('An error occurred while saving. Please try again.', 'omsar');
    $success_msg = function_exists('pll__')
        ? pll__('Thank you for completing the survey.')
        : __('Thank you for completing the survey.', 'omsar');

    // Validation
    if (empty(trim($administration_name))) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if ($number_of_properties < 1) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if (empty(trim($full_name)) || empty(trim($job_title)) || empty(trim($phone))) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if (empty($email) || !is_email($email)) {
        wp_send_json_error(['message' => $email_msg]);
        return;
    }

    $post_type = apply_filters('omsar_survey_submission_post_type', OMSAR_SURVEY_POST_TYPE);

    $post_data = [
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'post_title'  => sprintf('Survey - %s - %s', $administration_name, date_i18n('Y-m-d H:i')),
        'post_content' => '',
    ];

    $post_id = wp_insert_post($post_data, true);
    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => $save_msg]);
        return;
    }

    $save_field = function ($key, $value) use ($post_id) {
        if (function_exists('update_field')) {
            update_field($key, $value, $post_id);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    };

    // Save general information
    $save_field('administration_name', $administration_name);
    $save_field('number_of_properties', $number_of_properties);
    
    // Save properties as ACF repeater field
    // Structure: properties (repeater) -> each row contains all property fields
    if (function_exists('update_field')) {
        // Handle file uploads first and attach them to properties
        $uploaded_files = array();
        if (!empty($_FILES['properties']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            foreach ($_FILES['properties']['name'] as $index => $files) {
                if (isset($files['certificate']) && !empty($files['certificate']) && isset($_FILES['properties']['tmp_name'][$index]['certificate']) && is_uploaded_file($_FILES['properties']['tmp_name'][$index]['certificate'])) {
                    $file = array(
                        'name'     => $_FILES['properties']['name'][$index]['certificate'],
                        'type'     => $_FILES['properties']['type'][$index]['certificate'],
                        'tmp_name' => $_FILES['properties']['tmp_name'][$index]['certificate'],
                        'error'    => (int) $_FILES['properties']['error'][$index]['certificate'],
                        'size'     => (int) $_FILES['properties']['size'][$index]['certificate'],
                    );
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if (!isset($upload['error'])) {
                        $attachment = array(
                            'post_mime_type' => $upload['type'],
                            'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
                            'post_content'   => '',
                            'post_status'    => 'inherit',
                        );
                        $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                        if (!is_wp_error($attach_id)) {
                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            $uploaded_files[$index] = $attach_id;
                        }
                    }
                }
            }
        }
        
        // Prepare properties array for ACF repeater (save labels in meta, not ids)
        // ACF repeater expects an array where each element is an array of sub-field values
        $repeater_data = array();
        foreach ($properties as $index => $property) {
            $repeater_row = array(
                'property_governorate' => omsar_get_governorate_label_by_id($property['governorate']),
                'property_district' => omsar_get_level_label_by_id($property['district']),
                'property_area' => omsar_get_city_label_by_id($property['area']),
                'property_number' => $property['property_number'],
                'property_section_number' => $property['section_number'],
                'property_area_sqm' => $property['area_sqm'],
                'property_ownership_type' => $property['ownership_type'],
                'property_first_lease_date' => $property['first_lease_date'],
                'property_contract_start' => $property['contract_start'],
                'property_contract_end' => $property['contract_end'],
                'property_lease_value' => $property['lease_value'],
                'property_exchange_rate' => $property['exchange_rate'],
                'property_price_per_sqm' => $property['price_per_sqm'],
            );
            
            // Add certificate attachment if uploaded
            if (isset($uploaded_files[$index])) {
                $repeater_row['property_certificate'] = $uploaded_files[$index];
            }
            
            $repeater_data[] = $repeater_row;
        }
        
        // Save as ACF repeater field
        update_field('properties', $repeater_data, $post_id);
    } else {
        // Fallback: save as serialized post meta with governorate/district as labels
        $properties_for_meta = array();
        foreach ($properties as $property) {
            $row = $property;
            $row['governorate'] = omsar_get_governorate_label_by_id($property['governorate']);
            $row['district'] = omsar_get_level_label_by_id($property['district']);
            $properties_for_meta[] = $row;
        }
        update_post_meta($post_id, 'properties', $properties_for_meta);
        
        // Handle file uploads separately for fallback
        if (!empty($_FILES['properties']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            $uploaded_files = array();
            foreach ($_FILES['properties']['name'] as $index => $files) {
                if (isset($files['certificate']) && !empty($files['certificate']) && isset($_FILES['properties']['tmp_name'][$index]['certificate']) && is_uploaded_file($_FILES['properties']['tmp_name'][$index]['certificate'])) {
                    $file = array(
                        'name'     => $_FILES['properties']['name'][$index]['certificate'],
                        'type'     => $_FILES['properties']['type'][$index]['certificate'],
                        'tmp_name' => $_FILES['properties']['tmp_name'][$index]['certificate'],
                        'error'    => (int) $_FILES['properties']['error'][$index]['certificate'],
                        'size'     => (int) $_FILES['properties']['size'][$index]['certificate'],
                    );
                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $upload = wp_handle_upload($file, array('test_form' => false));
                    if (!isset($upload['error'])) {
                        $attachment = array(
                            'post_mime_type' => $upload['type'],
                            'post_title'     => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
                            'post_content'   => '',
                            'post_status'    => 'inherit',
                        );
                        $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                        if (!is_wp_error($attach_id)) {
                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            $uploaded_files[$index] = $attach_id;
                        }
                    }
                }
            }
            if (!empty($uploaded_files)) {
                update_post_meta($post_id, 'property_certificates', $uploaded_files);
            }
        }
    }
    
    // Save survey filler information
    $save_field('survey_full_name', $full_name);
    $save_field('survey_job_title', $job_title);
    $save_field('survey_phone', $phone);
    $save_field('survey_email', $email);
    
    // Store the verification code used
    if (!empty($verification_code)) {
        $save_field('verification_code', $verification_code);
    }

    // Send email notification
    omsar_send_survey_form_email($administration_name, $full_name, $email, $phone, $job_title, $number_of_properties, $properties, $post_id);

    wp_send_json_success(['message' => $success_msg]);
}

/**
 * Send email notification when survey form is submitted
 * 
 * @param string $administration_name Administration name
 * @param string $full_name Full name of the submitter
 * @param string $email Email address of the submitter
 * @param string $phone Phone number of the submitter
 * @param string $job_title Job title of the submitter
 * @param int $number_of_properties Number of properties
 * @param array $properties Array of property data
 * @param int $post_id Post ID of the created survey submission
 */
function omsar_send_survey_form_email($administration_name, $full_name, $email, $phone, $job_title, $number_of_properties, $properties, $post_id) {
    // Get recipient email from ACF options
    $recipient_email = '';
    if (function_exists('get_field')) {
        $recipient_email = get_field('survey_recipient_email', 'option');
    }
    
    // If no recipient email is set, don't send email
    if (empty($recipient_email) || !is_email($recipient_email)) {
        return;
    }
    
    // Detect current language for RTL support
    $current_lang = 'en';
    
    // Try to get language from post first (Polylang stores it with posts)
    if ($post_id && function_exists('pll_get_post_language')) {
        $post_lang = pll_get_post_language($post_id);
        if ($post_lang) {
            $current_lang = $post_lang;
        }
    }
    
    // Fallback: Check HTTP referer for language code (Polylang URL structure)
    if (($current_lang === 'en' || empty($current_lang)) && isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        if (preg_match('#/(ar|en)/#', $referer, $matches)) {
            $current_lang = $matches[1];
        }
    }
    
    // Fallback to current language detection
    if (($current_lang === 'en' || empty($current_lang)) && function_exists('pll_current_language')) {
        $detected_lang = pll_current_language();
        if ($detected_lang) {
            $current_lang = $detected_lang;
        }
    }
    
    // Final fallback: Check if RTL
    if (empty($current_lang) || $current_lang === 'en') {
        $current_lang = is_rtl() ? 'ar' : 'en';
    }
    
    $is_arabic = ($current_lang === 'ar' || is_rtl());
    $dir = $is_arabic ? 'rtl' : 'ltr';
    $text_align = $is_arabic ? 'right' : 'left';
    
    // Prepare email subject
    $subject = sprintf(
        function_exists('pll__') 
            ? pll__('New Survey Submission from %s')
            : __('New Survey Submission from %s', 'omsar'),
        get_bloginfo('name')
    );
    
    // Prepare email body with RTL support
    $email_body = '<!DOCTYPE html><html dir="' . esc_attr($dir) . '" lang="' . esc_attr($current_lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $email_body .= '<style type="text/css">';
    $email_body .= 'body { font-family: ' . ($is_arabic ? "'Almarai', sans-serif" : "'Inter', sans-serif") . '; direction: ' . $dir . '; text-align: ' . $text_align . '; line-height: 1.6; color: #333; margin: 0; padding: 20px; }';
    $email_body .= 'h2, h3, h4, h5 { color: #1a1a1a; margin-top: 20px; margin-bottom: 10px; text-align: ' . $text_align . '; }';
    $email_body .= 'h2 { font-size: 24px; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }';
    $email_body .= 'h3 { font-size: 20px; margin-top: 25px; }';
    $email_body .= 'h4 { font-size: 18px; margin-top: 20px; }';
    $email_body .= 'h5 { font-size: 16px; margin-top: 15px; }';
    $email_body .= 'p { margin: 10px 0; text-align: ' . $text_align . '; }';
    $email_body .= 'ul { margin: ' . ($is_arabic ? '0 20px 10px 0' : '0 0 10px 20px') . '; padding-' . ($is_arabic ? 'right' : 'left') . ': 20px; text-align: ' . $text_align . '; }';
    $email_body .= 'li { margin: 5px 0; text-align: ' . $text_align . '; }';
    $email_body .= 'strong { font-weight: 600; }';
    $email_body .= 'a { color: #2271b1; text-decoration: none; }';
    $email_body .= 'a:hover { text-decoration: underline; }';
    $email_body .= 'hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }';
    $email_body .= '</style></head><body>';
    $email_body .= '<h2>' . (function_exists('pll__') ? pll__('New Survey Submission') : __('New Survey Submission', 'omsar')) . '</h2>';
    
    $email_body .= '<h3>' . (function_exists('pll__') ? pll__('General Information about the Administration') : __('General Information about the Administration', 'omsar')) . '</h3>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Administration Name') : __('Administration Name', 'omsar')) . ': </strong> ' . esc_html($administration_name) . '</p>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Number of properties/buildings under the administration') : __('Number of properties/buildings under the administration', 'omsar')) . ': </strong> ' . esc_html($number_of_properties) . '</p>';
    
    if (!empty($properties)) {
        $email_body .= '<h3>' . (function_exists('pll__') ? pll__('List of Properties / Buildings') : __('List of Properties / Buildings', 'omsar')) . '</h3>';
        foreach ($properties as $index => $property) {
            $property_num = $index + 1;
            $email_body .= '<h4>' . (function_exists('pll__') ? pll__('Property') : __('Property', 'omsar')) . ' ' . $property_num . '</h4>';
            
            $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Property location') : __('Property location', 'omsar')) . '</strong></p>';
            $email_body .= '<ul style="text-align: ' . $text_align . '; padding-' . ($is_arabic ? 'right' : 'left') . ': 20px;">';
            $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Governorate') : __('Governorate', 'omsar')) . ': </strong> ' . esc_html(omsar_get_governorate_label_by_id($property['governorate'])) . '</li>';
            $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('District') : __('District', 'omsar')) . ': </strong> ' . esc_html(omsar_get_level_label_by_id($property['district'])) . '</li>';
            $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Area') : __('Area', 'omsar')) . ': </strong> ' . esc_html(omsar_get_city_label_by_id($property['area'])) . '</li>';
            $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Property Number') : __('Property Number', 'omsar')) . ': </strong> ' . esc_html($property['property_number']) . '</li>';
            $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Section Number') : __('Section Number', 'omsar')) . ': </strong> ' . esc_html($property['section_number']) . '</li>';
            $email_body .= '</ul>';
            
            $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Property area in square meters') : __('Property area in square meters', 'omsar')) . ': </strong> ' . esc_html($property['area_sqm']) . '</p>';
            
            $ownership_type_label = ($property['ownership_type'] === 'rented') 
                ? (function_exists('pll__') ? pll__('Rented') : __('Rented', 'omsar'))
                : (function_exists('pll__') ? pll__('State-owned') : __('State-owned', 'omsar'));
            $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Property ownership type') : __('Property ownership type', 'omsar')) . ': </strong> ' . esc_html($ownership_type_label) . '</p>';
            
            if ($property['ownership_type'] === 'rented') {
                $email_body .= '<h5 style="text-align: ' . $text_align . ';">' . (function_exists('pll__') ? pll__('Rental contract details') : __('Rental contract details', 'omsar')) . '</h5>';
                $email_body .= '<ul style="text-align: ' . $text_align . '; padding-' . ($is_arabic ? 'right' : 'left') . ': 20px;">';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Date of first lease') : __('Date of first lease', 'omsar')) . ': </strong> ' . esc_html($property['first_lease_date']) . '</li>';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Duration of the current contract') : __('Duration of the current contract', 'omsar')) . '</strong></li>';
                $email_body .= '<ul style="text-align: ' . $text_align . '; padding-' . ($is_arabic ? 'right' : 'left') . ': 20px;">';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Start date') : __('Start date', 'omsar')) . ': </strong> ' . esc_html($property['contract_start']) . '</li>';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('End date') : __('End date', 'omsar')) . ': </strong> ' . esc_html($property['contract_end']) . '</li>';
                $email_body .= '</ul>';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Lease value') : __('Lease value', 'omsar')) . ': </strong> ' . esc_html($property['lease_value']) . '</li>';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Exchange rate used') : __('Exchange rate used', 'omsar')) . ': </strong> ' . esc_html($property['exchange_rate']) . '</li>';
                $email_body .= '<li style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Price per square meter') : __('Price per square meter', 'omsar')) . ': </strong> ' . esc_html($property['price_per_sqm']) . '</li>';
                $email_body .= '</ul>';
            }
        }
    }
    
    $email_body .= '<h3 style="text-align: ' . $text_align . ';">' . (function_exists('pll__') ? pll__('Survey Filler Information') : __('Survey Filler Information', 'omsar')) . '</h3>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Full Name') : __('Full Name', 'omsar')) . ': </strong> ' . esc_html($full_name) . '</p>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Job Title') : __('Job Title', 'omsar')) . ': </strong> ' . esc_html($job_title) . '</p>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Phone Number') : __('Phone Number', 'omsar')) . ': </strong> ' . esc_html($phone) . '</p>';
    $email_body .= '<p style="text-align: ' . $text_align . ';"><strong>' . (function_exists('pll__') ? pll__('Email') : __('Email', 'omsar')) . ': </strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
    
    $email_body .= '</body></html>';
    
    // Set email headers for HTML email
    // $headers = array(
    //     'Content-Type: text/html; charset=UTF-8',
    //     'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    //     'Reply-To: ' . $full_name . ' <' . $email . '>'
    // );
    
    // Collect property certificate attachments
    $attachments = array();
    if ($post_id) {
        // Get properties from ACF repeater field
        $saved_properties = array();
        if (function_exists('get_field')) {
            $saved_properties = get_field('properties', $post_id);
        }
        
        // If ACF field doesn't exist, try post meta
        if (empty($saved_properties) || !is_array($saved_properties)) {
            $saved_properties = get_post_meta($post_id, 'properties', true);
            if (!is_array($saved_properties)) {
                $saved_properties = array();
            }
        }
        
        // Extract certificate attachment IDs and get file paths
        foreach ($saved_properties as $property) {
            $certificate_id = null;
            
            // Handle ACF field structure
            if (isset($property['property_certificate'])) {
                $certificate_id = $property['property_certificate'];
            } elseif (isset($property['certificate'])) {
                $certificate_id = $property['certificate'];
            }
            
            // Handle if certificate is stored as attachment ID array
            if (is_array($certificate_id) && isset($certificate_id['ID'])) {
                $certificate_id = $certificate_id['ID'];
            }
            
            // Get file path if certificate ID exists
            if (!empty($certificate_id) && is_numeric($certificate_id)) {
                $file_path = get_attached_file($certificate_id);
                if ($file_path && file_exists($file_path)) {
                    $attachments[] = $file_path;
                }
            }
        }
    }
    
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send email with attachments
    wp_mail($recipient_email, $subject, $email_body, $headers, $attachments);
}
