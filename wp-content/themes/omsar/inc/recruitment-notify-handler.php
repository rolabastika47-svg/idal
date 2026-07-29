<?php
/**
 * Recruitment Notify Me Handler
 * 
 * Handles recruitment notification subscriptions via AJAX
 * and sends notifications when recruitments become open
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Get recruitment recipient email from option (for From address and admin notifications)
 *
 * @return string|null Email address or null if not set/invalid
 */
function omsar_get_recruitment_recipient_email() {
    if (function_exists('get_field')) {
        $email = get_field('recruitment_recipient_email', 'option');
        if (!empty($email) && is_email($email)) {
            return $email;
        }
    }
    return null;
}

/**
 * Send notification email to recruitment_recipient_email when someone subscribes or applies
 *
 * @param int    $recruitment_id Recruitment post ID
 * @param string $user_email      Subscriber/applicant email
 * @param string $action          'notify' (subscribed to be notified) or 'apply' (clicked apply)
 * @return bool True if email was sent
 */
function omsar_send_recruitment_admin_notification($recruitment_id, $user_email, $action = 'notify') {
    $to = omsar_get_recruitment_recipient_email();
    if (empty($to)) {
        $to = get_option('admin_email');
    }
    if (empty($to) || !is_email($to)) {
        return false;
    }
    $recruitment = get_post($recruitment_id);
    if (!$recruitment || $recruitment->post_type !== 'recruitments') {
        return false;
    }
    $job_title = get_the_title($recruitment_id);
    $site_name = get_bloginfo('name');
    $from_email = get_option('admin_email');
    if (function_exists('get_field')) {
        $opt = get_field('recruitment_recipient_email', 'option');
        if (!empty($opt) && is_email($opt)) {
            $from_email = $opt;
        }
    }
    // $headers = array(
    //     'Content-Type: text/html; charset=UTF-8',
    //     'From: ' . $site_name . ' <' . $from_email . '>'
    // );
    $headers = array('Content-Type: text/html; charset=UTF-8');

    if ($action === 'notify') {
        $subject = sprintf(function_exists('pll__') ? pll__('[%s] New subscription: notify when recruitment opens') : __('[%s] New subscription: notify when recruitment opens', 'omsar'), $site_name);
        $body = '<p>' . (function_exists('pll__') ? pll__('Someone subscribed to be notified when this recruitment opens.') : __('Someone subscribed to be notified when this recruitment opens.', 'omsar')) . '</p>';
    } else {
        $subject = sprintf(function_exists('pll__') ? pll__('[%s] Someone clicked Apply for a recruitment') : __('[%s] Someone clicked Apply for a recruitment', 'omsar'), $site_name);
        $body = '<p>' . (function_exists('pll__') ? pll__('Someone clicked Apply for the following recruitment.') : __('Someone clicked Apply for the following recruitment.', 'omsar')) . '</p>';
    }
    $recruitment_label = function_exists('pll__') ? pll__('Recruitment / Job') : __('Recruitment / Job', 'omsar');
    $user_email_label = function_exists('pll__') ? pll__('User email') : __('User email', 'omsar');
    $date_label = function_exists('pll__') ? pll__('Date') : __('Date', 'omsar');
    $body .= '<p><strong>' . esc_html($recruitment_label) . ':</strong> ' . esc_html($job_title) . '</p>';
    $body .= '<p><strong>' . esc_html($user_email_label) . ':</strong> ' . esc_html($user_email) . '</p>';
    $body .= '<p><strong>' . esc_html($date_label) . ':</strong> ' . esc_html(current_time(get_option('date_format') . ' ' . get_option('time_format'))) . '</p>';
    $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial, sans-serif; line-height: 1.6;">' . $body . '</body></html>';
    return wp_mail($to, $subject, $body,$headers);
}

/**
 * AJAX handler for recruitment notification subscription
 */
add_action('wp_ajax_submit_recruitment_notify', 'omsar_handle_recruitment_notify_submission');
add_action('wp_ajax_nopriv_submit_recruitment_notify', 'omsar_handle_recruitment_notify_submission');

/**
 * Reset notification flag when opening date changes
 * This ensures users can be re-notified if dates are changed
 */
