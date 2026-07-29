<?php
/**
 * Citizen Survey Handler
 *
 * Handles verification (citizen_survey_repeater / citizen_survey_code) and
 * form submission (post type citizen_survey_sub) for the Citizen Survey template only.
 *
 * @package OMSAR
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue styles and scripts for Citizen Survey page only
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template('templates/citizen-survey-page.php')) {
        return;
    }
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
            'omsar-citizen-survey-css',
            get_template_directory_uri() . '/assets/css/survey-form.css',
            file_exists($complaint_css) ? ['complaint-inquiry-form-css'] : ['main-style-css'],
            filemtime($survey_css)
        );
    }
    // Select2 for searchable dropdowns (Governorate, Region, Annual income)
    $select2_css = get_template_directory() . '/assets/css/select2.min.css';
    $select2_js = get_template_directory() . '/assets/js/select2.min.js';
    if (file_exists($select2_css)) {
        wp_enqueue_style('select2-css', get_template_directory_uri() . '/assets/css/select2.min.css', [], filemtime($select2_css));
    }
    if (file_exists($select2_js)) {
        wp_enqueue_script('select2-js', get_template_directory_uri() . '/assets/js/select2.min.js', ['jquery'], filemtime($select2_js), true);
    }
    $citizen_js = get_template_directory() . '/assets/js/citizen-survey-form.js';
    if (file_exists($citizen_js)) {
        $citizen_deps = ['jquery'];
        if (file_exists($select2_js)) {
            $citizen_deps[] = 'select2-js';
        }
        wp_enqueue_script(
            'omsar-citizen-survey-js',
            get_template_directory_uri() . '/assets/js/citizen-survey-form.js',
            $citizen_deps,
            filemtime($citizen_js),
            true
        );
        $success_page_url = omsar_get_citizen_survey_success_page_url();
        wp_localize_script('omsar-citizen-survey-js', 'omsarCitizenSurvey', [
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'successPageUrl' => $success_page_url ? $success_page_url : '',
        ]);
        wp_localize_script('omsar-citizen-survey-js', 'omsarCitizenSurveyLabels', [
            'loading' => function_exists('pll__') ? pll__('Loading...') : __('Loading...', 'omsar'),
            'invalidCode' => function_exists('pll__') ? pll__('Invalid verification code. Please try again.') : __('Invalid verification code. Please try again.', 'omsar'),
            'error' => function_exists('pll__') ? pll__('An error occurred. Please try again.') : __('An error occurred. Please try again.', 'omsar'),
            'success' => function_exists('pll__') ? pll__('Thank you for completing the survey.') : __('Thank you for completing the survey.', 'omsar'),
            'fillRequiredFields' => function_exists('pll__') ? pll__('Please fill in all required fields.') : __('Please fill in all required fields.', 'omsar'),
            'thisFieldRequired' => function_exists('pll__') ? pll__('This field is required.') : __('This field is required.', 'omsar'),
            'rankingError' => function_exists('pll__') ? pll__('Please select exactly 3 options and assign unique rankings from 1 to 3.') : __('Please select exactly 3 options and assign unique rankings from 1 to 3.', 'omsar'),
            'rankingNumberError' => function_exists('pll__') ? pll__('Please enter a number between 1 and 3.') : __('Please enter a number between 1 and 3.', 'omsar'),
            'searchPlaceholder' => function_exists('pll__') ? pll__('Search...') : __('Search...', 'omsar'),
        ]);
    }
});

/**
 * Get the Citizen Survey success page URL for the current language (Polylang-aware).
 * Uses the same "Survey Success" template as the survey form. Create one page per language
 * with template "Survey Success" and link them as translations.
 *
 * @return string Empty string if no success page is set, otherwise the permalink.
 */
function omsar_get_citizen_survey_success_page_url() {
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
        $current_lang = pll_current_language();
        $translated_id = pll_get_post($page_id, $current_lang);
        if ($translated_id && (int) $translated_id !== $page_id) {
            $page_id = (int) $translated_id;
        }
    }
    $url = get_permalink($page_id);
    return is_string($url) ? $url : '';
}

/**
 * AJAX: Verify Citizen Survey code (citizen_survey_repeater / citizen_survey_code)
 */
add_action('wp_ajax_omsar_verify_citizen_survey_code', 'omsar_verify_citizen_survey_code');
add_action('wp_ajax_nopriv_omsar_verify_citizen_survey_code', 'omsar_verify_citizen_survey_code');

