<?php
/**
 * Procurement Apply Handler
 *
 * Handles procurement Apply modal submissions via AJAX.
 * Creates a post of type procurement_sub with email and procurement_id.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_submit_procurement_apply', 'omsar_handle_procurement_apply_submission');
add_action('wp_ajax_nopriv_submit_procurement_apply', 'omsar_handle_procurement_apply_submission');

/**
 * Handle procurement Apply form submission
 */
function omsar_handle_procurement_apply_submission() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'procurement_apply_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'omsar')
        ));
        return;
    }

    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $procurement_id = isset($_POST['procurement_id']) ? intval($_POST['procurement_id']) : 0;

    if (empty($email) || !is_email($email)) {
        $msg = function_exists('pll__') ? pll__('A valid email address is required.') : __('A valid email address is required.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    if (empty($procurement_id)) {
        $msg = function_exists('pll__') ? pll__('Invalid procurement. Please try again.') : __('Invalid procurement. Please try again.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    $procurement_post = get_post($procurement_id);
    if (!$procurement_post || $procurement_post->post_type !== 'procurement_notices') {
        $msg = function_exists('pll__') ? pll__('Invalid procurement. Please try again.') : __('Invalid procurement. Please try again.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    // Check if this email has already applied for this procurement
    $existing_applications = get_posts(array(
        'post_type' => 'procurement_sub',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'email',
                'value' => $email,
                'compare' => '='
            ),
            array(
                'key' => 'procurement_id',
                'value' => $procurement_id,
                'compare' => '='
            )
        )
    ));

    if (!empty($existing_applications)) {
        $msg = function_exists('pll__') 
            ? pll__('You have already applied for this procurement notice with this email address.') 
            : __('You have already applied for this procurement notice with this email address.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    $procurement_title = get_the_title($procurement_id);
    $post_title = sprintf(
        __('Application: %s - %s', 'omsar'),
        $email,
        $procurement_title
    );

    $post_data = array(
        'post_title'   => $post_title,
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'procurement_sub',
        'post_author'  => 1,
    );

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        $msg = function_exists('pll__') ? pll__('Failed to save your application. Please try again later.') : __('Failed to save your application. Please try again later.', 'omsar');
        wp_send_json_error(array('message' => $msg));
        return;
    }

    update_post_meta($post_id, 'email', $email);
    update_post_meta($post_id, 'procurement_id', $procurement_id);
    update_post_meta($post_id, 'submission_date', current_time('mysql'));

    if (function_exists('update_field')) {
        update_field('email', $email, $post_id);
        update_field('procurement_id', $procurement_id, $post_id);
        update_field('submission_date', current_time('mysql'), $post_id);
    }

    // Send email notification to procurement_recipient_email if set
    omsar_send_procurement_apply_email($email, $procurement_id, $procurement_title, $post_id);

    $success_msg = function_exists('pll__')
        ? pll__('Thank you. Your application has been submitted successfully.')
        : __('Thank you. Your application has been submitted successfully.', 'omsar');

    wp_send_json_success(array(
        'message' => $success_msg,
        'post_id' => $post_id
    ));
}

/**
 * Send email notification when a procurement application is submitted.
 * Recipient: ACF option 'procurement_recipient_email', else admin_email.
 *
 * @param string $email            Applicant email.
 * @param int    $procurement_id   Procurement notice post ID.
 * @param string $procurement_title Procurement notice title.
 * @param int    $submission_id    The procurement_sub post ID.
 */
function omsar_send_procurement_apply_email($email, $procurement_id, $procurement_title, $submission_id) {
    $recipient_email = get_option('admin_email');
    if (function_exists('get_field')) {
        $opt = get_field('procurement_recipient_email', 'option');
        if (!empty($opt) && is_email($opt)) {
            $recipient_email = $opt;
        }
    }

    $submission_date = get_post_meta($submission_id, 'submission_date', true) ?: current_time('mysql');
    $pll = function_exists('pll__');

    $subject = $pll
        ? sprintf(pll__('New procurement application for: %s'), $procurement_title)
        : sprintf(__('New procurement application for: %s', 'omsar'), $procurement_title);

    $email_body = ($pll ? pll__('New Procurement Application') : __('New Procurement Application', 'omsar')) . "\n\n";
    $email_body .= ($pll ? pll__('A new application has been submitted for the following procurement notice.') : __('A new application has been submitted for the following procurement notice.', 'omsar')) . "\n\n";
    $email_body .= ($pll ? pll__('Applicant email:') : __('Applicant email:', 'omsar')) . ' ' . $email . "\n\n";
    $email_body .= ($pll ? pll__('Submission date:') : __('Submission date:', 'omsar')) . ' ' . $submission_date . "\n\n";

    // Force plain text so the email is not sent as HTML (plugins/WordPress may default to HTML).
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    wp_mail($recipient_email, $subject, $email_body, $headers);
}
