<?php
/**
 * Partnership Form Handler
 * 
 * Handles partnership form submissions and saves them to the partnerships post type
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX handler for partnership form submission
 */
add_action('wp_ajax_submit_partnership_form', 'omsar_handle_partnership_form_submission');
add_action('wp_ajax_nopriv_submit_partnership_form', 'omsar_handle_partnership_form_submission');

function omsar_handle_partnership_form_submission() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'partnership_form_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'omsar')
        ));
        return;
    }

    // Sanitize and validate form data
    $full_name = isset($_POST['fullName']) ? sanitize_text_field($_POST['fullName']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $website = isset($_POST['website']) ? esc_url_raw($_POST['website']) : '';
    $address = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';

    // Validate required fields
    if (empty($full_name)) {
        wp_send_json_error(array(
            'message' => __('Full name is required.', 'omsar')
        ));
        return;
    }

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array(
            'message' => __('A valid email address is required.', 'omsar')
        ));
        return;
    }

    if (empty($phone)) {
        wp_send_json_error(array(
            'message' => __('Phone number is required.', 'omsar')
        ));
        return;
    }

    if (empty($website)) {
        wp_send_json_error(array(
            'message' => __('Website URL is required.', 'omsar')
        ));
        return;
    }

    if (empty($address)) {
        wp_send_json_error(array(
            'message' => __('Address is required.', 'omsar')
        ));
        return;
    }

    if (empty($notes)) {
        wp_send_json_error(array(
            'message' => __('Notes are required.', 'omsar')
        ));
        return;
    }

    // Create post title from name and date
    $post_title = sprintf(
        __('Partnership Inquiry from %s - %s', 'omsar'),
        $full_name,
        current_time('Y-m-d H:i:s')
    );

    // Prepare post content
    $post_content = '<p><strong>' . __('Full Name:', 'omsar') . '</strong> ' . esc_html($full_name) . '</p>';
    $post_content .= '<p><strong>' . __('Email:', 'omsar') . '</strong> ' . esc_html($email) . '</p>';
    $post_content .= '<p><strong>' . __('Phone:', 'omsar') . '</strong> ' . esc_html($phone) . '</p>';
    $post_content .= '<p><strong>' . __('Website:', 'omsar') . '</strong> <a href="' . esc_url($website) . '" target="_blank">' . esc_html($website) . '</a></p>';
    $post_content .= '<p><strong>' . __('Address:', 'omsar') . '</strong> ' . esc_html($address) . '</p>';
    $post_content .= '<p><strong>' . __('Notes:', 'omsar') . '</strong></p>';
    $post_content .= '<p>' . nl2br(esc_html($notes)) . '</p>';

    // Insert the post
    $post_data = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_status'   => 'publish',
        'post_type'     => 'partnerships',
        'post_author'   => 1, // Set to admin user ID or get current user ID
    );

    $post_id = wp_insert_post($post_data);

    // Check if post was created successfully
    if (is_wp_error($post_id)) {
        wp_send_json_error(array(
            'message' => __('Failed to save your submission. Please try again later.', 'omsar')
        ));
        return;
    }

    // Save form fields as custom fields with the same IDs
    // Check if ACF is available and update fields
    if (function_exists('update_field')) {
        update_field('fullName', $full_name, $post_id);
        update_field('email', $email, $post_id);
        update_field('phone', $phone, $post_id);
        update_field('website', $website, $post_id);
        update_field('address', $address, $post_id);
        update_field('notes', $notes, $post_id);
    } else {
        // Fallback to post meta if ACF is not available
        update_post_meta($post_id, 'fullName', $full_name);
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'phone', $phone);
        update_post_meta($post_id, 'website', $website);
        update_post_meta($post_id, 'address', $address);
        update_post_meta($post_id, 'notes', $notes);
    }

    // Send email notification
    omsar_send_partnership_form_email($full_name, $email, $phone, $website, $address, $notes, $post_id);

    // Send success response
    $success_message = function_exists('pll__') ? pll__('Thank you for your partnership inquiry! We will get back to you soon.') : __('Thank you for your partnership inquiry! We will get back to you soon.', 'omsar');
    
    wp_send_json_success(array(
        'message' => $success_message,
        'post_id' => $post_id
    ));
}