function omsar_verify_citizen_survey_code() {
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
    $repeater = array();
    if (function_exists('get_field')) {
        $repeater = get_field('citizen_survey_repeater', 'option');
    }
    if (!is_array($repeater) || empty($repeater)) {
        $msg = function_exists('pll__')
            ? pll__('Invalid verification code. Please try again.')
            : __('Invalid verification code. Please try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }
    $code_found = false;
    foreach ($repeater as $row) {
        $code = isset($row['citizen_survey_code']) ? trim($row['citizen_survey_code']) : '';
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
 * AJAX: Submit Citizen Survey form (post type citizen_survey_sub)
 */
add_action('wp_ajax_omsar_submit_citizen_survey_form', 'omsar_submit_citizen_survey_form');
add_action('wp_ajax_nopriv_omsar_submit_citizen_survey_form', 'omsar_submit_citizen_survey_form');

function omsar_submit_citizen_survey_form() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'omsar_survey_form_nonce')) {
        $msg = function_exists('pll__')
            ? pll__('Security check failed. Please refresh and try again.')
            : __('Security check failed. Please refresh and try again.', 'omsar');
        wp_send_json_error(['message' => $msg]);
        return;
    }

    $verification_code = isset($_POST['verification_code']) ? sanitize_text_field(wp_unslash($_POST['verification_code'])) : '';
    $required_msg = function_exists('pll__') ? pll__('Please fill in all required fields.') : __('Please fill in all required fields.', 'omsar');
    $success_msg = function_exists('pll__') ? pll__('Thank you for completing the survey.') : __('Thank you for completing the survey.', 'omsar');
    $save_msg = function_exists('pll__') ? pll__('An error occurred while saving. Please try again.') : __('An error occurred while saving. Please try again.', 'omsar');

    $q1 = isset($_POST['q1_institutions']) && is_array($_POST['q1_institutions']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['q1_institutions'])) : array();
    if (empty($q1)) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    $q1_has_not_any = in_array('not_any', $q1, true);
    $q2 = isset($_POST['q2_satisfaction']) ? sanitize_text_field(wp_unslash($_POST['q2_satisfaction'])) : '';
    
    // Process Q3 Matrix as ACF Repeater field
    $q3_matrix = array();
    if (isset($_POST['q3_matrix']) && is_array($_POST['q3_matrix'])) {
        foreach ($_POST['q3_matrix'] as $index => $row_data) {
            if (isset($row_data['row_name']) && isset($row_data['selected_value'])) {
                $q3_matrix[] = array(
                    'row_name' => sanitize_text_field(wp_unslash($row_data['row_name'])),
                    'selected_value' => sanitize_text_field(wp_unslash($row_data['selected_value']))
                );
            }
        }
    }
    
    // Validate Q3 matrix has all 6 rows
    if (!$q1_has_not_any && count($q3_matrix) !== 6) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    
    // Validate each row has a selected value
    if (!$q1_has_not_any) {
        foreach ($q3_matrix as $row) {
            if (empty($row['selected_value']) && $row['selected_value'] !== '0') {
                wp_send_json_error(['message' => $required_msg]);
                return;
            }
        }
    }
    // Process Q4 Ranking - combine checkbox selections with rankings
    $q4_cb = isset($_POST['q4_rank_cb']) && is_array($_POST['q4_rank_cb']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['q4_rank_cb'])) : array();
    $q4_num = isset($_POST['q4_rank_num']) && is_array($_POST['q4_rank_num']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['q4_rank_num'])) : array();
    
    // Process Q4 ranking into ordered array (rank 1, 2, 3)
    $q4_ranked = array();
    // Changed condition: q4_num can have more items than q4_cb (all form inputs vs only checked)
    if (!empty($q4_cb) && !empty($q4_num)) {
        $q4_options = array(
            'Providing clear and accessible information',
            'Inclusive services for all groups including persons with disabilities and elderly',
            'Simplifying procedures and reducing timeframes',
            'Unified government online portal',
            'Reducing service costs',
            'Enabling complaint submission and quick response',
            'Decentralizing procedures to municipalities',
            'Improving infrastructure and cleanliness of public offices',
        );
        
        // Combine selections with rankings
        // IMPORTANT: q4_rank_cb[] only contains checked checkboxes, but q4_rank_num[] contains ALL ranking inputs
        // We need to match them by the option index (checkbox value), not array position
        // The ranking number input at position X corresponds to checkbox at position X in the form
        // So we need to check all 8 possible positions and match checked ones with their rankings
        $ranked_items = array();
        
        // Iterate through all possible option indices (0-7)
        // Check if that checkbox was checked, and get its ranking from the corresponding position
        for ($form_position = 0; $form_position < count($q4_options); $form_position++) {
            // Check if this checkbox was checked (its value should be in q4_rank_cb array)
            // Try both string and integer comparison
            $is_checked = in_array((string)$form_position, $q4_cb, true) || in_array($form_position, $q4_cb, true);
            
            if ($is_checked && isset($q4_num[$form_position]) && !empty($q4_num[$form_position])) {
                $option_index = $form_position;
                $rank = intval($q4_num[$form_position]);
                
                // Validate rank is between 1-3 and option index is valid
                if ($rank >= 1 && $rank <= 3 && isset($q4_options[$option_index])) {
                    // Get translated label (same as displayed in form)
                    $translated_label = function_exists('pll__') ? pll__($q4_options[$option_index]) : $q4_options[$option_index];
                    $ranked_items[] = array(
                        'rank' => $rank,
                        'index' => $option_index,
                        'label' => $translated_label
                    );
                }
            }
        }
        
        // Sort by rank (1, 2, 3) to get ordered list
        usort($ranked_items, function($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        
        // Save as ordered array (just the selected items in order: rank 1, 2, 3)
        // ACF Repeater requires numeric keys (0, 1, 2) and each row as associative array
        // Using specific field names to prevent conflicts
        // Save translated label (as displayed to user) and original English key for reference
        $q4_ranked = array();
        foreach ($ranked_items as $item) {
            // Ensure label is translated (in case translation wasn't applied earlier)
            $label = $item['label'];
            if (function_exists('pll__')) {
                // If label is English, translate it; if already translated, pll__ returns original
                $original_key = isset($q4_options[$item['index']]) ? $q4_options[$item['index']] : $label;
                $label = pll__($original_key);
            }
            $q4_ranked[] = array(
                'q4_item_index' => $item['index'],
                'q4_item_label' => $label
            );
        }
    }
    
    $q5 = isset($_POST['q5_other_reforms']) ? sanitize_textarea_field(wp_unslash($_POST['q5_other_reforms'])) : '';
    $q6 = isset($_POST['q6_trust']) ? sanitize_text_field(wp_unslash($_POST['q6_trust'])) : '';
    
    // Process Q7 Ranking - combine checkbox selections with rankings
    $q7_cb = isset($_POST['q7_rank_cb']) && is_array($_POST['q7_rank_cb']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['q7_rank_cb'])) : array();
    $q7_num = isset($_POST['q7_rank_num']) && is_array($_POST['q7_rank_num']) ? array_map('sanitize_text_field', array_map('wp_unslash', $_POST['q7_rank_num'])) : array();
    
    // Process Q7 ranking into ordered array (rank 1, 2, 3)
    $q7_ranked = array();
    // Changed condition: q7_num can have more items than q7_cb (all form inputs vs only checked)
    if (!empty($q7_cb) && !empty($q7_num)) {
        $q7_options = array(
            'Health',
            'Education',
            'Energy and electricity',
            'Water, waste and sanitation',
            'Public transport and road safety',
            'Employment and labor market',
            'Banking, finance and insurance',
            'Housing and real estate',
            'Justice and public security',
            'Environment and natural resources',
        );
        
        // Combine selections with rankings
        // IMPORTANT: q7_rank_cb[] only contains checked checkboxes, but q7_rank_num[] contains ALL ranking inputs
        // We need to match them by the option index (checkbox value), not array position
        // The ranking number input at position X corresponds to checkbox at position X in the form
        // So we need to check all 10 possible positions and match checked ones with their rankings
        $ranked_items = array();
        
        // Iterate through all possible option indices (0-9)
        // Check if that checkbox was checked, and get its ranking from the corresponding position
        for ($form_position = 0; $form_position < count($q7_options); $form_position++) {
            // Check if this checkbox was checked (its value should be in q7_rank_cb array)
            // Try both string and integer comparison
            $is_checked = in_array((string)$form_position, $q7_cb, true) || in_array($form_position, $q7_cb, true);
            
            if ($is_checked && isset($q7_num[$form_position]) && !empty($q7_num[$form_position])) {
                $option_index = $form_position;
                $rank = intval($q7_num[$form_position]);
                
                // Validate rank is between 1-3 and option index is valid
                if ($rank >= 1 && $rank <= 3 && isset($q7_options[$option_index])) {
                    // Get translated label (same as displayed in form)
                    $translated_label = function_exists('pll__') ? pll__($q7_options[$option_index]) : $q7_options[$option_index];
                    $ranked_items[] = array(
                        'rank' => $rank,
                        'index' => $option_index,
                        'label' => $translated_label
                    );
                }
            }
        }
        
        // Sort by rank (1, 2, 3) to get ordered list
        usort($ranked_items, function($a, $b) {
            return $a['rank'] - $b['rank'];
        });
        
        // Save as ordered array (just the selected items in order: rank 1, 2, 3)
        // ACF Repeater requires numeric keys (0, 1, 2) and each row as associative array
        // Using specific field names to prevent conflicts
        // Save translated label (as displayed to user) and original English key for reference
        $q7_ranked = array();
        foreach ($ranked_items as $item) {
            // Ensure label is translated (in case translation wasn't applied earlier)
            $label = $item['label'];
            if (function_exists('pll__')) {
                // If label is English, translate it; if already translated, pll__ returns original
                $original_key = isset($q7_options[$item['index']]) ? $q7_options[$item['index']] : $label;
                $label = pll__($original_key);
            }
            $q7_ranked[] = array(
                'q7_item_index' => $item['index'],
                'q7_item_label' => $label
            );
        }
    }
    $q8 = isset($_POST['q8_other_sectors']) ? sanitize_textarea_field(wp_unslash($_POST['q8_other_sectors'])) : '';
    $q9 = isset($_POST['q9_nationality']) ? sanitize_text_field(wp_unslash($_POST['q9_nationality'])) : '';
    $q10 = isset($_POST['q10_residence']) ? sanitize_text_field(wp_unslash($_POST['q10_residence'])) : '';
    $q11 = isset($_POST['q11_governorate']) ? sanitize_text_field(wp_unslash($_POST['q11_governorate'])) : '';
    $q12 = isset($_POST['q12_region_abroad']) ? sanitize_text_field(wp_unslash($_POST['q12_region_abroad'])) : '';
    $q13 = isset($_POST['q13_age']) ? sanitize_text_field(wp_unslash($_POST['q13_age'])) : '';
    $q14 = isset($_POST['q14_gender']) ? sanitize_text_field(wp_unslash($_POST['q14_gender'])) : '';
    $q15 = isset($_POST['q15_disability']) ? sanitize_text_field(wp_unslash($_POST['q15_disability'])) : '';
    $q16 = isset($_POST['q16_income']) ? sanitize_text_field(wp_unslash($_POST['q16_income'])) : '';
    $q17 = isset($_POST['q17_employed']) ? sanitize_text_field(wp_unslash($_POST['q17_employed'])) : '';
    $q17_sector = isset($_POST['q17_employment_sector']) ? sanitize_text_field(wp_unslash($_POST['q17_employment_sector'])) : '';
    $q17_status = isset($_POST['q17_employment_status']) ? sanitize_text_field(wp_unslash($_POST['q17_employment_status'])) : '';

    if (empty($q9) || empty($q13) || empty($q14) || empty($q15) || empty($q17)) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if (!$q1_has_not_any && (empty($q2) || count($q3_matrix) !== 6)) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if ($q9 === 'lebanese') {
        if (empty($q10)) {
            wp_send_json_error(['message' => $required_msg]);
            return;
        }
        if ($q10 === 'lebanon' && empty($q11)) {
            wp_send_json_error(['message' => $required_msg]);
            return;
        }
        if ($q10 === 'abroad' && empty($q12)) {
            wp_send_json_error(['message' => $required_msg]);
            return;
        }
    }
    if ($q17 === 'yes' && empty($q17_sector)) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }
    if ($q17 === 'no' && empty($q17_status)) {
        wp_send_json_error(['message' => $required_msg]);
        return;
    }

    $post_data = array(
        'post_type'   => 'citizen_survey_sub',
        'post_status' => 'publish',
        'post_title'  => 'Citizen Survey 2030 - ' . date_i18n('Y-m-d H:i'),
        'post_content' => '',
    );
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

    $save_field('verification_code', $verification_code);
    $save_field('q1_institutions', $q1);
    $save_field('q2_satisfaction', $q2);
    
    // Save Q3 matrix as ACF Repeater field
    if (function_exists('update_field')) {
        update_field('q3_matrix', $q3_matrix, $post_id);
    } else {
        // Fallback: save as serialized post meta
        update_post_meta($post_id, 'q3_matrix', $q3_matrix);
    }
    // Save Q4 ranking data - save ordered list as main field, keep raw data for reference
    // Use update_field directly for repeater fields to ensure proper ACF format
    if (function_exists('update_field') && !empty($q4_ranked)) {
        // Ensure data is properly formatted for ACF repeater
        // ACF expects: array(0 => array('field_name' => 'value'), 1 => array(...))
        // Using specific field names: q4_item_index and q4_item_label
        $q4_formatted = array();
        foreach ($q4_ranked as $row) {
            $q4_formatted[] = array(
                'q4_item_index' => isset($row['q4_item_index']) ? intval($row['q4_item_index']) : (isset($row['index']) ? intval($row['index']) : 0),
                'q4_item_label' => isset($row['q4_item_label']) ? sanitize_text_field($row['q4_item_label']) : (isset($row['label']) ? sanitize_text_field($row['label']) : '')
            );
        }
        update_field('q4_ranked', $q4_formatted, $post_id);
    } elseif (!empty($q4_ranked)) {
        update_post_meta($post_id, 'q4_ranked', $q4_ranked);
    }
    $save_field('q4_rank_cb', $q4_cb); // Keep raw checkbox data for reference
    $save_field('q4_rank_num', $q4_num); // Keep raw ranking numbers for reference
    $save_field('q5_other_reforms', $q5);
    $save_field('q6_trust', $q6);
    
    // Save Q7 ranking data - save ordered list as main field, keep raw data for reference
    // Use update_field directly for repeater fields to ensure proper ACF format
    if (function_exists('update_field') && !empty($q7_ranked)) {
        // Ensure data is properly formatted for ACF repeater
        // ACF expects: array(0 => array('field_name' => 'value'), 1 => array(...))
        // Using specific field names: q7_item_index and q7_item_label
        $q7_formatted = array();
        foreach ($q7_ranked as $row) {
            $q7_formatted[] = array(
                'q7_item_index' => isset($row['q7_item_index']) ? intval($row['q7_item_index']) : (isset($row['index']) ? intval($row['index']) : 0),
                'q7_item_label' => isset($row['q7_item_label']) ? sanitize_text_field($row['q7_item_label']) : (isset($row['label']) ? sanitize_text_field($row['label']) : '')
            );
        }
        update_field('q7_ranked', $q7_formatted, $post_id);
    } elseif (!empty($q7_ranked)) {
        update_post_meta($post_id, 'q7_ranked', $q7_ranked);
    }
    $save_field('q7_rank_cb', $q7_cb); // Keep raw checkbox data for reference
    $save_field('q7_rank_num', $q7_num); // Keep raw ranking numbers for reference
    $save_field('q8_other_sectors', $q8);
    $save_field('q9_nationality', $q9);
    $save_field('q10_residence', $q10);
    $save_field('q11_governorate', $q11);
    $save_field('q12_region_abroad', $q12);
    $save_field('q13_age', $q13);
    $save_field('q14_gender', $q14);
    $save_field('q15_disability', $q15);
    $save_field('q16_income', $q16);
    $save_field('q17_employed', $q17);
    $save_field('q17_employment_sector', $q17_sector);
    $save_field('q17_employment_status', $q17_status);

    // Save submission language so Submission in BO shows the same language as the sent email
    $submission_lang = omsar_detect_citizen_survey_submission_language($post_id);
    $save_field('submission_lang', $submission_lang);

    // Send email notification
    omsar_send_citizen_survey_form_email(
        $q1, $q1_has_not_any, $q2, $q3_matrix, $q4_ranked, $q5,
        $q6, $q7_ranked, $q8, $q9, $q10, $q11, $q12,
        $q13, $q14, $q15, $q16, $q17, $q17_sector, $q17_status,
        $post_id
    );

    wp_send_json_success(['message' => $success_msg]);
}

