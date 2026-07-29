<?php
/**
 * ACF Field Group: Recruitment Email Templates
 * 
 * Registers ACF options page and field group for customizable recruitment notification email templates
 * Creates a dedicated menu item in WordPress admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF options page for recruitment email templates
 */
function omsar_register_recruitment_email_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title'    => 'Recruitment Email Templates',
            'menu_title'    => 'Recruitment Emails',
            'menu_slug'     => 'recruitment-email-templates',
            'capability'    => 'edit_posts',
            'icon_url'      => 'dashicons-email-alt',
            'position'      => 30,
            'redirect'      => false
        ));
    }
}
add_action('acf/init', 'omsar_register_recruitment_email_options_page');

/**
 * Register ACF field group for recruitment email templates
 * This function will be called via acf/init hook
 */
function omsar_register_recruitment_email_template_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Check if field group already exists to prevent duplicates
    $existing_groups = acf_get_local_field_groups();
    foreach ($existing_groups as $group) {
        if (isset($group['key']) && $group['key'] === 'group_recruitment_email_templates') {
            // Field group already exists, don't register again
            return;
        }
    }
    
    // Default email subject (English only)
    $default_subject = 'Application Now Open for {{Job Title}}';
    
    // Default email body (combined English and Arabic)
    $default_body = 'Dear Candidate,

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
    
    acf_add_local_field_group(array(
        'key' => 'group_recruitment_email_templates',
        'title' => 'Recruitment Email Templates',
        'fields' => array(
            array(
                'key' => 'field_recruitment_email_subject',
                'label' => 'Email Subject',
                'name' => 'recruitment_email_subject',
                'type' => 'text',
                'instructions' => 'Enter the email subject line (English). Use {{Job Title}} as a variable that will be replaced with the actual job title.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => $default_subject,
                'placeholder' => 'Application Now Open for {{Job Title}}',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_recruitment_email_body',
                'label' => 'Email Body',
                'name' => 'recruitment_email_body',
                'type' => 'wysiwyg',
                'instructions' => 'Enter the email body text. You can write both English and Arabic versions here. Use {{Job Title}} for English job title (from post title), {{Job Title Arabic}} for Arabic job title (from job_title_ar custom field), and {{Job Link}} for the application link. Use the text editor to manage text alignment and formatting.',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => $default_body,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'recruitment-email-templates',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Customize the email template sent to candidates when a recruitment position becomes available. The email subject is in English only. The email body can contain both English and Arabic versions - use the text editor to manage alignment and formatting. Use {{Job Title}} and {{Job Link}} as dynamic variables.',
    ));
}

// Register the field group when ACF is ready
add_action('acf/init', 'omsar_register_recruitment_email_template_fields');
