<?php
/**
 * Complaint / Inquiry Submission Form Handler
 *
 * Handles submissions via AJAX.
 * Saves a complaint post with mapped meta fields, attempts to email notification, and cleans uploaded files.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('wp_ajax_submit_complaint_inquiry_form', 'omsar_handle_complaint_inquiry_form_submission');
add_action('wp_ajax_nopriv_submit_complaint_inquiry_form', 'omsar_handle_complaint_inquiry_form_submission');

function omsar_handle_complaint_inquiry_form_submission() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'complaint_inquiry_form_nonce')) {
        $msg = function_exists('pll__')
            ? pll__('Security check failed. Please refresh the page and try again.')
            : __('Security check failed. Please refresh the page and try again.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Helper: word count
    $max_words = 200;
    $count_words = function($text) {
        $text = wp_strip_all_tags((string)$text);
        $text = preg_replace('/\s+/u', ' ', trim($text));
        if ($text === '') {
            return 0;
        }
        // Prefer multibyte-friendly split
        $parts = preg_split('/\s+/u', $text);
        return is_array($parts) ? count($parts) : 0;
    };

    // Collect + sanitize inputs
    $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
    $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $address = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
    $preferred_contact = isset($_POST['preferred_contact']) ? sanitize_key($_POST['preferred_contact']) : '';
    $on_behalf = isset($_POST['on_behalf']) ? sanitize_key($_POST['on_behalf']) : '';
    $on_behalf_explanation = isset($_POST['on_behalf_explanation']) ? sanitize_textarea_field($_POST['on_behalf_explanation']) : '';
    
    $inquiry_text = isset($_POST['inquiry_text']) ? sanitize_textarea_field($_POST['inquiry_text']) : '';
    $issue_type = isset($_POST['issue_type']) ? sanitize_key($_POST['issue_type']) : '';
    $issue_type_other = isset($_POST['issue_type_other']) ? sanitize_text_field($_POST['issue_type_other']) : '';
    $complaint_text = isset($_POST['complaint_text']) ? sanitize_textarea_field($_POST['complaint_text']) : '';
    
    $sea_sh_anonymous = isset($_POST['sea_sh_anonymous']) ? sanitize_key($_POST['sea_sh_anonymous']) : '';
    $sea_sh_referral = isset($_POST['sea_sh_referral']) ? sanitize_key($_POST['sea_sh_referral']) : '';
    $sea_sh_info = isset($_POST['sea_sh_info']) ? sanitize_textarea_field($_POST['sea_sh_info']) : '';
    
    $consent_followup = isset($_POST['consent_followup']) ? sanitize_key($_POST['consent_followup']) : '';

    // Validate email format if provided
    if (!empty($email) && !is_email($email)) {
        $msg = function_exists('pll__') ? pll__('Please enter a valid email address.') : __('Please enter a valid email address.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Validate required fields
    if (empty($preferred_contact) || !in_array($preferred_contact, array('email', 'phone', 'no_response'), true)) {
        $msg = function_exists('pll__') ? pll__('Please select your preferred method of contact.') : __('Please select your preferred method of contact.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    if (empty($on_behalf) || !in_array($on_behalf, array('yes', 'no'), true)) {
        $msg = function_exists('pll__') ? pll__('Please indicate if you are submitting on behalf of someone else.') : __('Please indicate if you are submitting on behalf of someone else.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    // Validate on_behalf_explanation if on_behalf is "yes"
    if ($on_behalf === 'yes' && empty(trim($on_behalf_explanation))) {
        $msg = function_exists('pll__') ? pll__('Please provide an explanation.') : __('Please provide an explanation.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    if (empty(trim($inquiry_text))) {
        $msg = function_exists('pll__') ? pll__('Please provide your inquiry.') : __('Please provide your inquiry.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    if (empty($issue_type)) {
        $msg = function_exists('pll__') ? pll__('Please select the type of issue you are reporting.') : __('Please select the type of issue you are reporting.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    if (empty(trim($complaint_text))) {
        $msg = function_exists('pll__') ? pll__('Please provide your complaint.') : __('Please provide your complaint.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }
    
    if (!in_array($consent_followup, array('yes', 'no'), true)) {
        $msg = function_exists('pll__') ? pll__('Please select your consent preference.') : __('Please select your consent preference.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Validate textareas word limits (only if not empty)
    $textareas = array(
        'inquiry_text' => $inquiry_text,
        'complaint_text' => $complaint_text,
        'sea_sh_info' => $sea_sh_info,
        'on_behalf_explanation' => $on_behalf_explanation,
    );
    foreach ($textareas as $field_key => $val) {
        if ($val !== '' && $count_words($val) > $max_words) {
            $msg = function_exists('pll__') ? pll__('Please limit your response to 200 words.') : __('Please limit your response to 200 words.', 'omsar');
            wp_send_json_error(array('message' => $msg, 'field' => $field_key));
            return;
        }
    }

    // Validate issue_type "other" field if needed
    if ($issue_type === 'other' && empty($issue_type_other)) {
        $msg = function_exists('pll__') ? pll__('Please specify the issue type.') : __('Please specify the issue type.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Validate SEA/SH fields if issue_type is sea_sh
    if ($issue_type === 'sea_sh') {
        if (!in_array($sea_sh_anonymous, array('yes', 'no'), true)) {
            $msg = function_exists('pll__') ? pll__('Please select whether you would like to remain anonymous.') : __('Please select whether you would like to remain anonymous.', 'omsar');
            wp_send_json_error(array('message' => $msg));
            return;
        }
        if (!in_array($sea_sh_referral, array('yes', 'no', 'unsure'), true)) {
            $msg = function_exists('pll__') ? pll__('Please select your referral preference.') : __('Please select your referral preference.', 'omsar');
            wp_send_json_error(array('message' => $msg));
            return;
        }
    }

    // If consent is "yes", at least one contact channel is recommended (except for SEA/SH)
    if ($consent_followup === 'yes' && $issue_type !== 'sea_sh' && empty($email) && empty($phone_number)) {
        $msg = function_exists('pll__')
            ? pll__('To allow follow-up, please provide at least an email address or phone number.')
            : __('To allow follow-up, please provide at least an email address or phone number.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Handle uploads (optional; multiple) and attach to complaint post as a gallery
    $uploaded_files = array(); // file paths for wp_mail attachments
    $attachment_ids = array(); // media attachment IDs to save in post meta / ACF gallery
    if (!empty($_FILES['complaint_files']) && isset($_FILES['complaint_files']['name']) && is_array($_FILES['complaint_files']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $allowed_mimes = array(
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $files_count = count($_FILES['complaint_files']['name']);
        for ($i = 0; $i < $files_count; $i++) {
            if (empty($_FILES['complaint_files']['name'][$i])) {
                continue;
            }

            $file = array(
                'name'     => $_FILES['complaint_files']['name'][$i],
                'type'     => $_FILES['complaint_files']['type'][$i],
                'tmp_name' => $_FILES['complaint_files']['tmp_name'][$i],
                'error'    => $_FILES['complaint_files']['error'][$i],
                'size'     => $_FILES['complaint_files']['size'][$i],
            );

            if (!empty($file['error'])) {
                continue;
            }

            // Check file extension against allowed types
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array_keys($allowed_mimes);
            if (empty($file_ext) || !in_array($file_ext, $allowed_extensions, true)) {
                $msg = function_exists('pll__')
                    ? pll__('One or more uploaded files are not allowed.')
                    : __('One or more uploaded files are not allowed.', 'omsar');
                wp_send_json_error(array('message' => $msg));
                return;
            }

            $overrides = array(
                'test_form' => false,
                'mimes' => $allowed_mimes,
            );
            $result = wp_handle_upload($file, $overrides);
            if (isset($result['error'])) {
                $msg = function_exists('pll__')
                    ? pll__('File upload failed. Please try again.')
                    : __('File upload failed. Please try again.', 'omsar');
                wp_send_json_error(array('message' => $msg));
                return;
            }

            // Create media attachment so files are retained and available as a gallery
            $sideload = array(
                'name'     => basename($result['file']),
                'type'     => $result['type'],
                'tmp_name' => $result['file'],
                'error'    => 0,
                'size'     => filesize($result['file']),
            );

            // Temporarily set post ID to 0; will update to real post ID after creation
            $attachment_id = media_handle_sideload($sideload, 0);
            if (!is_wp_error($attachment_id)) {
                $attachment_ids[] = $attachment_id;
                $file_path = get_attached_file($attachment_id);
                if ($file_path && file_exists($file_path)) {
                    $uploaded_files[] = $file_path; // for email attachments
                }
            }
        }
    }

    // Recipient: try ACF option `recipient_email`, else admin_email.
    $recipient_email = get_option('admin_email');
    if (function_exists('get_field')) {
        $opt = get_field('recipient_email', 'option');
        if (!empty($opt) && is_email($opt)) {
            $recipient_email = $opt;
        }
    }

    // Helper function to translate field values
    $translate_value = function($field, $value) {
        $translations = array(
            'preferred_contact' => array(
                'email' => pll__('Email'),
                'phone' => pll__('Phone/WhatsApp'),
                'no_response' => pll__('No response needed'),
            ),
            'on_behalf' => array(
                'yes' => pll__('Yes'),
                'no' => pll__('No'),
            ),
            'issue_type' => array(
                'general_admin' => pll__('General or administrative concern'),
                'conduct_code' => pll__('Conduct or Code of Conduct issue'),
                'working_environment' => pll__('Working environment or interaction issue'),
                'fraud_corruption' => pll__('Fraud or corruption concern'),
                'env_social' => pll__('Environmental or social concern'),
                'procurement' => pll__('Procurement related'),
                'sea_sh' => pll__('SEA/SH complaint (confidential pathway)'),
                'other' => pll__('Other'),
            ),
            'sea_sh_anonymous' => array(
                'yes' => pll__('Yes'),
                'no' => pll__('No'),
            ),
            'sea_sh_referral' => array(
                'yes' => pll__('Yes'),
                'no' => pll__('No'),
                'unsure' => pll__('Unsure'),
            ),
            'consent_followup' => array(
                'yes' => pll__('Yes'),
                'no' => pll__('No'),
            ),
        );

        if (isset($translations[$field][$value])) {
            return $translations[$field][$value];
        }
        return $value;
    };

    // Helper function to get field labels
    $get_label = function($field) {
        $labels = array(
            'full_name' => pll__('Full Name'),
            'phone_number' => pll__('Phone Number'),
            'email' => pll__('Email'),
            'address' => pll__('Address'),
            'preferred_contact' => pll__('Preferred method of contact'),
            'on_behalf' => pll__('Are you submitting on behalf of someone else?'),
            'on_behalf_explanation' => pll__('Explanation'),
            'inquiry_text' => pll__('Inquiry'),
            'issue_type' => pll__('What type of issue are you reporting?'),
            'issue_type_other' => pll__('Please specify'),
            'complaint_text' => pll__('Complaint'),
            'sea_sh_anonymous' => pll__('Would you like to remain anonymous?'),
            'sea_sh_referral' => pll__('Would you like referral to specialized support?'),
            'sea_sh_info' => pll__('Optional information'),
            'consent_followup' => pll__('Do you consent to be contacted for follow-up?'),
        );
            
        return isset($labels[$field]) ? $labels[$field] : $field;
    };

    // Build email (keep content minimal; no IP capture)
    $subject = function_exists('pll__')
        ? pll__('New Complaint / Inquiry Submission')
        : __('New Complaint / Inquiry Submission', 'omsar');

    $lines = array();
    $lines[] = '---';
    if (!empty($full_name)) { $lines[] = $get_label('full_name') . ': ' . $full_name; }
    if (!empty($phone_number)) { $lines[] = $get_label('phone_number') . ': ' . $phone_number; }
    if (!empty($email)) { $lines[] = $get_label('email') . ': ' . $email; }
    if (!empty($address)) { $lines[] = $get_label('address') . ': ' . $address; }
    if (!empty($preferred_contact)) { $lines[] = $get_label('preferred_contact') . ': ' . $translate_value('preferred_contact', $preferred_contact); }
    if (!empty($on_behalf)) { $lines[] = $get_label('on_behalf') . ': ' . $translate_value('on_behalf', $on_behalf); }
    if (!empty($on_behalf_explanation)) { $lines[] = $get_label('on_behalf_explanation') . ': ' . $on_behalf_explanation; }
    $lines[] = '---';
    if (!empty($inquiry_text)) { $lines[] = $get_label('inquiry_text') . ': ' . $inquiry_text; }
    if (!empty($issue_type)) { $lines[] = $get_label('issue_type') . ': ' . $translate_value('issue_type', $issue_type); }
    if (!empty($issue_type_other)) { $lines[] = $get_label('issue_type_other') . ': ' . $issue_type_other; }
    if (!empty($complaint_text)) { $lines[] = $get_label('complaint_text') . ': ' . $complaint_text; }
    $lines[] = '---';
    if ($issue_type === 'sea_sh') {
        if (!empty($sea_sh_anonymous)) { $lines[] = $get_label('sea_sh_anonymous') . ': ' . $translate_value('sea_sh_anonymous', $sea_sh_anonymous); }
        if (!empty($sea_sh_referral)) { $lines[] = $get_label('sea_sh_referral') . ': ' . $translate_value('sea_sh_referral', $sea_sh_referral); }
        if (!empty($sea_sh_info)) { $lines[] = $get_label('sea_sh_info') . ': ' . $sea_sh_info; }
    }
    if (!empty($consent_followup)) { $lines[] = $get_label('consent_followup') . ': ' . $translate_value('consent_followup', $consent_followup); }
    $lines[] = '---';

    // Plain text with line breaks so each field appears on its own line (use text/plain, not text/html).
    $body = implode("\r\n", array_map('sanitize_text_field', $lines));
    $headers = array('Content-Type: text/plain; charset=UTF-8');

    // Create complaint post
    $post_title_parts = array(
        'Complaint/Inquiry',
        date_i18n('Y-m-d H:i'),
    );

    $post_data = array(
        'post_type'   => 'complaint',
        'post_status' => 'publish',
        'post_title'  => implode(' - ', array_filter($post_title_parts)),
        'post_content'=> '',
    );

    $post_id = wp_insert_post($post_data, true);
    if (is_wp_error($post_id)) {
        $msg = function_exists('pll__')
            ? pll__('An error occurred while saving your submission. Please try again.')
            : __('An error occurred while saving your submission. Please try again.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Persist fields as post meta (ACF if available)
    $save_field = function($key, $value) use ($post_id) {
        if (function_exists('update_field')) {
            update_field($key, $value, $post_id);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    };

    // Save all fields
    $save_field('full_name', $full_name);
    $save_field('phone_number', $phone_number);
    $save_field('email', $email);
    $save_field('address', $address);
    $save_field('preferred_contact', $preferred_contact);
    $save_field('on_behalf', $on_behalf);
    $save_field('on_behalf_explanation', $on_behalf_explanation);
    
    $save_field('inquiry_text', $inquiry_text);
    $save_field('issue_type', $issue_type);
    $save_field('issue_type_other', $issue_type_other);
    $save_field('complaint_text', $complaint_text);
    
    // SEA/SH fields (only save if issue_type is sea_sh)
    if ($issue_type === 'sea_sh') {
        $save_field('sea_sh_anonymous', $sea_sh_anonymous);
        $save_field('sea_sh_referral', $sea_sh_referral);
        $save_field('sea_sh_info', $sea_sh_info);
    }
    
    $save_field('consent_followup', $consent_followup);
    
    if (!empty($attachment_ids)) {
        $save_field('complaint_files', $attachment_ids);
        // Re-assign attachments to this post so they appear in media library under the complaint
        foreach ($attachment_ids as $aid) {
            wp_update_post(array(
                'ID'          => $aid,
                'post_parent' => $post_id,
            ));
        }
    }

    // For SEA/SH complaints: only clear personal data when user chose to remain anonymous
    if ($issue_type === 'sea_sh' && $sea_sh_anonymous === 'yes') {
        $save_field('full_name', '');
        $save_field('phone_number', '');
        $save_field('email', '');
        $save_field('address', '');
    }

    // Attempt email notification; do not block success if mail fails
    $sent = wp_mail($recipient_email, $subject, $body, $headers, $uploaded_files);

    // Even if email fails, keep the saved post and return success to the user.
    $success = function_exists('pll__')
        ? pll__('Thank you. Your submission has been received.')
        : __('Thank you. Your submission has been received.', 'omsar');
    wp_send_json_success(array(
        'message' => $success,
        'mail_sent' => $sent,
        'post_id' => $post_id,
    ));
}