/**
 * Detect the language used for the citizen survey submission (same logic as email).
 * Used at submit time to store submission_lang so Submission in BO shows correct language.
 *
 * @param int $post_id Post ID of the submission (optional; used for Polylang post language).
 * @return string Language code, e.g. 'ar' or 'en'.
 */
function omsar_detect_citizen_survey_submission_language($post_id = null) {
    $current_lang = 'en';
    if ($post_id && function_exists('pll_get_post_language')) {
        $post_lang = pll_get_post_language($post_id);
        if ($post_lang) {
            $current_lang = $post_lang;
        }
    }
    if (($current_lang === 'en' || empty($current_lang)) && isset($_SERVER['HTTP_REFERER'])) {
        $referer = isset($_SERVER['HTTP_REFERER']) ? wp_unslash($_SERVER['HTTP_REFERER']) : '';
        if (preg_match('#/(ar|en)/#', $referer, $matches)) {
            $current_lang = $matches[1];
        }
    }
    if (($current_lang === 'en' || empty($current_lang)) && function_exists('pll_current_language')) {
        $detected_lang = pll_current_language();
        if ($detected_lang) {
            $current_lang = $detected_lang;
        }
    }
    if (empty($current_lang) || $current_lang === 'en') {
        $current_lang = is_rtl() ? 'ar' : 'en';
    }
    return $current_lang;
}

/**
 * Build the citizen survey email body HTML from submission data.
 * Used for both sending the email and for the admin "Submission" meta box.
 *
 * @param array $data Associative array with keys: q1, q1_has_not_any, q2, q3_matrix, q4_ranked, q5, q6, q7_ranked, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q17_sector, q17_status. Optional: current_lang.
 * @param int|null $post_id Optional. Used to detect language when current_lang not provided.
 * @return string Full HTML email body (DOCTYPE + html + head + body).
 */