add_action('save_post_recruitments', 'omsar_reset_recruitment_notification_on_date_change', 10, 2);
function omsar_reset_recruitment_notification_on_date_change($post_id, $post) {
    // Skip autosaves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Only process published recruitments
    if ($post->post_status !== 'publish') {
        return;
    }
    
    // Get the new opening date
    $new_opening_date = get_field('opening_date', $post_id);
    $new_opening_date_string = '';
    
    if ($new_opening_date) {
        if (is_numeric($new_opening_date) && strlen($new_opening_date) == 8) {
            $new_opening_date_string = $new_opening_date;
        } else {
            $date_obj = DateTime::createFromFormat('Y-m-d', $new_opening_date);
            if (!$date_obj) {
                $date_obj = DateTime::createFromFormat('d/m/Y', $new_opening_date);
            }
            if ($date_obj) {
                $new_opening_date_string = $date_obj->format('Ymd');
            }
        }
    }
    
    // Get the stored opening date from when notifications were last sent
    $notify_sent_date = get_post_meta($post_id, '_recruitment_notify_sent_date', true);
    
    // If opening date has changed, reset notification flags
    if ($new_opening_date_string && $notify_sent_date && $new_opening_date_string !== $notify_sent_date) {
        delete_post_meta($post_id, '_recruitment_notify_sent');
        delete_post_meta($post_id, '_recruitment_notify_sent_date');
    }
}

/**
 * Handle recruitment notification subscription submission
 */
function omsar_handle_recruitment_notify_submission() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'recruitment_notify_nonce')) {
        wp_send_json_error(array(
            'message' => __('Security check failed. Please refresh the page and try again.', 'omsar')
        ));
        return;
    }

    // Sanitize and validate form data
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $recruitment_id = isset($_POST['recruitment_id']) ? intval($_POST['recruitment_id']) : 0;

    // Validate required fields
    if (empty($email) || !is_email($email)) {
        $error_message = function_exists('pll__') 
            ? pll__('A valid email address is required.') 
            : __('A valid email address is required.', 'omsar');
        wp_send_json_error(array(
            'message' => $error_message
        ));
        return;
    }

    if (empty($recruitment_id) || !get_post($recruitment_id)) {
        $error_message = function_exists('pll__') 
            ? pll__('Invalid recruitment. Please try again.') 
            : __('Invalid recruitment. Please try again.', 'omsar');
        wp_send_json_error(array(
            'message' => $error_message
        ));
        return;
    }

    // Check if recruitment post type is correct
    $recruitment_post = get_post($recruitment_id);
    if (!$recruitment_post || $recruitment_post->post_type !== 'recruitments') {
        $error_message = function_exists('pll__') 
            ? pll__('Invalid recruitment. Please try again.') 
            : __('Invalid recruitment. Please try again.', 'omsar');
        wp_send_json_error(array(
            'message' => $error_message
        ));
        return;
    }

    // Check for duplicate email for the same recruitment
    $existing_subscription = omsar_check_duplicate_subscription($email, $recruitment_id);
    if ($existing_subscription) {
        $error_message = function_exists('pll__') 
            ? pll__('You are already subscribed to notifications for this recruitment.') 
            : __('You are already subscribed to notifications for this recruitment.', 'omsar');
        wp_send_json_error(array(
            'message' => $error_message
        ));
        return;
    }

    // Create post title from email and recruitment title
    $recruitment_title = get_the_title($recruitment_id);
    $post_title = sprintf(
        __('Subscription: %s - %s', 'omsar'),
        $email,
        $recruitment_title
    );

    // Insert the subscription post
    $post_data = array(
        'post_title'    => $post_title,
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'recruitment_sub',
        'post_author'   => 1,
    );

    $post_id = wp_insert_post($post_data);

    // Check if post was created successfully
    if (is_wp_error($post_id)) {
        $error_message = function_exists('pll__') 
            ? pll__('Failed to save your subscription. Please try again later.') 
            : __('Failed to save your subscription. Please try again later.', 'omsar');
        wp_send_json_error(array(
            'message' => $error_message
        ));
        return;
    }

    // Save subscription data to post meta
    update_post_meta($post_id, 'email', $email);
    update_post_meta($post_id, 'recruitment_id', $recruitment_id);
    update_post_meta($post_id, 'subscription_date', current_time('mysql'));

    // Save using ACF if available
    if (function_exists('update_field')) {
        update_field('email', $email, $post_id);
        update_field('recruitment_id', $recruitment_id, $post_id);
        update_field('subscription_date', current_time('mysql'), $post_id);
    }

    // Notify recruitment_recipient_email that someone subscribed
    omsar_send_recruitment_admin_notification($recruitment_id, $email, 'notify');

    // Send success response
    $success_message = function_exists('pll__') 
        ? pll__('Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.') 
        : __('Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 'omsar');
    
    wp_send_json_success(array(
        'message' => $success_message,
        'post_id' => $post_id
    ));
}