/**
 * Send email notification when partnership form is submitted
 * 
 * @param string $full_name Full name of the submitter
 * @param string $email Email address of the submitter
 * @param string $phone Phone number of the submitter
 * @param string $website Website URL
 * @param string $address Address
 * @param string $notes Notes content
 * @param int $post_id Post ID of the created partnership submission
 */
function omsar_send_partnership_form_email($full_name, $email, $phone, $website, $address, $notes, $post_id) {
    // Get recipient email from the partnership page custom field
    $recipient_email = '';
    
    // Find the page that uses the partnership-page.php template
    // Handle Polylang if available - get current language's page
    $partnership_page_id = null;
    $current_lang = '';
    if (function_exists('pll_current_language')) {
        $current_lang = pll_current_language();
    }
    
    $args = array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'templates/partnership-page.php',
        'number' => 1,
        'post_status' => 'publish'
    );
    
    // If Polylang is active, filter by current language
    if (!empty($current_lang) && function_exists('pll_get_post_language')) {
        $partnership_pages = get_pages($args);
        foreach ($partnership_pages as $page) {
            $page_lang = pll_get_post_language($page->ID);
            if ($page_lang === $current_lang) {
                $partnership_page_id = $page->ID;
                break;
            }
        }
    } else {
        // No Polylang, just get the first page
        $partnership_pages = get_pages($args);
        if (!empty($partnership_pages)) {
            $partnership_page_id = $partnership_pages[0]->ID;
        }
    }
    
    // Get recipient_email from the partnership page
    if (!empty($partnership_page_id)) {
        // Try to get recipient_email from ACF field first
        if (function_exists('get_field')) {
            $recipient_email = get_field('recipient_email', $partnership_page_id);
        }
        
        // Fallback to post meta if ACF is not available or field is empty
        if (empty($recipient_email)) {
            $recipient_email = get_post_meta($partnership_page_id, 'recipient_email', true);
        }
    }
    
    // If still no recipient email, try ACF options as fallback
    if (empty($recipient_email) && function_exists('get_field')) {
        $recipient_email = get_field('recipient_email', 'option');
    }
    
    // If no recipient email is set, don't send email
    if (empty($recipient_email) || !is_email($recipient_email)) {
        return;
    }
    
    // Prepare email subject
    $subject = sprintf(
        __('New Partnership Inquiry from %s', 'omsar'),
        get_bloginfo('name')
    );
    
    // Prepare email body
    $email_body = '<html><body>';
    $email_body .= '<h2>' . __('New Partnership Inquiry', 'omsar') . '</h2>';
    $email_body .= '<p><strong>' . __('Full Name:', 'omsar') . '</strong> ' . esc_html($full_name) . '</p>';
    $email_body .= '<p><strong>' . __('Email:', 'omsar') . '</strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
    $email_body .= '<p><strong>' . __('Phone:', 'omsar') . '</strong> ' . esc_html($phone) . '</p>';
    $email_body .= '<p><strong>' . __('Website:', 'omsar') . '</strong> <a href="' . esc_url($website) . '" target="_blank">' . esc_html($website) . '</a></p>';
    $email_body .= '<p><strong>' . __('Address:', 'omsar') . '</strong> ' . esc_html($address) . '</p>';
    $email_body .= '<p><strong>' . __('Notes:', 'omsar') . '</strong></p>';
    $email_body .= '<p>' . nl2br(esc_html($notes)) . '</p>';
    
    $email_body .= '</body></html>';
    
    // Set email headers for HTML email
    // $headers = array(
    //     'Content-Type: text/html; charset=UTF-8',
    //     'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    //     'Reply-To: ' . $full_name . ' <' . $email . '>'
    // );
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send email
    wp_mail($recipient_email, $subject, $email_body,$headers);
}