function omsar_build_citizen_survey_email_body($data, $post_id = null) {
    $q1 = isset($data['q1']) ? $data['q1'] : array();
    $q1_has_not_any = !empty($data['q1_has_not_any']);
    $q2 = isset($data['q2']) ? $data['q2'] : '';
    $q3_matrix = isset($data['q3_matrix']) && is_array($data['q3_matrix']) ? $data['q3_matrix'] : array();
    $q4_ranked = isset($data['q4_ranked']) && is_array($data['q4_ranked']) ? $data['q4_ranked'] : array();
    $q5 = isset($data['q5']) ? $data['q5'] : '';
    $q6 = isset($data['q6']) ? $data['q6'] : '';
    $q7_ranked = isset($data['q7_ranked']) && is_array($data['q7_ranked']) ? $data['q7_ranked'] : array();
    $q8 = isset($data['q8']) ? $data['q8'] : '';
    $q9 = isset($data['q9']) ? $data['q9'] : '';
    $q10 = isset($data['q10']) ? $data['q10'] : '';
    $q11 = isset($data['q11']) ? $data['q11'] : '';
    $q12 = isset($data['q12']) ? $data['q12'] : '';
    $q13 = isset($data['q13']) ? $data['q13'] : '';
    $q14 = isset($data['q14']) ? $data['q14'] : '';
    $q15 = isset($data['q15']) ? $data['q15'] : '';
    $q16 = isset($data['q16']) ? $data['q16'] : '';
    $q17 = isset($data['q17']) ? $data['q17'] : '';
    $q17_sector = isset($data['q17_sector']) ? $data['q17_sector'] : '';
    $q17_status = isset($data['q17_status']) ? $data['q17_status'] : '';

    // Prefer stored submission language (so BO Submission matches the sent email). Fall back to runtime detection.
    $current_lang = isset($data['current_lang']) && !empty($data['current_lang']) ? $data['current_lang'] : 'en';
    if ($current_lang === 'en' || empty($current_lang)) {
        if ($post_id && function_exists('pll_get_post_language')) {
            $post_lang = pll_get_post_language($post_id);
            if ($post_lang) {
                $current_lang = $post_lang;
            }
        }
        if (($current_lang === 'en' || empty($current_lang)) && isset($_SERVER['HTTP_REFERER'])) {
            $referer = isset($_SERVER['HTTP_REFERER']) ? wp_unslash($_SERVER['HTTP_REFERER']) : '';
            if (preg_match('#/(ar|en)/#', $referer, $matches)) {
                $current_lang = $matches[1];
            }
        }
        if (($current_lang === 'en' || empty($current_lang)) && function_exists('pll_current_language')) {
            $detected_lang = pll_current_language();
            if ($detected_lang) {
                $current_lang = $detected_lang;
            }
        }
        if (empty($current_lang) || $current_lang === 'en') {
            $current_lang = is_rtl() ? 'ar' : 'en';
        }
    }

    $is_arabic = ($current_lang === 'ar' || is_rtl());
    $dir = $is_arabic ? 'rtl' : 'ltr';
    $text_align = $is_arabic ? 'right' : 'left';

    // Use pll_translate_string($s, $current_lang) so labels match submission language (e.g. Arabic in BO when submission was in Arabic)
    $pll = function_exists('pll__');
    $pll_translate = function_exists('pll_translate_string');
    $t = function ($s) use ($pll, $pll_translate, $current_lang) {
        if ($pll_translate && !empty($current_lang)) {
            return pll_translate_string($s, $current_lang);
        }
        if ($pll) {
            return pll__($s);
        }
        return __($s, 'omsar');
    };

    $email_body = '<!DOCTYPE html><html dir="' . esc_attr($dir) . '" lang="' . esc_attr($current_lang) . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $email_body .= '<style type="text/css">';
    $email_body .= 'body { font-family: ' . ($is_arabic ? "'Almarai', sans-serif" : "'Inter', sans-serif") . '; direction: ' . $dir . '; text-align: ' . $text_align . '; line-height: 1.6; color: #333; margin: 0; padding: 20px; }';
    $email_body .= 'h2, h3, h4, h5 { color: #1a1a1a; margin-top: 20px; margin-bottom: 10px; text-align: ' . $text_align . '; }';
    $email_body .= 'h2 { font-size: 24px; border-bottom: 2px solid #2271b1; padding-bottom: 10px; }';
    $email_body .= 'h3 { font-size: 20px; margin-top: 25px; }';
    $email_body .= 'h4 { font-size: 18px; margin-top: 20px; }';
    $email_body .= 'p { margin: 10px 0; text-align: ' . $text_align . '; }';
    $email_body .= 'ul { margin: ' . ($is_arabic ? '0 20px 10px 0' : '0 0 10px 20px') . '; padding-' . ($is_arabic ? 'right' : 'left') . ': 20px; text-align: ' . $text_align . '; }';
    $email_body .= 'li { margin: 5px 0; text-align: ' . $text_align . '; }';
    $email_body .= 'strong { font-weight: 600; }';
    $email_body .= 'table { width: 100%; border-collapse: collapse; text-align: ' . $text_align . '; direction: ' . $dir . '; }';
    $email_body .= 'th, td { border: 1px solid #ddd; padding: 8px; }';
    $email_body .= 'a { color: #2271b1; text-decoration: none; }';
    $email_body .= 'a:hover { text-decoration: underline; }';
    $email_body .= 'hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }';
    $email_body .= '</style></head><body>';

    $email_body .= '<h2>' . $t('New Citizen Survey Submission') . '</h2>';

    $q1_options = array(
        'Ministry of Interior and Municipalities', 'Ministry of Energy and Water', 'Ministry of Finance',
        'Ministry of Public Health', 'Ministry of Social Affairs', 'Ministry of Education and Higher Education',
        'Ministry of Justice', 'Ministry of Labor', 'Ministry of Information', 'Ministry of Economy and Trade',
        'Ministry of Telecommunications', 'Ministry of Environment', 'Ministry of Culture',
        'Ministry of Foreign Affairs and Emigrants', 'Ministry of Agriculture', 'Ministry of Tourism',
        'Ministry of Youth and Sports', 'Ministry of Industry', 'Ministry of Administrative Development',
        'Ministry of Displaced', 'Ministry of Public Works and Transport', 'Ministry of Defense',
        'Municipalities', 'Governorates', 'Mukhtars', 'I have not interacted with any of the above'
    );

    $q1_labels = array();
    foreach ($q1 as $val) {
        if ($val === 'not_any') {
            $q1_labels[] = $t('I have not interacted with any of the above');
        } else {
            $idx = (int) preg_replace('/^q1_/', '', (string) $val);
            $q1_labels[] = isset($q1_options[$idx]) ? $t($q1_options[$idx]) : esc_html($val);
        }
    }
    $email_body .= '<h3>' . $t('Interaction with Public Institutions') . '</h3>';
    $email_body .= '<p><strong>' . $t('Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)') . ':</strong></p>';
    $email_body .= '<ul><li>' . implode('</li><li>', array_map('esc_html', $q1_labels)) . '</li></ul>';

    if (!$q1_has_not_any) {
        $q2_opts = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
        $q2_idx = (int) str_replace('q2_', '', $q2);
        $q2_label = isset($q2_opts[$q2_idx]) ? $t($q2_opts[$q2_idx]) : $q2;
        $email_body .= '<p><strong>' . $t('Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?') . ':</strong> ' . esc_html($q2_label) . '</p>';

        if (!empty($q3_matrix)) {
            $email_body .= '<h4>' . $t('How satisfied are you with the following aspects?') . '</h4>';
            $email_body .= '<table><thead><tr><th></th><th>' . $t('Response') . '</th></tr></thead><tbody>';
            $q3_cols = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
            foreach ($q3_matrix as $row) {
                $row_name = isset($row['row_name']) ? $row['row_name'] : '';
                $sv = isset($row['selected_value']) ? $row['selected_value'] : '';
                $col_label = isset($q3_cols[(int) $sv]) ? $t($q3_cols[(int) $sv]) : $sv;
                $email_body .= '<tr><td>' . esc_html($t($row_name)) . '</td><td>' . esc_html($col_label) . '</td></tr>';
            }
            $email_body .= '</tbody></table>';
        }

        if (!empty($q4_ranked)) {
            $email_body .= '<h4>' . $t('What are the three most important reforms that would improve your experience?') . '</h4>';
            $email_body .= '<ol style="padding-' . ($is_arabic ? 'right' : 'left') . ': 20px;">';
            foreach ($q4_ranked as $item) {
                $label = isset($item['q4_item_label']) ? $item['q4_item_label'] : (isset($item['label']) ? $item['label'] : '');
                $email_body .= '<li>' . esc_html($label) . '</li>';
            }
            $email_body .= '</ol>';
        }

        if (trim((string) $q5) !== '') {
            $email_body .= '<p><strong>' . $t('Are there any other reforms you would like to suggest?') . ':</strong><br>' . nl2br(esc_html($q5)) . '</p>';
        }
    }

    $email_body .= '<h3>' . $t('Priorities and Reform Perceptions') . '</h3>';
    $q6_opts = array('Full trust', 'High trust', 'Moderate trust', 'Low trust', 'No trust at all');
    $q6_idx = (int) str_replace('q6_', '', $q6);
    $q6_label = isset($q6_opts[$q6_idx]) ? $t($q6_opts[$q6_idx]) : $q6;
    $email_body .= '<p><strong>' . $t('How much trust do you have in public institutions overall?') . ':</strong> ' . esc_html($q6_label) . '</p>';

    if (!empty($q7_ranked)) {
        $email_body .= '<h4>' . $t('Which three sectors should be prioritized?') . '</h4>';
        $email_body .= '<ol style="padding-' . ($is_arabic ? 'right' : 'left') . ': 20px;">';
        foreach ($q7_ranked as $item) {
            $label = isset($item['q7_item_label']) ? $item['q7_item_label'] : (isset($item['label']) ? $item['label'] : '');
            $email_body .= '<li>' . esc_html($label) . '</li>';
        }
        $email_body .= '</ol>';
    }

    if (trim((string) $q8) !== '') {
        $email_body .= '<p><strong>' . $t('Are there other priority sectors not listed above?') . ':</strong><br>' . nl2br(esc_html($q8)) . '</p>';
    }

    $email_body .= '<h3>' . $t('General Information') . '</h3>';
    $email_body .= '<p><strong>' . $t('Are you Lebanese or another nationality?') . ':</strong> ' . esc_html($q9 === 'lebanese' ? $t('Lebanese') : $t('Other nationality')) . '</p>';

    if ($q9 === 'lebanese') {
        $email_body .= '<p><strong>' . $t('Are you residing in Lebanon or abroad?') . ':</strong> ' . esc_html($q10 === 'lebanon' ? $t('Residing in Lebanon') : $t('Residing abroad')) . '</p>';
        if ($q10 === 'lebanon' && $q11) {
            $email_body .= '<p><strong>' . $t('Governorate (if residing in Lebanon)') . ':</strong> ' . esc_html($t($q11)) . '</p>';
        }
        if ($q10 === 'abroad' && $q12) {
            $email_body .= '<p><strong>' . $t('Region (if residing abroad)') . ':</strong> ' . esc_html($t($q12)) . '</p>';
        }
    }

    $q13_opts = array('Under 18', '18–34', '35–49', '50–64', '65+');
    $q13_idx = (int) str_replace('q13_', '', $q13);
    $q13_label = isset($q13_opts[$q13_idx]) ? $t($q13_opts[$q13_idx]) : $q13;
    $email_body .= '<p><strong>' . $t('Age group:') . ':</strong> ' . esc_html($q13_label) . '</p>';
    $email_body .= '<p><strong>' . $t('Gender:') . ':</strong> ' . esc_html($q14 === 'female' ? $t('Female') : $t('Male')) . '</p>';
    $q15_opts = array('yes' => 'Yes', 'no' => 'No', 'prefer_not' => 'Prefer not to answer');
    $q15_label = isset($q15_opts[$q15]) ? $t($q15_opts[$q15]) : $q15;
    $email_body .= '<p><strong>' . $t('Do you have any type of disability?') . ':</strong> ' . esc_html($q15_label) . '</p>';

    if (!empty($q16)) {
        $q16_opts = array(
            '0_4000' => '0–4000$ (0–360,000,000 LBP)',
            '4001_10000' => '4001–10000$ (360,000,001–900,000,000 LBP)',
            '10001_20000' => '10001–20000$ (900,000,001–1,800,000,000 LBP)',
            '20001_40000' => '20001–40000$ (1,800,000,001–3,600,000,000 LBP)',
            '40001_80000' => '40001–80000$ (3,600,000,001–7,200,000,000 LBP)',
            '80001_150000' => '80001–150000$ (7,200,000,001–13,500,000,000 LBP)',
            'above_150000' => 'More than 13,500,000,000 LBP (more than 150,000$)',
            'prefer_not' => 'Prefer not to answer',
        );
        $q16_label = isset($q16_opts[$q16]) ? $t($q16_opts[$q16]) : $q16;
        $email_body .= '<p><strong>' . $t('Annual income:') . ':</strong> ' . esc_html($q16_label) . '</p>';
    }
    $email_body .= '<p><strong>' . $t('Are you currently employed?') . ':</strong> ' . esc_html($q17 === 'yes' ? $t('Yes') : $t('No')) . '</p>';

    if ($q17 === 'yes' && $q17_sector) {
        $sector_opts = array('public_sector' => 'Public sector', 'private_sector' => 'Private sector', 'ngo_sector' => 'NGO sector');
        $sector_label = isset($sector_opts[$q17_sector]) ? $t($sector_opts[$q17_sector]) : $q17_sector;
        $email_body .= '<p><strong>' . $t('Employment sector') . ':</strong> ' . esc_html($sector_label) . '</p>';
    }
    if ($q17 === 'no' && $q17_status) {
        $status_opts = array('Student', 'Homemaker', 'Retired', 'Medical condition', 'Unemployed');
        $status_idx = (int) str_replace('q17_no_', '', $q17_status);
        $status_label = isset($status_opts[$status_idx]) ? $t($status_opts[$status_idx]) : $q17_status;
        $email_body .= '<p><strong>' . $t('Status') . ':</strong> ' . esc_html($status_label) . '</p>';
    }

    $email_body .= '</body></html>';
    return $email_body;
}