/**
 * Check for duplicate subscription
 * 
 * @param string $email Email address
 * @param int $recruitment_id Recruitment post ID
 * @return int|false Post ID if duplicate exists, false otherwise
 */
function omsar_check_duplicate_subscription($email, $recruitment_id) {
    $args = array(
        'post_type' => 'recruitment_sub',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'email',
                'value' => $email,
                'compare' => '='
            ),
            array(
                'key' => 'recruitment_id',
                'value' => $recruitment_id,
                'compare' => '='
            )
        )
    );

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $duplicate_id = get_the_ID();
        wp_reset_postdata();
        return $duplicate_id;
    }
    
    wp_reset_postdata();
    return false;
}

/**
 * Get recruitment status based on opening_date and closing_date
 * 
 * @param int $recruitment_id Recruitment post ID
 * @return string Status: 'upcoming', 'open', or 'closed'
 */
function omsar_get_recruitment_status($recruitment_id) {
    if (function_exists('omsar_get_recruitment_status_from_acf')) {
        return omsar_get_recruitment_status_from_acf($recruitment_id);
    }

    $opening_date = get_field('opening_date', $recruitment_id);
    $closing_date = get_field('closing_date', $recruitment_id);

    if (function_exists('omsar_compute_recruitment_status_from_date_values')) {
        return omsar_compute_recruitment_status_from_date_values($opening_date, $closing_date);
    }

    return 'upcoming';
}

/**
 * Check all recruitments and notify subscribers for those that became open
 * This function is called by the daily cron job
 */
function omsar_check_recruitments_and_notify() {
    // Get all published recruitments
    $args = array(
        'post_type' => 'recruitments',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    
    $query = new WP_Query($args);
    $notified_count = 0;
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $recruitment_id = get_the_ID();
            
            // Get current status
            $current_status = omsar_get_recruitment_status($recruitment_id);
            
            // Only process if status is 'open'
            if ($current_status === 'open') {
                // Get opening date to verify it has been reached
                $opening_date = get_field('opening_date', $recruitment_id);
                $opening_date_obj = null;
                $opening_date_string = '';
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                
                if ($opening_date) {
                    if (is_numeric($opening_date) && strlen($opening_date) == 8) {
                        $opening_date_obj = DateTime::createFromFormat('Ymd', $opening_date);
                        $opening_date_string = $opening_date; // Store as Ymd format
                    } else {
                        $opening_date_obj = DateTime::createFromFormat('Y-m-d', $opening_date);
                        if (!$opening_date_obj) {
                            $opening_date_obj = DateTime::createFromFormat('d/m/Y', $opening_date);
                        }
                        if ($opening_date_obj) {
                            $opening_date_string = $opening_date_obj->format('Ymd'); // Normalize to Ymd
                        }
                    }
                    if ($opening_date_obj) {
                        $opening_date_obj->setTime(0, 0, 0);
                    }
                }
                
                // Verify opening date has been reached (or no opening date set)
                $opening_date_reached = true;
                if ($opening_date_obj) {
                    $opening_date_reached = ($today >= $opening_date_obj);
                }
                
                // Check if notifications were already sent for this opening date
                $notify_sent_date = get_post_meta($recruitment_id, '_recruitment_notify_sent_date', true);
                $notify_sent_flag = get_post_meta($recruitment_id, '_recruitment_notify_sent', true);
                $should_notify = false;
                
                if (empty($notify_sent_date) && empty($notify_sent_flag)) {
                    // Never notified - safe to notify
                    $should_notify = true;
                } elseif ($opening_date_string && $notify_sent_date && $notify_sent_date !== $opening_date_string) {
                    // Opening date has changed since last notification
                    // Reset the flags to allow re-notification for the new date
                    delete_post_meta($recruitment_id, '_recruitment_notify_sent');
                    delete_post_meta($recruitment_id, '_recruitment_notify_sent_date');
                    $should_notify = true;
                } elseif ($opening_date_string && $notify_sent_date === $opening_date_string && $notify_sent_flag) {
                    // Already notified for this exact opening date - skip
                    $should_notify = false;
                } elseif (empty($opening_date_string) && empty($notify_sent_flag)) {
                    // No opening date set and never notified - safe to notify
                    $should_notify = true;
                }
                
                // If opening date has been reached and we should notify, notify subscribers
                if ($opening_date_reached && $should_notify) {
                    $result = omsar_notify_recruitment_subscribers($recruitment_id);
                    if ($result) {
                        // Mark as notified with the current opening date to prevent duplicate emails
                        update_post_meta($recruitment_id, '_recruitment_notify_sent', current_time('mysql'));
                        if ($opening_date_string) {
                            update_post_meta($recruitment_id, '_recruitment_notify_sent_date', $opening_date_string);
                        } else {
                            // If no opening date, use a special marker to indicate notification was sent
                            update_post_meta($recruitment_id, '_recruitment_notify_sent_date', 'no_date');
                        }
                        $notified_count++;
                    }
                }
            }
        }
    }
    
    wp_reset_postdata();
    
    return $notified_count;
}

