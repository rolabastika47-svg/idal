<?php
/**
 * Contact Form Handler
 * 
 * Handles contact form submissions and saves them to the contact_us_forms post type
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX handler for contact form submission
 */
add_action('wp_ajax_submit_contact_form', 'omsar_handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'omsar_handle_contact_form_submission');

function omsar_handle_contact_form_submission() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'contact_form_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'omsar')
        ));
        return;
    }

    // Sanitize and validate form data
    $full_name = isset($_POST['fullName']) ? sanitize_text_field($_POST['fullName']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

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

    if (empty($message)) {
        wp_send_json_error(array(
            'message' => __('Message is required.', 'omsar')
        ));
        return;
    }

    // Create post title from name and date
    $post_title = sprintf(
        __('Contact Form Submission from %s - %s', 'omsar'),
        $full_name,
        current_time('Y-m-d H:i:s')
    );

    // Prepare post content
    $post_content = '<p><strong>' . __('Full Name:', 'omsar') . '</strong> ' . esc_html($full_name) . '</p>';
    $post_content .= '<p><strong>' . __('Email:', 'omsar') . '</strong> ' . esc_html($email) . '</p>';
    if (!empty($phone)) {
        $post_content .= '<p><strong>' . __('Phone:', 'omsar') . '</strong> ' . esc_html($phone) . '</p>';
    }
    $post_content .= '<p><strong>' . __('Message:', 'omsar') . '</strong></p>';
    $post_content .= '<p>' . nl2br(esc_html($message)) . '</p>';

    // Insert the post
    $post_data = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_status'   => 'publish',
        'post_type'     => 'contact_us_forms',
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

    // Save form fields to ACF fields
    // Check if ACF is available and update fields
    if (function_exists('update_field')) {
        update_field('fullName', $full_name, $post_id);
        update_field('email', $email, $post_id);
        update_field('phone', $phone, $post_id);
        update_field('message', $message, $post_id);
    } else {
        // Fallback to post meta if ACF is not available
        update_post_meta($post_id, 'fullName', $full_name);
        update_post_meta($post_id, 'email', $email);
        update_post_meta($post_id, 'phone', $phone);
        update_post_meta($post_id, 'message', $message);
    }

    // Send email notification
    omsar_send_contact_form_email($full_name, $email, $phone, $message, $post_id);

    // Send success response
    $success_message = function_exists('pll__') ? pll__('Thank you for your submission!') : __('Thank you for your submission!', 'omsar');
    
    wp_send_json_success(array(
        'message' => $success_message,
        'post_id' => $post_id
    ));
}

/**
 * Send email notification when contact form is submitted
 * 
 * @param string $full_name Full name of the submitter
 * @param string $email Email address of the submitter
 * @param string $phone Phone number of the submitter
 * @param string $message Message content
 * @param int $post_id Post ID of the created contact form submission
 */
function omsar_send_contact_form_email($full_name, $email, $phone, $message, $post_id) {
    // Get recipient email from ACF options
    $recipient_email = '';
    if (function_exists('get_field')) {
        $recipient_email = get_field('recipient_email', 'option');
    }
    
    // If no recipient email is set, don't send email
    if (empty($recipient_email) || !is_email($recipient_email)) {
        return;
    }
    
    // Prepare email subject
    $subject = sprintf(
        __('New Contact Form Submission from %s', 'omsar'),
        get_bloginfo('name')
    );
    
    // Prepare email body
    $email_body = '<html><body>';
    $email_body .= '<h2>' . __('New Contact Form Submission', 'omsar') . '</h2>';
    $email_body .= '<p><strong>' . __('Full Name:', 'omsar') . '</strong> ' . esc_html($full_name) . '</p>';
    $email_body .= '<p><strong>' . __('Email:', 'omsar') . '</strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
    
    if (!empty($phone)) {
        $email_body .= '<p><strong>' . __('Phone:', 'omsar') . '</strong> ' . esc_html($phone) . '</p>';
    }
    
    $email_body .= '<p><strong>' . __('Message:', 'omsar') . '</strong></p>';
    $email_body .= '<p>' . nl2br(esc_html($message)) . '</p>';

    $email_body .= '</body></html>';
    
    // Set email headers for HTML email
    // $headers = array(
    //     'Content-Type: text/html; charset=UTF-8',
    //     'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
    //     'Reply-To: ' . $full_name . ' <' . $email . '>'
    // );
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send email
    wp_mail($recipient_email, $subject, $email_body, $headers);
}