/**
 * Get citizen survey submission data from a post (for building email from saved submission).
 *
 * @param int $post_id Post ID of citizen_survey_sub.
 * @return array Data array suitable for omsar_build_citizen_survey_email_body().
 */
function omsar_get_citizen_survey_submission_data($post_id) {
    $get_field = function ($key) use ($post_id) {
        if (function_exists('get_field')) {
            return get_field($key, $post_id);
        }
        return get_post_meta($post_id, $key, true);
    };

    $q1 = $get_field('q1_institutions');
    if (!is_array($q1)) {
        $q1 = array();
    }
    $q1_has_not_any = in_array('not_any', $q1, true);

    $q3_matrix = $get_field('q3_matrix');
    if (!is_array($q3_matrix)) {
        $q3_matrix = array();
    }

    $q4_ranked = $get_field('q4_ranked');
    if (!is_array($q4_ranked)) {
        $q4_ranked = array();
    }

    $q7_ranked = $get_field('q7_ranked');
    if (!is_array($q7_ranked)) {
        $q7_ranked = array();
    }

    $submission_lang = $get_field('submission_lang');
    if (!is_string($submission_lang) || $submission_lang === '') {
        $submission_lang = null;
    }

    return array(
        'q1' => $q1,
        'q1_has_not_any' => $q1_has_not_any,
        'q2' => (string) $get_field('q2_satisfaction'),
        'q3_matrix' => $q3_matrix,
        'q4_ranked' => $q4_ranked,
        'q5' => (string) $get_field('q5_other_reforms'),
        'q6' => (string) $get_field('q6_trust'),
        'q7_ranked' => $q7_ranked,
        'q8' => (string) $get_field('q8_other_sectors'),
        'q9' => (string) $get_field('q9_nationality'),
        'q10' => (string) $get_field('q10_residence'),
        'q11' => (string) $get_field('q11_governorate'),
        'q12' => (string) $get_field('q12_region_abroad'),
        'q13' => (string) $get_field('q13_age'),
        'q14' => (string) $get_field('q14_gender'),
        'q15' => (string) $get_field('q15_disability'),
        'q16' => (string) $get_field('q16_income'),
        'q17' => (string) $get_field('q17_employed'),
        'q17_sector' => (string) $get_field('q17_employment_sector'),
        'q17_status' => (string) $get_field('q17_employment_status'),
        'current_lang' => $submission_lang,
    );
}