/**
 * Get all subscribers for a recruitment and send notification emails
 * 
 * @param int $recruitment_id Recruitment post ID
 * @return bool True if emails were sent, false otherwise
 */
function omsar_notify_recruitment_subscribers($recruitment_id) {
    // Get recruitment details
    $recruitment = get_post($recruitment_id);
    if (!$recruitment || $recruitment->post_type !== 'recruitments') {
        return false;
    }
    
    // Get job titles: English from post title, Arabic from custom field
    $job_title_en = get_the_title($recruitment_id);
    $job_title_ar = get_field('job_title_ar', $recruitment_id);
    // Fallback to English title if Arabic is not available
    if (empty($job_title_ar)) {
        $job_title_ar = $job_title_en;
    }
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';

    $recruitment_link = get_permalink($recruitment_id);
    // $job_link = get_field('job_link', $recruitment_id);
    $job_link = $current_lang === 'ar' ? get_field( 'job_link_ar', $recruitment_id ) : get_field( 'job_link', $recruitment_id );

    
    // Get all subscribers for this recruitment
    $args = array(
        'post_type' => 'recruitment_sub',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'recruitment_id',
                'value' => $recruitment_id,
                'compare' => '='
            )
        )
    );
    
    $subscribers_query = new WP_Query($args);
    
    if (!$subscribers_query->have_posts()) {
        wp_reset_postdata();
        return false;
    }
    
    $emails_sent = 0;
    
    // Send email to each subscriber
    while ($subscribers_query->have_posts()) {
        $subscribers_query->the_post();
        $subscriber_id = get_the_ID();
        $subscriber_email = get_post_meta($subscriber_id, 'email', true);
        
        if (!empty($subscriber_email) && is_email($subscriber_email)) {
            $sent = omsar_send_recruitment_available_email($subscriber_email, $recruitment_id, $job_title_en, $job_title_ar, $recruitment_link, $job_link);
            if ($sent) {
                $emails_sent++;
            }
        }
    }
    
    wp_reset_postdata();
    
    return $emails_sent > 0;
}

/**
 * Send email notification when recruitment becomes available
 * 
 * @param string $email Subscriber email address
 * @param int $recruitment_id Recruitment post ID
 * @param string $job_title_en Job title in English (from post title)
 * @param string $job_title_ar Job title in Arabic (from job_title_ar field)
 * @param string $recruitment_link Link to recruitment page
 * @param string $job_link Link to apply (if available)
 * @return bool True if email was sent successfully
 */