/**
 * Send email notification when citizen survey form is submitted.
 * Supports Polylang and RTL/LTR alignment for Arabic/English.
 *
 * @param array $q1 Q1 institutions selected
 * @param bool $q1_has_not_any Whether "not any" was selected for Q1
 * @param string $q2 Q2 satisfaction value
 * @param array $q3_matrix Q3 matrix data
 * @param array $q4_ranked Q4 ranked items
 * @param string $q5 Q5 other reforms text
 * @param string $q6 Q6 trust value
 * @param array $q7_ranked Q7 ranked items
 * @param string $q8 Q8 other sectors text
 * @param string $q9 Q9 nationality
 * @param string $q10 Q10 residence
 * @param string $q11 Q11 governorate
 * @param string $q12 Q12 region abroad
 * @param string $q13 Q13 age
 * @param string $q14 Q14 gender
 * @param string $q15 Q15 disability
 * @param string $q16 Q16 income
 * @param string $q17 Q17 employed
 * @param string $q17_sector Q17 employment sector
 * @param string $q17_status Q17 employment status
 * @param int $post_id Post ID of the created citizen survey submission
 */
function omsar_send_citizen_survey_form_email(
    $q1, $q1_has_not_any, $q2, $q3_matrix, $q4_ranked, $q5,
    $q6, $q7_ranked, $q8, $q9, $q10, $q11, $q12,
    $q13, $q14, $q15, $q16, $q17, $q17_sector, $q17_status,
    $post_id
) {
    $recipient_email = '';
    if (function_exists('get_field')) {
        $recipient_email = get_field('survey_recipient_email', 'option');
    }

    if (empty($recipient_email) || !is_email($recipient_email)) {
        return;
    }

    $pll = function_exists('pll__');
    $t = function ($s) use ($pll) {
        return $pll ? pll__($s) : __($s, 'omsar');
    };
    $subject = sprintf(
        $t('New Citizen Survey Submission from %s'),
        get_bloginfo('name')
    );

    $data = array(
        'q1' => $q1,
        'q1_has_not_any' => $q1_has_not_any,
        'q2' => $q2,
        'q3_matrix' => $q3_matrix,
        'q4_ranked' => $q4_ranked,
        'q5' => $q5,
        'q6' => $q6,
        'q7_ranked' => $q7_ranked,
        'q8' => $q8,
        'q9' => $q9,
        'q10' => $q10,
        'q11' => $q11,
        'q12' => $q12,
        'q13' => $q13,
        'q14' => $q14,
        'q15' => $q15,
        'q16' => $q16,
        'q17' => $q17,
        'q17_sector' => $q17_sector,
        'q17_status' => $q17_status,
    );
    $email_body = omsar_build_citizen_survey_email_body($data, $post_id);

    // $headers = array(
    //     'Content-Type: text/html; charset=UTF-8',
    //     'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    // );
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($recipient_email, $subject, $email_body, $headers);
}

/**
 * Add "Submission" meta box to citizen_survey_sub edit screen.
 * Renders the same HTML as the sent email, generated dynamically from submission data.
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'omsar_citizen_survey_email_preview',
        __('Submission', 'omsar'),
        'omsar_render_citizen_survey_email_preview_meta_box',
        'citizen_survey_sub',
        'normal',
        'default'
    );
});

/**
 * Render the Submission meta box: display email body as real HTML in a read-only iframe.
 *
 * @param \WP_Post $post Current post (citizen_survey_sub).
 */
function omsar_render_citizen_survey_email_preview_meta_box($post) {
    $data = omsar_get_citizen_survey_submission_data($post->ID);
    $html = omsar_build_citizen_survey_email_body($data, $post->ID);

    echo '<div class="omsar-email-preview-wrapper" style="margin: -6px -12px -12px -12px;">';
    echo '<iframe id="omsar-email-preview-iframe" ';
    echo 'class="omsar-email-preview-iframe" ';
    echo 'style="width:100%; min-height:200px; height:200px; border:0; display:block; background:#fff; overflow:visible;" ';
    echo 'title="' . esc_attr__('Submission', 'omsar') . '" ';
    echo 'sandbox="allow-same-origin" ';
    echo 'srcdoc="' . esc_attr($html) . '">';
    echo '</iframe>';
    echo '<script>(function(){ var f=document.getElementById("omsar-email-preview-iframe"); function fit(){ try{ var d=f.contentDocument||f.contentWindow.document; var h=Math.max(d.body.scrollHeight,d.documentElement.scrollHeight,d.body.offsetHeight,d.documentElement.offsetHeight); f.style.height=Math.max(h,200)+"px"; }catch(e){} } f.onload=fit; if(f.contentDocument&&f.contentDocument.readyState==="complete") fit(); })();</script>';
    echo '</div>';
}

/**
 * Example: How to retrieve and display Q3 Matrix data from saved posts
 * 
 * $post_id = 123; // Your post ID
 * 
 * // Method 1: Using ACF get_field() (recommended)
 * $q3_matrix = get_field('q3_matrix', $post_id);
 * 
 * if ($q3_matrix && is_array($q3_matrix)) {
 *     foreach ($q3_matrix as $row) {
 *         $row_name = isset($row['row_name']) ? $row['row_name'] : '';
 *         $selected_value = isset($row['selected_value']) ? $row['selected_value'] : '';
 *         
 *         // Map selected_value to label
 *         $value_labels = array(
 *             '0' => 'Very satisfied',
 *             '1' => 'Somewhat satisfied',
 *             '2' => 'Somewhat dissatisfied',
 *             '3' => 'Very dissatisfied',
 *             '4' => 'Not applicable / I don\'t know'
 *         );
 *         $selected_label = isset($value_labels[$selected_value]) ? $value_labels[$selected_value] : '';
 *         
 *         echo $row_name . ': ' . $selected_label . '<br>';
 *     }
 * }
 * 
 * // Method 2: Using get_post_meta() (fallback if ACF not available)
 * $q3_matrix = get_post_meta($post_id, 'q3_matrix', true);
 * // Then process the same way as above
 */

/**
 * Debug function to check if Q4/Q7 ranked data is saved correctly
 * Add this temporarily to verify data structure
 * 
 * function omsar_debug_q4_ranked($post_id) {
 *     $q4_ranked = get_field('q4_ranked', $post_id);
 *     error_log('Q4 Ranked Data: ' . print_r($q4_ranked, true));
 *     
 *     // Also check raw post meta
 *     $q4_raw = get_post_meta($post_id, 'q4_ranked', true);
 *     error_log('Q4 Raw Meta: ' . print_r($q4_raw, true));
 * }
 * add_action('save_post_citizen_survey_sub', 'omsar_debug_q4_ranked', 20);
 */

/**
 * Example: How to retrieve and display Q4 and Q7 Ranking data
 * 
 * $post_id = 123; // Your post ID
 * 
 * // Retrieve Q4 ranked items (ordered: rank 1, 2, 3)
 * $q4_ranked = get_field('q4_ranked', $post_id);
 * 
 * if ($q4_ranked && is_array($q4_ranked)) {
 *     echo '<h3>Q4 - Top 3 Reforms (in order):</h3>';
 *     foreach ($q4_ranked as $position => $item) {
 *         $rank = $position + 1; // Position 0 = Rank 1, Position 1 = Rank 2, etc.
 *         $label = isset($item['q4_item_label']) ? $item['q4_item_label'] : (isset($item['label']) ? $item['label'] : '');
 *         $index = isset($item['q4_item_index']) ? $item['q4_item_index'] : (isset($item['index']) ? $item['index'] : '');
 *         echo $rank . '. ' . $label . ' (Index: ' . $index . ')<br>';
 *     }
 * }
 * 
 * // Retrieve Q7 ranked items (ordered: rank 1, 2, 3)
 * $q7_ranked = get_field('q7_ranked', $post_id);
 * 
 * if ($q7_ranked && is_array($q7_ranked)) {
 *     echo '<h3>Q7 - Top 3 Priority Sectors (in order):</h3>';
 *     foreach ($q7_ranked as $position => $item) {
 *         $rank = $position + 1; // Position 0 = Rank 1, Position 1 = Rank 2, etc.
 *         $label = isset($item['q7_item_label']) ? $item['q7_item_label'] : (isset($item['label']) ? $item['label'] : '');
 *         $index = isset($item['q7_item_index']) ? $item['q7_item_index'] : (isset($item['index']) ? $item['index'] : '');
 *         echo $rank . '. ' . $label . ' (Index: ' . $index . ')<br>';
 *     }
 * }
 * 
 * // The data structure saved:
 * // $q4_ranked = array(
 * //     array('q4_item_index' => 0, 'q4_item_label' => 'Providing clear and accessible information'),  // Rank 1
 * //     array('q4_item_index' => 2, 'q4_item_label' => 'Simplifying procedures and reducing timeframes'), // Rank 2
 * //     array('q4_item_index' => 5, 'q4_item_label' => 'Enabling complaint submission and quick response')  // Rank 3
 * // );
 */

/**
 * Add Export to CSV button to citizen_survey_sub list screen
 */
add_filter('views_edit-citizen_survey_sub', 'omsar_add_citizen_survey_sub_export_button');
function omsar_add_citizen_survey_sub_export_button($views) {
    if (!current_user_can('edit_posts')) {
        return $views;
    }
    $export_url = admin_url('admin.php');
    $export_url = add_query_arg('page', 'export_citizen_survey_sub', $export_url);
    $export_url = add_query_arg('_wpnonce', wp_create_nonce('export_citizen_survey_sub'), $export_url);
    $views['export'] = '<a href="' . esc_url($export_url) . '" class="button" style="margin-left: 10px;">' . __('Export to CSV', 'omsar') . '</a>';
    return $views;
}

/**
 * Handle export request for citizen_survey_sub
 */
add_action('admin_init', 'omsar_handle_citizen_survey_sub_export');
function omsar_handle_citizen_survey_sub_export() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'export_citizen_survey_sub') {
        return;
    }
    if (!current_user_can('edit_posts')) {
        wp_die(__('You do not have permission to export Citizen Survey submissions.', 'omsar'));
    }
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'export_citizen_survey_sub')) {
        wp_die(__('Security check failed.', 'omsar'));
    }
    omsar_export_citizen_survey_sub_csv();
}

/**
 * Register hidden admin page for export URL
 */
add_action('admin_menu', 'omsar_add_citizen_survey_sub_export_page');
function omsar_add_citizen_survey_sub_export_page() {
    add_submenu_page(
        null,
        __('Export Citizen Survey Submissions', 'omsar'),
        __('Export Citizen Survey Submissions', 'omsar'),
        'edit_posts',
        'export_citizen_survey_sub',
        '__return_empty_string'
    );
}

/**
 * Export citizen_survey_sub posts to CSV with human-readable labels
 */