function omsar_send_recruitment_available_email($email, $recruitment_id, $job_title_en, $job_title_ar, $recruitment_link, $job_link = '') {
    // Get email templates from Recruitment Email Templates options page
    $email_subject_template = '';
    $email_body_template = '';
    
    if (function_exists('get_field')) {
        // Get from the recruitment-email-templates options page
        $email_subject_template = get_field('recruitment_email_subject', 'option');
        $email_body_template = get_field('recruitment_email_body', 'option');
    }
    
    // Fallback to WordPress options if ACF is not available
    if (empty($email_subject_template)) {
        $email_subject_template = get_option('recruitment_email_subject', '');
    }
    if (empty($email_body_template)) {
        $email_body_template = get_option('recruitment_email_body', '');
    }
    
    // Use default templates if no custom template is set (subject/body translatable via Polylang)
    if (empty($email_subject_template)) {
        $email_subject_template = function_exists('pll__') ? pll__('Application Now Open for {{Job Title}}') : 'Application Now Open for {{Job Title}}';
    }
    
    if (empty($email_body_template)) {
        $email_body_template = 'Dear Candidate,

We are pleased to inform you that applications for the position {{Job Title}} are now open.
You may submit your application online by visiting the following link:
{{Job Link}}

Thank you for your continued interest in joining the public sector. We look forward to receiving your application.

Best regards,
Office of the Minister of State for Administrative Reform

---

تحية طيبة وبعد،

يسرنا إعلامكم بأن باب التقديم على وظيفة {{Job Title}} أصبح مفتوحًا الآن.
يمكنكم التقدم بطلبكم إلكترونيًا عبر الرابط التالي:
{{Job Link}}

نشكركم على اهتمامكم المستمر بالانضمام إلى القطاع العام، وتتطلع إلى استلام طلبكم.

مع فائق الاحترام،
وزارة الدولة لشؤون التنمية الإدارية';
    }
    
    // Use job_link if available, otherwise use recruitment_link
    // Ensure we have a valid link
    $application_link = !empty($job_link) ? $job_link : $recruitment_link;
    if (empty($application_link)) {
        $application_link = get_permalink($recruitment_id);
    }
    
    // Replace variables in subject (English only) - use English title
    $subject = str_replace('{{Job Title}}', esc_html($job_title_en), $email_subject_template);
    
    // Since the email body is from a WYSIWYG editor, it may already contain HTML
    // We need to replace variables while preserving existing HTML formatting
    // Replace {{Job Title Arabic}} first (for Arabic sections), then {{Job Title}} (for English sections)
    // Note: job_title_ar comes from the custom field 'job_title_ar', not the post title
    $job_title_html_ar = '<strong>' . esc_html($job_title_ar) . '</strong>';
    $email_body_template = str_replace('{{Job Title Arabic}}', $job_title_html_ar, $email_body_template);
    
    $job_title_html_en = '<strong>' . esc_html($job_title_en) . '</strong>';
    $email_body_template = str_replace('{{Job Title}}', $job_title_html_en, $email_body_template);
    
    // Replace {{Job Link}} with a clickable HTML link
    $job_link_html = '<a href="' . esc_url($application_link) . '" style="color: #5693ff; text-decoration: underline; font-weight: bold;">' . esc_html($application_link) . '</a>';
    $email_body_template = str_replace('{{Job Link}}', $job_link_html, $email_body_template);
    
    // The email body from WYSIWYG editor already contains HTML, so we can use it directly
    // But we need to ensure it's properly wrapped in email HTML structure
    $email_body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        ' . $email_body_template . '
    </div>
</body>
</html>';
    
    // Set From address: use recruitment_recipient_email option if set, otherwise admin email
    $from_email = get_option('admin_email');
    if (function_exists('get_field')) {
        $option_email = get_field('recruitment_recipient_email', 'option');
        if (!empty($option_email) && is_email($option_email)) {
            $from_email = $option_email;
        }
    }
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . $from_email . '>'
    );
    
    // Send email
    $sent = wp_mail($email, $subject, $email_body, $headers);
    
    return $sent;
}