function omsar_export_citizen_survey_sub_csv() {
    while (ob_get_level()) {
        ob_end_clean();
    }
    @ini_set('display_errors', 0);
    @set_time_limit(0);

    // Translation helper: same as email/preview – use submission language so CSV matches question language
    $pll_translate = function_exists('pll_translate_string');
    $pll = function_exists('pll__');
    $t = function ($s, $lang) use ($pll_translate, $pll) {
        if ($pll_translate && !empty($lang)) {
            return pll_translate_string($s, $lang);
        }
        if ($pll) {
            return pll__($s);
        }
        return __($s, 'omsar');
    };

    $q1_options = array(
        'Ministry of Interior and Municipalities', 'Ministry of Energy and Water', 'Ministry of Finance',
        'Ministry of Public Health', 'Ministry of Social Affairs', 'Ministry of Education and Higher Education',
        'Ministry of Justice', 'Ministry of Labor', 'Ministry of Information', 'Ministry of Economy and Trade',
        'Ministry of Telecommunications', 'Ministry of Environment', 'Ministry of Culture',
        'Ministry of Foreign Affairs and Emigrants', 'Ministry of Agriculture', 'Ministry of Tourism',
        'Ministry of Youth and Sports', 'Ministry of Industry', 'Ministry of Administrative Development',
        'Ministry of Displaced', 'Ministry of Public Works and Transport', 'Ministry of Defense',
        'Municipalities', 'Governorates', 'Mukhtars', 'I have not interacted with any of the above'
    );
    $q2_opts = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
    $q3_cols = array('Very satisfied', 'Somewhat satisfied', 'Somewhat dissatisfied', 'Very dissatisfied', 'Not applicable / I don\'t know');
    $q6_opts = array('Full trust', 'High trust', 'Moderate trust', 'Low trust', 'No trust at all');
    $q13_opts = array('Under 18', '18–34', '35–49', '50–64', '65+');
    $q15_opts = array('yes' => 'Yes', 'no' => 'No', 'prefer_not' => 'Prefer not to answer');
    $q16_opts = array(
        '0_4000' => '0–4000$ (0–360,000,000 LBP)', '4001_10000' => '4001–10000$ (360,000,001–900,000,000 LBP)',
        '10001_20000' => '10001–20000$ (900,000,001–1,800,000,000 LBP)', '20001_40000' => '20001–40000$ (1,800,000,001–3,600,000,000 LBP)',
        '40001_80000' => '40001–80000$ (3,600,000,001–7,200,000,000 LBP)', '80001_150000' => '80001–150000$ (7,200,000,001–13,500,000,000 LBP)',
        'above_150000' => 'More than 13,500,000,000 LBP (more than 150,000$)', 'prefer_not' => 'Prefer not to answer',
    );
    $sector_opts = array('public_sector' => 'Public sector', 'private_sector' => 'Private sector', 'ngo_sector' => 'NGO sector');
    $status_opts = array('Student', 'Homemaker', 'Retired', 'Medical condition', 'Unemployed');

    // Column headers in Arabic to match submission language (use pll_translate_string with 'ar')
    $h = function ($s) use ($pll_translate) {
        return ($pll_translate ? pll_translate_string($s, 'ar') : __($s, 'omsar'));
    };
    $headers = array(
        'ID',
        $h('Date'),
        $h('Which of the following public institutions have you interacted with during the past 12 months? (You may select more than one)'),
        $h('Overall, how satisfied are you with the performance of the institutions you interacted with in the past 12 months?'),
        $h('Clarity of required procedures'),
        $h('Ease of completing the service'),
        $h('Processing time'),
        $h('Cost of service'),
        $h('Staff cooperation'),
        $h('Accessibility to responsible officials when needed'),
        $h('What are the three most important reforms that would improve your experience?') . ' (1)',
        $h('What are the three most important reforms that would improve your experience?') . ' (2)',
        $h('What are the three most important reforms that would improve your experience?') . ' (3)',
        $h('Are there any other reforms you would like to suggest?'),
        $h('How much trust do you have in public institutions overall?'),
        $h('Which three sectors should be prioritized?') . ' (1)',
        $h('Which three sectors should be prioritized?') . ' (2)',
        $h('Which three sectors should be prioritized?') . ' (3)',
        $h('Are there other priority sectors not listed above?'),
        $h('Are you Lebanese or another nationality?'),
        $h('Are you residing in Lebanon or abroad?'),
        $h('Governorate (if residing in Lebanon)'),
        $h('Region (if residing abroad)'),
        $h('Age group:'),
        $h('Gender:'),
        $h('Do you have any type of disability?'),
        $h('Annual income:'),
        $h('Are you currently employed?'),
        $h('Employment sector'),
        $h('Status'),
    );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="citizen_survey_submissions_' . date('Y-m-d_His') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, $headers);

    $posts_per_page = 500;
    $paged = 1;

    do {
        $query = new WP_Query(array(
            'post_type'      => 'citizen_survey_sub',
            'post_status'    => 'any',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ));

        if (!$query->have_posts()) {
            break;
        }

        foreach ($query->posts as $post_id) {
            $data = omsar_get_citizen_survey_submission_data($post_id);

            // Use submission language so labels match the same translations as the survey questions
            $lang = isset($data['current_lang']) && $data['current_lang'] !== '' ? $data['current_lang'] : 'en';

            $q1_labels = array();
            foreach ($data['q1'] as $val) {
                if ($val === 'not_any') {
                    $q1_labels[] = $t('I have not interacted with any of the above', $lang);
                } else {
                    $idx = (int) preg_replace('/^q1_/', '', (string) $val);
                    $q1_labels[] = isset($q1_options[$idx]) ? $t($q1_options[$idx], $lang) : $val;
                }
            }
            $q1_str = implode('; ', $q1_labels);

            $q2_idx = (int) str_replace('q2_', '', (string) $data['q2']);
            $q2_str = isset($q2_opts[$q2_idx]) ? $t($q2_opts[$q2_idx], $lang) : $data['q2'];

            $q3_rows = array('', '', '', '', '', '');
            foreach ($data['q3_matrix'] as $i => $row) {
                $sv = isset($row['selected_value']) ? $row['selected_value'] : '';
                $q3_rows[$i] = isset($q3_cols[(int) $sv]) ? $t($q3_cols[(int) $sv], $lang) : $sv;
            }

            // Q4/Q7 labels are already stored in submission language at submit time
            $q4_1 = $q4_2 = $q4_3 = '';
            if (!empty($data['q4_ranked'])) {
                $q4_1 = isset($data['q4_ranked'][0]['q4_item_label']) ? $data['q4_ranked'][0]['q4_item_label'] : (isset($data['q4_ranked'][0]['label']) ? $data['q4_ranked'][0]['label'] : '');
                $q4_2 = isset($data['q4_ranked'][1]['q4_item_label']) ? $data['q4_ranked'][1]['q4_item_label'] : (isset($data['q4_ranked'][1]['label']) ? $data['q4_ranked'][1]['label'] : '');
                $q4_3 = isset($data['q4_ranked'][2]['q4_item_label']) ? $data['q4_ranked'][2]['q4_item_label'] : (isset($data['q4_ranked'][2]['label']) ? $data['q4_ranked'][2]['label'] : '');
            }

            $q6_idx = (int) str_replace('q6_', '', (string) $data['q6']);
            $q6_str = isset($q6_opts[$q6_idx]) ? $t($q6_opts[$q6_idx], $lang) : $data['q6'];

            $q7_1 = $q7_2 = $q7_3 = '';
            if (!empty($data['q7_ranked'])) {
                $q7_1 = isset($data['q7_ranked'][0]['q7_item_label']) ? $data['q7_ranked'][0]['q7_item_label'] : (isset($data['q7_ranked'][0]['label']) ? $data['q7_ranked'][0]['label'] : '');
                $q7_2 = isset($data['q7_ranked'][1]['q7_item_label']) ? $data['q7_ranked'][1]['q7_item_label'] : (isset($data['q7_ranked'][1]['label']) ? $data['q7_ranked'][1]['label'] : '');
                $q7_3 = isset($data['q7_ranked'][2]['q7_item_label']) ? $data['q7_ranked'][2]['q7_item_label'] : (isset($data['q7_ranked'][2]['label']) ? $data['q7_ranked'][2]['label'] : '');
            }

            $q13_idx = (int) str_replace('q13_', '', (string) $data['q13']);
            $q13_str = isset($q13_opts[$q13_idx]) ? $t($q13_opts[$q13_idx], $lang) : $data['q13'];
            $q14_str = $data['q14'] === 'female' ? $t('Female', $lang) : $t('Male', $lang);
            $q15_str = isset($q15_opts[$data['q15']]) ? $t($q15_opts[$data['q15']], $lang) : $data['q15'];
            $q16_str = isset($q16_opts[$data['q16']]) ? $t($q16_opts[$data['q16']], $lang) : $data['q16'];
            $q17_str = $data['q17'] === 'yes' ? $t('Yes', $lang) : $t('No', $lang);
            $q17_sector_str = isset($sector_opts[$data['q17_sector']]) ? $t($sector_opts[$data['q17_sector']], $lang) : $data['q17_sector'];
            $q17_status_idx = (int) str_replace('q17_no_', '', (string) $data['q17_status']);
            $q17_status_str = isset($status_opts[$q17_status_idx]) ? $t($status_opts[$q17_status_idx], $lang) : $data['q17_status'];

            $q3_1 = isset($q3_rows[0]) ? $q3_rows[0] : '';
            $q3_2 = isset($q3_rows[1]) ? $q3_rows[1] : '';
            $q3_3 = isset($q3_rows[2]) ? $q3_rows[2] : '';
            $q3_4 = isset($q3_rows[3]) ? $q3_rows[3] : '';
            $q3_5 = isset($q3_rows[4]) ? $q3_rows[4] : '';
            $q3_6 = isset($q3_rows[5]) ? $q3_rows[5] : '';

            $q9_str = $data['q9'] === 'lebanese' ? $t('Lebanese', $lang) : $t('Other nationality', $lang);
            $q10_str = $data['q10'] === 'lebanon' ? $t('Residing in Lebanon', $lang) : $t('Residing abroad', $lang);
            $q11_str = $data['q11'] !== '' ? $t($data['q11'], $lang) : '';
            $q12_str = $data['q12'];

            fputcsv($output, array(
                $post_id,
                get_the_date('Y-m-d H:i:s', $post_id),
                $q1_str,
                $q2_str,
                $q3_1, $q3_2, $q3_3, $q3_4, $q3_5, $q3_6,
                $q4_1, $q4_2, $q4_3,
                $data['q5'],
                $q6_str,
                $q7_1, $q7_2, $q7_3,
                $data['q8'],
                $q9_str, $q10_str, $q11_str, $q12_str,
                $q13_str, $q14_str, $q15_str, $q16_str, $q17_str,
                $q17_sector_str, $q17_status_str,
            ));
        }

        wp_reset_postdata();
        $paged++;
    } while (true);

    fclose($output);
    exit;
}
