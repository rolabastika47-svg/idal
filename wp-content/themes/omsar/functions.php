<?php
/**
 * OMSAR Theme functions and definitions
 *
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Theme setup
 */

/**
 * OMSAR Theme functions and definitions
 *
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require 'inc/pll_register_strings.php';

/**
 * Suppress error display for REST API and AJAX requests to prevent breaking JSON responses
 * This must run very early to catch all errors
 * Elementor relies heavily on REST API endpoints, so we need to ensure clean JSON responses
 */
// Detect REST API requests early (before WordPress fully loads)
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/wp-json/') !== false || 
    strpos($_SERVER['REQUEST_URI'] ?? '', 'rest_route=') !== false) {
    @ini_set('display_errors', 0);
    // Only log fatal errors, suppress warnings/notices/deprecated
    @error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
}

// Suppress errors for REST API requests (Elementor uses REST API)
if (defined('REST_REQUEST') && REST_REQUEST) {
    @ini_set('display_errors', 0);
    @error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
}

// Suppress errors for AJAX requests
if (defined('DOING_AJAX') && DOING_AJAX) {
    @ini_set('display_errors', 0);
    @error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
}

// Also hook into REST API initialization as backup
add_action('rest_api_init', function() {
    @ini_set('display_errors', 0);
    @error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
}, 1);

// Suppress errors for all admin AJAX requests
add_action('admin_init', function() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        @ini_set('display_errors', 0);
        @error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
    }
}, 1);

// Clean any output before REST API responses
add_filter('rest_pre_serve_request', function($served, $result, $request, $server) {
    // Clear any output that might have been sent
    if (ob_get_level() > 0) {
        ob_clean();
    }
    return $served;
}, 10, 4);

/**
 * Theme setup
 */


add_action('wp_enqueue_scripts', function () {

    // Bootstrap CSS
    $bootstrap_css = get_template_directory() . '/assets/css/bootstrap.min.css';
    wp_enqueue_style('bootstrap-css', get_template_directory_uri() . '/assets/css/bootstrap.min.css', [], file_exists($bootstrap_css) ? filemtime($bootstrap_css) : '5.3.3');

    // Bootstrap Icons
    // $bootstrap_icons_css = get_template_directory() . '/assets/css/bootstrap-icons.min.css';
    // wp_enqueue_style('bootstrap-icons', get_template_directory_uri() . '/assets/css/bootstrap-icons.min.css', ['bootstrap-css'], file_exists($bootstrap_icons_css) ? filemtime($bootstrap_icons_css) : '1.11.3');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', ['bootstrap-css'], '1.11.3');

    
    // Google Fonts - Inter
    $google_inter_css = get_template_directory() . '/assets/css/google-fonts-inter.css';
    wp_enqueue_style('google-inter', get_template_directory_uri() . '/assets/css/google-fonts-inter.css', ['bootstrap-icons'], file_exists($google_inter_css) ? filemtime($google_inter_css) : null);
    
    // Almarai font for Arabic pages
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    if ($current_lang === 'ar' || is_rtl()) {
        $google_almarai_css = get_template_directory() . '/assets/css/google-fonts-almarai.css';
        wp_enqueue_style('google-almarai', get_template_directory_uri() . '/assets/css/google-fonts-almarai.css', ['google-inter'], file_exists($google_almarai_css) ? filemtime($google_almarai_css) : null);
    }

    // Theme CSS files
    $base_css = get_template_directory() . '/assets/css/base.css';
    wp_enqueue_style('base-css', get_template_directory_uri() . '/assets/css/base.css', ['google-inter'], file_exists($base_css) ? filemtime($base_css) : '1.0');

    $hf_css = get_template_directory() . '/assets/css/header-footer.css';
    wp_enqueue_style('header-footer-css', get_template_directory_uri() . '/assets/css/header-footer.css', ['base-css'], file_exists($hf_css) ? filemtime($hf_css) : '1.0');

    $style_css = get_template_directory() . '/assets/css/style.css';
    wp_enqueue_style('main-style-css', get_template_directory_uri() . '/assets/css/style.css', ['header-footer-css'], file_exists($style_css) ? filemtime($style_css) : '1.0');

    // Owl Carousel CSS
    $owl_carousel_css = get_template_directory() . '/assets/css/owl.carousel.min.css';
    wp_enqueue_style('owl-carousel-css', get_template_directory_uri() . '/assets/css/owl.carousel.min.css', ['main-style-css'], file_exists($owl_carousel_css) ? filemtime($owl_carousel_css) : '2.3.4');
    
    $owl_theme_css = get_template_directory() . '/assets/css/owl.theme.default.min.css';
    wp_enqueue_style('owl-theme-css', get_template_directory_uri() . '/assets/css/owl.theme.default.min.css', ['owl-carousel-css'], file_exists($owl_theme_css) ? filemtime($owl_theme_css) : '2.3.4');

    // Theme main style.css
    $styles_css = get_template_directory() . '/style.css';
    wp_enqueue_style('theme-style', get_template_directory_uri() . '/style.css', [], file_exists($styles_css) ? filemtime($styles_css) : '1.0');

    // Enqueue RTL CSS for Arabic pages (using $current_lang already defined above)
    if ($current_lang === 'ar' || is_rtl()) {
        $rtl_css = get_template_directory() . '/assets/css/rtl.css';
        wp_enqueue_style('rtl-css', get_template_directory_uri() . '/assets/css/rtl.css', ['theme-style', 'google-almarai'], file_exists($rtl_css) ? filemtime($rtl_css) : '1.0');
    }

    // banner.css
    $banner_css = get_template_directory() . '/assets/css/banner.css';
    wp_enqueue_style('banner-css', get_template_directory_uri() . '/assets/css/banner.css', ['google-inter'], file_exists($banner_css) ? filemtime($banner_css) : '1.0');
    

    // contact.css
    if (is_page_template('templates/contact-us-page.php')) {
        $contact_css = get_template_directory() . '/assets/css/contact.css';
        wp_enqueue_style('contact-css', get_template_directory_uri() . '/assets/css/contact.css', [], file_exists($contact_css) ? filemtime($contact_css) : '1.0');
        
        // contact.js
        $contact_js = get_template_directory() . '/assets/js/contact.js';
        $success_message = function_exists('pll__') ? pll__('Thank you for your submission!') : __('Thank you for your submission!', 'omsar');
        wp_enqueue_script('contact-js', get_template_directory_uri() . '/assets/js/contact.js', ['jquery'], file_exists($contact_js) ? filemtime($contact_js) : '1.0', true);
        wp_localize_script('contact-js', 'contactData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contact_form_nonce'),
            'successMessage' => esc_js($success_message)
        ));
    }

    // partnership.css and partnership.js
    if (is_page_template('templates/partnership-page.php')) {
        // Also load contact.css since the template uses contact-* classes
        $contact_css = get_template_directory() . '/assets/css/contact.css';
        wp_enqueue_style('contact-css', get_template_directory_uri() . '/assets/css/contact.css', [], file_exists($contact_css) ? filemtime($contact_css) : '1.0');
        
        $partnership_css = get_template_directory() . '/assets/css/partnership.css';
        wp_enqueue_style('partnership-css', get_template_directory_uri() . '/assets/css/partnership.css', ['contact-css'], file_exists($partnership_css) ? filemtime($partnership_css) : '1.0');
        
        // partnership.js
        $partnership_js = get_template_directory() . '/assets/js/partnership.js';
        $success_message = function_exists('pll__') ? pll__('Thank you for your partnership inquiry! We will get back to you soon.') : __('Thank you for your partnership inquiry! We will get back to you soon.', 'omsar');
        wp_enqueue_script('partnership-js', get_template_directory_uri() . '/assets/js/partnership.js', ['jquery'], file_exists($partnership_js) ? filemtime($partnership_js) : '1.0', true);
        wp_localize_script('partnership-js', 'partnershipData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('partnership_form_nonce'),
            'successMessage' => esc_js($success_message)
        ));
    }

    // complaint-inquiry-form.css and complaint-inquiry-form.js
    if (is_page_template('templates/complaint-inquiry-submission-form.php')) {
        $complaint_css = get_template_directory() . '/assets/css/complaint-inquiry-form.css';
        wp_enqueue_style(
            'complaint-inquiry-form-css',
            get_template_directory_uri() . '/assets/css/complaint-inquiry-form.css',
            ['main-style-css'],
            file_exists($complaint_css) ? filemtime($complaint_css) : '1.0'
        );

        $complaint_js = get_template_directory() . '/assets/js/complaint-inquiry-form.js';
        wp_enqueue_script(
            'complaint-inquiry-form-js',
            get_template_directory_uri() . '/assets/js/complaint-inquiry-form.js',
            ['jquery'],
            file_exists($complaint_js) ? filemtime($complaint_js) : '1.0',
            true
        );

        // Localize script for AJAX + UI strings (translated via Polylang where available)
        $success_message = function_exists('pll__')
            ? pll__('Thank you. Your submission has been received.')
            : __('Thank you. Your submission has been received.', 'omsar');
        $generic_error = function_exists('pll__')
            ? pll__('An error occurred. Please try again.')
            : __('An error occurred. Please try again.', 'omsar');
        $word_limit_error = function_exists('pll__')
            ? pll__('Please limit your response to 200 words.')
            : __('Please limit your response to 200 words.', 'omsar');
        $required_fields_error = function_exists('pll__')
            ? pll__('Please fill in all required fields before continuing.')
            : __('Please fill in all required fields before continuing.', 'omsar');
        $please_specify_error = function_exists('pll__')
            ? pll__('Please provide the required information.')
            : __('Please provide the required information.', 'omsar');
        $invalid_email_error = function_exists('pll__')
            ? pll__('Please enter a valid email address.')
            : __('Please enter a valid email address.', 'omsar');

        wp_localize_script('complaint-inquiry-form-js', 'omsarComplaintForm', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('complaint_inquiry_form_nonce'),
            'successMessage' => esc_js($success_message),
            'genericError' => esc_js($generic_error),
            'wordLimitError' => esc_js($word_limit_error),
            'requiredFieldsError' => esc_js($required_fields_error),
            'pleaseSpecifyError' => esc_js($please_specify_error),
            'invalidEmailError' => esc_js($invalid_email_error),
            'maxWords' => 200,
        ));
    }

    // publication-page.css - Load on single publication pages
    if (is_singular('publications')) {
        $publication_css = get_template_directory() . '/assets/css/publication-page.css';
        wp_enqueue_style('publication-page-css', get_template_directory_uri() . '/assets/css/publication-page.css', ['main-style-css'], file_exists($publication_css) ? filemtime($publication_css) : '1.0');
    }

    // procurement-single.css - Load on single procurement notices pages or pages using procurement single page template
    if (is_singular('procurement_notices') || is_page_template('templates/procurement-single-page.php')) {
        $procurement_single_css = get_template_directory() . '/assets/css/procurement-single.css';
        wp_enqueue_style('procurement-single-css', get_template_directory_uri() . '/assets/css/procurement-single.css', ['main-style-css'], file_exists($procurement_single_css) ? filemtime($procurement_single_css) : '1.0');
    }

    // sidebar.css - Load on pages where sidebar might be displayed
    if (is_page() && !is_front_page()) {
        $sidebar_css = get_template_directory() . '/assets/css/sidebar.css';
        wp_enqueue_style('sidebar-css', get_template_directory_uri() . '/assets/css/sidebar.css', ['main-style-css'], file_exists($sidebar_css) ? filemtime($sidebar_css) : '1.0');
    }

    // media.css
    if (is_page_template('templates/news-listing-page.php') || is_page_template('templates/events-listing-page.php') || is_page_template('templates/workshop-listing-page.php') || is_page_template('templates/recruitments-listing-page.php')) {
        $media_css = get_template_directory() . '/assets/css/media.css';
        wp_enqueue_style('media-css', get_template_directory_uri() . '/assets/css/media.css', [], file_exists($media_css) ? filemtime($media_css) : '1.0');
    }

    // ministers-listing.css
    if (is_page_template('templates/ministers-listing-page.php')) {
        $ministers_listing_css = get_template_directory() . '/assets/css/ministers-listing.css';
        wp_enqueue_style('ministers-listing-css', get_template_directory_uri() . '/assets/css/ministers-listing.css', ['main-style-css'], file_exists($ministers_listing_css) ? filemtime($ministers_listing_css) : '1.0');
    }

    // single-minister.css - Load on single minister page template
    if (is_page_template('single-minister.php')) {
        $single_minister_css = get_template_directory() . '/assets/css/single-minister.css';
        wp_enqueue_style('single-minister-css', get_template_directory_uri() . '/assets/css/single-minister.css', ['main-style-css'], file_exists($single_minister_css) ? filemtime($single_minister_css) : '1.0');
    }

    // search.css - Load on all pages (header search is on all pages)
    $search_css = get_template_directory() . '/assets/css/search.css';
    wp_enqueue_style('search-css', get_template_directory_uri() . '/assets/css/search.css', ['main-style-css'], file_exists($search_css) ? filemtime($search_css) : '1.0');

    // 404.css - Load only on 404 error pages
    if (is_404()) {
        $error_404_css = get_template_directory() . '/assets/css/404.css';
        wp_enqueue_style('error-404-css', get_template_directory_uri() . '/assets/css/404.css', ['main-style-css'], file_exists($error_404_css) ? filemtime($error_404_css) : '1.0');
    }

    // Citizen Survey Success page - load survey-form.css (and complaint for .omsar-btn)
    if (is_page_template('templates/citizen-survey-success-page.php')) {
        $complaint_css = get_template_directory() . '/assets/css/complaint-inquiry-form.css';
        if (file_exists($complaint_css)) {
            wp_enqueue_style('complaint-inquiry-form-css', get_template_directory_uri() . '/assets/css/complaint-inquiry-form.css', ['main-style-css'], filemtime($complaint_css));
        }
        $survey_css = get_template_directory() . '/assets/css/survey-form.css';
        if (file_exists($survey_css)) {
            wp_enqueue_style('omsar-survey-success-css', get_template_directory_uri() . '/assets/css/survey-form.css', [file_exists($complaint_css) ? 'complaint-inquiry-form-css' : 'main-style-css'], filemtime($survey_css));
        }
    }

    // Survey Success page (Secured Survey Form) - same success layout and .omsar-btn
    if (is_page_template('templates/survey-success-page.php')) {
        $complaint_css = get_template_directory() . '/assets/css/complaint-inquiry-form.css';
        if (file_exists($complaint_css)) {
            wp_enqueue_style('complaint-inquiry-form-css', get_template_directory_uri() . '/assets/css/complaint-inquiry-form.css', ['main-style-css'], filemtime($complaint_css));
        }
        $survey_css = get_template_directory() . '/assets/css/survey-form.css';
        if (file_exists($survey_css)) {
            wp_enqueue_style('omsar-survey-success-css', get_template_directory_uri() . '/assets/css/survey-form.css', [file_exists($complaint_css) ? 'complaint-inquiry-form-css' : 'main-style-css'], filemtime($survey_css));
        }
    }

    // procurement-notices-widget.css - Load on all pages (widget can appear anywhere)
    // Load after Elementor styles if Elementor is active
    $procurement_widget_css = get_template_directory() . '/assets/css/procurement-notices-widget.css';
    $dependencies = ['main-style-css'];
    if (did_action('elementor/loaded')) {
        $dependencies[] = 'elementor-frontend';
    }
    wp_enqueue_style('procurement-notices-widget-css', get_template_directory_uri() . '/assets/css/procurement-notices-widget.css', $dependencies, file_exists($procurement_widget_css) ? filemtime($procurement_widget_css) : '1.0');

    // knowledge-resources-widget.css - Load on all pages (widget can appear anywhere)
    $knowledge_resources_widget_css = get_template_directory() . '/assets/css/knowledge-resources-widget.css';
    wp_enqueue_style('knowledge-resources-widget-css', get_template_directory_uri() . '/assets/css/knowledge-resources-widget.css', $dependencies, file_exists($knowledge_resources_widget_css) ? filemtime($knowledge_resources_widget_css) : '1.0');

    // Elementor styles - Load on all pages (Elementor handles its own CSS, but we add our custom styles)
    if (did_action('elementor/loaded')) {
        // Check if current page/post is built with Elementor
        $post_id = get_the_ID();
        if ($post_id && class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->db)) {
            try {
                if (\Elementor\Plugin::$instance->db->is_built_with_elementor($post_id)) {
                    $elementor_css = get_template_directory() . '/assets/css/elementor-style.css';
                    wp_enqueue_style('elementor-style-css', get_template_directory_uri() . '/assets/css/elementor-style.css', ['main-style-css'], file_exists($elementor_css) ? filemtime($elementor_css) : '1.0');
                }
            } catch (Exception $e) {
                // Silently fail if Elementor is not ready
            }
        }
    }

    // ---------- JS ----------
    $bootstrap_js = get_template_directory() . '/assets/js/bootstrap.bundle.min.js';
    wp_register_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', [], file_exists($bootstrap_js) ? filemtime($bootstrap_js) : '5.3.3', true);
    wp_enqueue_script('bootstrap-js');

    // Scroll inline JS
    wp_add_inline_script('bootstrap-js', "
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar-scroll');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('bg-white', 'shadow-sm');
                } else {
                    navbar.classList.remove('bg-white', 'shadow-sm');
                }
            }
        });
    ");

    // Don't deregister jQuery if Elementor preview/editor is active to prevent conflicts
    $is_elementor_preview = isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor');
    if (!$is_elementor_preview) {
        wp_deregister_script('jquery');
        $jquery_js = get_template_directory() . '/assets/js/jquery.min.js';
        wp_register_script('jquery', get_template_directory_uri() . '/assets/js/jquery.min.js', ['bootstrap-js'], file_exists($jquery_js) ? filemtime($jquery_js) : '3.6.0', true);
        wp_enqueue_script('jquery');
    }

    $owl_carousel_js = get_template_directory() . '/assets/js/owl.carousel.min.js';
    wp_register_script('owl-carousel-js', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', ['jquery'], file_exists($owl_carousel_js) ? filemtime($owl_carousel_js) : '2.3.4', true);
    wp_enqueue_script('owl-carousel-js');

    // Enqueue Hero Banner Carousel script
    $hero_banner_js = get_template_directory() . '/assets/js/hero-banner.js';
    wp_enqueue_script('hero-banner-js', get_template_directory_uri() . '/assets/js/hero-banner.js', ['jquery', 'owl-carousel-js'], file_exists($hero_banner_js) ? filemtime($hero_banner_js) : '1.0', true);

    $main_js = get_template_directory() . '/assets/js/main.js';
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/main.js', ['owl-carousel-js'], file_exists($main_js) ? filemtime($main_js) : '1.0', true);

    // Sidebar JS - Load on pages where sidebar might be displayed
    if (is_page() && !is_front_page()) {
        $sidebar_js = get_template_directory() . '/assets/js/sidebar.js';
        wp_enqueue_script('sidebar-js', get_template_directory_uri() . '/assets/js/sidebar.js', ['jquery'], file_exists($sidebar_js) ? filemtime($sidebar_js) : '1.0', true);
    }

    // Enqueue AJAX script
    $ajax_js = get_template_directory() . '/assets/js/ajax.js';
    wp_enqueue_script('ajax-js', get_template_directory_uri() . '/assets/js/ajax.js', ['jquery', 'custom-js'], file_exists($ajax_js) ? filemtime($ajax_js) : '1.0', true);

    // Posts by Taxonomy (List / Projects) JS
    if (did_action('elementor/loaded')) {
        // Client-side helpers for taxonomy/projects widgets (search & click handling)
        $posts_taxonomy_search_js = get_template_directory() . '/assets/js/posts-by-taxonomy-search.js';
        wp_enqueue_script('posts-by-taxonomy-search-js', get_template_directory_uri() . '/assets/js/posts-by-taxonomy-search.js', ['jquery'], file_exists($posts_taxonomy_search_js) ? filemtime($posts_taxonomy_search_js) : '1.0', true);
        
        // AJAX Load More / server-side filtering
        $posts_taxonomy_load_more_js = get_template_directory() . '/assets/js/posts-by-taxonomy-load-more.js';
        wp_enqueue_script('posts-by-taxonomy-load-more-js', get_template_directory_uri() . '/assets/js/posts-by-taxonomy-load-more.js', ['jquery'], file_exists($posts_taxonomy_load_more_js) ? filemtime($posts_taxonomy_load_more_js) : '1.0', true);
        
        // Localize script for Load More
        wp_localize_script('posts-by-taxonomy-load-more-js', 'omsarTaxonomyLoadMore', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('omsar_taxonomy_load_more_nonce'),
            'moreText' => function_exists('pll__') ? pll__('more') : __('more', 'omsar'),
        ));
        
        // Style 4 Filters JS
        $style4_filters_js = get_template_directory() . '/assets/js/style4-filters.js';
        wp_enqueue_script('style4-filters-js', get_template_directory_uri() . '/assets/js/style4-filters.js', ['jquery'], file_exists($style4_filters_js) ? filemtime($style4_filters_js) : '1.0', true);
        
        // Recruitment Notify Me JS - Load on all pages (widget can appear anywhere)
        $recruitment_notify_js = get_template_directory() . '/assets/js/recruitment-notify.js';
        wp_enqueue_script('recruitment-notify-js', get_template_directory_uri() . '/assets/js/recruitment-notify.js', ['jquery'], file_exists($recruitment_notify_js) ? filemtime($recruitment_notify_js) : '1.0', true);
        
        // Localize script for Recruitment Notify
        $email_required = function_exists('pll__') ? pll__('Email address is required.') : __('Email address is required.', 'omsar');
        $email_invalid = function_exists('pll__') ? pll__('Please enter a valid email address.') : __('Please enter a valid email address.', 'omsar');
        $success_message = function_exists('pll__') ? pll__('Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.') : __('Thank you for your interest. You will be notified by email as soon as this position becomes available. Please check your junk folder frequently to avoid missing our notifications.', 'omsar');
        $error_generic = function_exists('pll__') ? pll__('An error occurred. Please try again.') : __('An error occurred. Please try again.', 'omsar');
        $error_timeout = function_exists('pll__') ? pll__('Request timed out. Please try again.') : __('Request timed out. Please try again.', 'omsar');
        
        wp_localize_script('recruitment-notify-js', 'recruitmentNotifyData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitment_notify_nonce'),
            'emailRequired' => esc_js($email_required),
            'emailInvalid' => esc_js($email_invalid),
            'successMessage' => esc_js($success_message),
            'errorGeneric' => esc_js($error_generic),
            'errorTimeout' => esc_js($error_timeout),
        ));
    }


    // Localize script for AJAX
    wp_localize_script('ajax-js', 'omsarAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_news_nonce'),
        'eventsNonce' => wp_create_nonce('load_more_events_nonce'),
        'workshopNonce' => wp_create_nonce('load_more_workshop_nonce'),
        'recruitmentsNonce' => wp_create_nonce('load_more_recruitments_nonce'),
        'procurementsNonce' => wp_create_nonce('load_more_procurements_nonce'),
        'knowledgeResourcesNonce' => wp_create_nonce('load_more_knowledge_resources_nonce'),
        'procurementApplyNonce' => wp_create_nonce('procurement_apply_nonce'),
        'procurementApplySuccessMessage' => function_exists('pll__') ? pll__('Thank you. Your application has been submitted successfully.') : __('Thank you. Your application has been submitted successfully.', 'omsar'),
        'procurementApplyEmailRequired' => function_exists('pll__') ? pll__('Please enter your email.') : __('Please enter your email.', 'omsar'),
        'procurementApplyEmailInvalid' => function_exists('pll__') ? pll__('Please enter a valid email address.') : __('Please enter a valid email address.', 'omsar'),
        'procurementApplyErrorGeneric' => function_exists('pll__') ? pll__('An error occurred. Please try again.') : __('An error occurred. Please try again.', 'omsar'),
        'noProcurementsMessage' => function_exists('pll__') ? pll__('No procurement notices are available at this time. Please check back later.') : __('No procurement notices are available at this time. Please check back later.', 'omsar'),
        'krSearchError' => function_exists('pll__') ? pll__('Error loading search results. Please try again.') : __('Error loading search results. Please try again.', 'omsar'),
        'loadingText' => function_exists('pll__') ? pll__('Loading...') : 'Loading...',
        'loadMoreText' => function_exists('pll__') ? pll__('Load More') : 'Load More',
    ));

});


/**
 * Enqueue admin scripts to enforce single selection for publication_category
 */
add_action('admin_enqueue_scripts', 'omsar_enqueue_admin_scripts');

function omsar_enqueue_admin_scripts($hook) {
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
    
    // Only load for publications post type
    if ($post_type === 'publications') {
        // Add inline JavaScript to enforce single selection for taxonomy
        $script = "
        jQuery(document).ready(function($) {
            // Function to enforce single selection for publication_category taxonomy
            function enforceSingleCategoryTaxonomy() {
                // Find the taxonomy metabox
                var taxonomyBox = $('#publication_category_single, #publication_categorydiv, #tagsdiv-publication_category');
                
                if (taxonomyBox.length === 0) {
                    return; // Metabox not found
                }
                
                // Handle checkbox fields in taxonomy metabox - enforce single selection
                var checkboxes = taxonomyBox.find('input[type=\"checkbox\"]');
                if (checkboxes.length > 0) {
                    // Remove existing handlers to prevent duplicates
                    checkboxes.off('change.omsar-single-cat-tax');
                    
                    // Add handler to enforce single selection
                    checkboxes.on('change.omsar-single-cat-tax', function() {
                        var \$this = $(this);
                        if (\$this.is(':checked')) {
                            // Uncheck all other checkboxes in this taxonomy
                            checkboxes.not(\$this).prop('checked', false).trigger('change');
                        }
                    });
                }
                
                // Handle select fields with multiple attribute
                var multiSelect = taxonomyBox.find('select[multiple]');
                if (multiSelect.length > 0) {
                    // Remove multiple attribute
                    multiSelect.removeAttr('multiple');
                    
                    // If multiple options are selected, keep only the first
                    var currentVal = multiSelect.val();
                    if (currentVal && Array.isArray(currentVal) && currentVal.length > 1) {
                        multiSelect.val([currentVal[0]]).trigger('change');
                    }
                    
                    // Prevent multiple selection via change event
                    multiSelect.off('change.omsar-single-cat-tax');
                    multiSelect.on('change.omsar-single-cat-tax', function() {
                        var val = $(this).val();
                        if (val && Array.isArray(val) && val.length > 1) {
                            $(this).val([val[0]]).trigger('change');
                        }
                    });
                }
            }
            
            // Run on page load
            enforceSingleCategoryTaxonomy();
            
            // Run after delays to catch late-loading metaboxes
            setTimeout(enforceSingleCategoryTaxonomy, 500);
            setTimeout(enforceSingleCategoryTaxonomy, 1000);
            setTimeout(enforceSingleCategoryTaxonomy, 2000);
        });
        ";
        
        wp_add_inline_script('jquery', $script);
    }
}

function omsar_load_textdomain() {
    load_theme_textdomain( 'omsar', get_template_directory() . '/languages' );
}
add_action( 'init', 'omsar_load_textdomain', 1 );

function omsar_setup() {
    // Add theme support for title tag (allows WordPress to manage document title)
    add_theme_support('title-tag');
    
    add_theme_support('menus');
    
    // Add Elementor theme support
    add_theme_support('elementor');

    // Add excerpt support for pages
    add_post_type_support('page', 'excerpt');

    // Register menus
    register_nav_menus(array(
        'primary_menu' => __('Primary Menu', 'omsar'),
        // 'topbar_menu'  => __('Top Bar Menu', 'omsar'),
        // 'footer_menu'    => __('Footer Menu', 'omsar'),

    ));
}
add_action('after_setup_theme', 'omsar_setup');

/**
 * Customize document title format to "Site Name - Page Name"
 */
function omsar_document_title($title) {
    // Get site name
    $site_name = get_bloginfo('name');
    
    // Get current page/post title
    $page_title = '';
    if (is_front_page()) {
        // For homepage, just show site name
        return $site_name;
    } elseif (is_singular()) {
        $page_title = get_the_title();
    } elseif (is_archive()) {
        $page_title = get_the_archive_title();
        // Clean up archive title (remove "Archives: " prefix and HTML tags)
        $page_title = str_replace(array('Archives: ', 'Category: ', 'Tag: ', 'Author: '), '', $page_title);
        $page_title = wp_strip_all_tags($page_title);
    } elseif (is_search()) {
        $page_title = sprintf(pll__('Search Results for: %s'), get_search_query());
    } elseif (is_404()) {
        $page_title = __('Page Not Found', 'omsar');
    }
    
    // Format: Site Name - Page Title
    if (!empty($page_title) && $page_title !== $site_name) {
        return $site_name . ' - ' . $page_title;
    } else {
        // If no specific page title, just show site name
        return $site_name;
    }
}
add_filter('document_title', 'omsar_document_title', 10, 1);

/**
 * Customize document title separator
 */
function omsar_document_title_separator($separator) {
    return ' - ';
}
add_filter('document_title_separator', 'omsar_document_title_separator');

// ACF options page (fixed with init hook)
add_action('init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'    => 'Theme Options',
            'menu_title'    => 'Theme Options',
            'menu_slug'     => 'theme-options',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ]);
    }
});

/**
 * Helper function to check if current user has access to breadcrumb settings
 * 
 * @return bool True if user has access, false otherwise
 */
function omsar_user_can_access_breadcrumb_settings() {
    // Super admins and administrators always have access
    if (current_user_can('manage_options')) {
        return true;
    }
    
    // Check user meta for access permission
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    // Get user meta - try both with and without underscore prefix
    $can_access = get_user_meta($user_id, 'can_access_breadcrumb_settings', true);
    
    // If not found, try without underscore (some systems store differently)
    if ($can_access === '' || $can_access === false) {
        $can_access = get_user_meta($user_id, 'can_access_breadcrumb_settings', true);
    }
    
    // Check for various formats: '1', 1, true, or any truthy value
    // Also check if meta exists but is empty string (should be false)
    if ($can_access === '' || $can_access === '0' || $can_access === 0 || $can_access === false || $can_access === null) {
        return false;
    }
    
    // If it's '1', 1, true, or any other truthy value, allow access
    return ($can_access === '1' || $can_access === 1 || $can_access === true || !empty($can_access));
}

/**
 * Add breadcrumb settings page
 * Adds a simple checkbox setting to control breadcrumb display on all posts
 * Always adds as standalone menu item - access is checked in the callback
 */
add_action('admin_menu', 'omsar_add_breadcrumb_settings_page', 20);
function omsar_add_breadcrumb_settings_page() {
    // Always add the menu item - access will be checked when user tries to access the page
    // This ensures menu appears for users with access
    $hook = add_menu_page(
        __('Breadcrumb Settings', 'omsar'),
        __('Breadcrumb Settings', 'omsar'),
        'read', // Basic capability - actual access checked in callback
        'omsar-breadcrumb-settings',
        'omsar_breadcrumb_settings_page_callback',
        'dashicons-list-view', // Icon
        61 // Position (after Appearance which is 60, or after Menus)
    );
    
    // Enqueue styles and scripts for toggle switches
    if ($hook) {
        add_action('admin_print_styles-' . $hook, 'omsar_breadcrumb_settings_admin_styles');
        add_action('admin_print_scripts-' . $hook, 'omsar_breadcrumb_settings_admin_scripts');
    }
}

/**
 * Hide breadcrumb settings menu item for users without access
 * This runs after menu is added to remove it if user doesn't have access
 */
add_action('admin_menu', 'omsar_remove_breadcrumb_settings_menu_for_unauthorized_users', 999);
function omsar_remove_breadcrumb_settings_menu_for_unauthorized_users() {
    // Only remove if user doesn't have access
    if (!omsar_user_can_access_breadcrumb_settings()) {
        // Remove the standalone menu item
        remove_menu_page('omsar-breadcrumb-settings');
    }
}

/**
 * Restrict access to breadcrumb settings page
 */
add_action('admin_init', 'omsar_restrict_breadcrumb_settings_access');
function omsar_restrict_breadcrumb_settings_access() {
    // Only check in admin area
    if (!is_admin()) {
        return;
    }
    
    // Check if we're on the breadcrumb settings page
    if (isset($_GET['page']) && $_GET['page'] === 'omsar-breadcrumb-settings') {
        if (!omsar_user_can_access_breadcrumb_settings()) {
            wp_die(
                __('You are not allowed to access this page.', 'omsar'),
                __('Access Denied', 'omsar'),
                array('response' => 403)
            );
        }
    }
}

/**
 * Enqueue admin styles for toggle switches
 */
function omsar_breadcrumb_settings_admin_styles() {
    ?>
    <style>
        .omsar-toggle-wrapper {
            display: inline-block;
            position: relative;
            margin-right: 10px;
        }
        .omsar-toggle {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin: 0;
        }
        .omsar-toggle input[type="checkbox"] {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .omsar-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
            border: 1px solid #8c8f94;
        }
        .omsar-toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
        .omsar-toggle input:checked + .omsar-toggle-slider {
            background-color: #2271b1;
            border-color: #2271b1;
        }
        .omsar-toggle input:checked + .omsar-toggle-slider:before {
            transform: translateX(26px);
        }
        .omsar-toggle-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .omsar-toggle-text {
            font-weight: 500;
            color: #1d2327;
        }
        .omsar-toggle-text.on {
            color: #2271b1;
        }
        .omsar-toggle-text.off {
            color: #646970;
        }
    </style>
    <?php
}

/**
 * Enqueue admin scripts for toggle switches
 */
function omsar_breadcrumb_settings_admin_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Update toggle text on change
        $('.omsar-toggle input[type="checkbox"]').on('change', function() {
            var $toggle = $(this).closest('.omsar-toggle-wrapper');
            var $text = $toggle.find('.omsar-toggle-text');
            if ($(this).is(':checked')) {
                $text.removeClass('off').addClass('on').text('Yes');
            } else {
                $text.removeClass('on').addClass('off').text('No');
            }
        });
        
        // Initialize toggle text on page load
        $('.omsar-toggle input[type="checkbox"]').each(function() {
            var $toggle = $(this).closest('.omsar-toggle-wrapper');
            var $text = $toggle.find('.omsar-toggle-text');
            if ($(this).is(':checked')) {
                $text.removeClass('off').addClass('on').text('Yes');
            } else {
                $text.removeClass('on').addClass('off').text('No');
            }
        });
    });
    </script>
    <?php
}

/**
 * Settings page callback
 */
function omsar_breadcrumb_settings_page_callback() {
    // Check access using our custom function
    if (!omsar_user_can_access_breadcrumb_settings()) {
        wp_die(__('You are not allowed to access this page.', 'omsar'), __('Access Denied', 'omsar'), array('response' => 403));
    }
    
    $message = '';
    $message_type = 'updated';
    
    // Handle form submission
    if (isset($_POST['omsar_save_breadcrumb_settings']) && check_admin_referer('omsar_breadcrumb_settings_nonce', 'omsar_breadcrumb_settings_nonce')) {
        // Save settings for post type options (news, events, workshops) and post types (projects, former_ministers, publication, knowledge_resources)
        $post_type_options = array('news', 'events', 'workshops');
        $post_types = array('projects', 'former_ministers', 'publication', 'knowledge_resources');

        // Save post type options (news, events, workshops)
        foreach ($post_type_options as $post_type) {
            $option_name = 'omsar_breadcrumb_enable_' . $post_type;
            // Checkbox sends '1' when checked, nothing when unchecked
            // We use a hidden field with value '0' that gets overridden by checkbox when checked
            $enabled = isset($_POST[$option_name]) && $_POST[$option_name] === '1' ? '1' : '0';
            update_option($option_name, $enabled);
            wp_cache_delete($option_name, 'options');
        }

        // Save post types (projects, former_ministers, publication)
        foreach ($post_types as $post_type) {
            $option_name = 'omsar_breadcrumb_enable_' . $post_type;
            $enabled = isset($_POST[$option_name]) && $_POST[$option_name] === '1' ? '1' : '0';
            update_option($option_name, $enabled);
            wp_cache_delete($option_name, 'options');
        }

        // Save back button settings (same content types as breadcrumb)
        foreach ($post_type_options as $post_type) {
            $option_name = 'omsar_back_button_enable_' . $post_type;
            $enabled = isset($_POST[$option_name]) && $_POST[$option_name] === '1' ? '1' : '0';
            update_option($option_name, $enabled);
            wp_cache_delete($option_name, 'options');
        }
        foreach ($post_types as $post_type) {
            $option_name = 'omsar_back_button_enable_' . $post_type;
            $enabled = isset($_POST[$option_name]) && $_POST[$option_name] === '1' ? '1' : '0';
            update_option($option_name, $enabled);
            wp_cache_delete($option_name, 'options');
        }

        $message = __('Settings saved.', 'omsar');
    }
    
    // Get current values - default to '1' (enabled) for backward compatibility
    $post_type_options = array(
        'news' => __('News', 'omsar'),
        'events' => __('Events', 'omsar'),
        'workshops' => __('Workshops', 'omsar')
    );
    
    $post_types = array(
        'projects' => __('Projects', 'omsar'),
        'former_ministers' => __('Former Ministers', 'omsar'),
        'publication' => __('Publication', 'omsar'),
        'knowledge_resources' => __('Knowledge and Resources', 'omsar')
    );
    ?>
    <div class="wrap">
        <h1><?php _e('Breadcrumb Settings', 'omsar'); ?></h1>
        
        <?php if (!empty($message)) : ?>
            <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                <p><?php echo esc_html($message); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <?php wp_nonce_field('omsar_breadcrumb_settings_nonce', 'omsar_breadcrumb_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th colspan="2"><h2><?php _e('Post Type Options (Posts with custom field)', 'omsar'); ?></h2></th>
                </tr>
                <?php foreach ($post_type_options as $post_type_key => $post_type_label) : 
                    $option_name = 'omsar_breadcrumb_enable_' . $post_type_key;
                    $current_value = get_option($option_name, '1');
                    $checked = ($current_value === '1' || $current_value === 1 || $current_value === true) ? 'checked="checked"' : '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($post_type_label); ?></th>
                    <td>
                        <!-- Hidden field ensures value is always sent, even when unchecked -->
                        <input type="hidden" name="<?php echo esc_attr($option_name); ?>" value="0" />
                        <div class="omsar-toggle-wrapper">
                            <label class="omsar-toggle-label">
                                <span class="omsar-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="1" <?php echo $checked; ?> />
                                    <span class="omsar-toggle-slider"></span>
                                </span>
                                <span class="omsar-toggle-text <?php echo $checked ? 'on' : 'off'; ?>">
                                    <?php echo $checked ? 'Yes' : 'No'; ?>
                                </span>
                            </label>
                        </div>
                        <p class="description">
                            <?php printf(__('When enabled, breadcrumbs will be displayed on all single %s posts (posts with post_type_option="%s"). When disabled, breadcrumbs will be hidden on all %s posts.', 'omsar'), strtolower($post_type_label), $post_type_key, strtolower($post_type_label)); ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <tr>
                    <th colspan="2"><h2><?php _e('Custom Post Types', 'omsar'); ?></h2></th>
                </tr>
                <?php foreach ($post_types as $post_type_key => $post_type_label) : 
                    $option_name = 'omsar_breadcrumb_enable_' . $post_type_key;
                    $current_value = get_option($option_name, '1');
                    $checked = ($current_value === '1' || $current_value === 1 || $current_value === true) ? 'checked="checked"' : '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($post_type_label); ?></th>
                    <td>
                        <!-- Hidden field ensures value is always sent, even when unchecked -->
                        <input type="hidden" name="<?php echo esc_attr($option_name); ?>" value="0" />
                        <div class="omsar-toggle-wrapper">
                            <label class="omsar-toggle-label">
                                <span class="omsar-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="1" <?php echo $checked; ?> />
                                    <span class="omsar-toggle-slider"></span>
                                </span>
                                <span class="omsar-toggle-text <?php echo $checked ? 'on' : 'off'; ?>">
                                    <?php echo $checked ? 'Yes' : 'No'; ?>
                                </span>
                            </label>
                        </div>
                        <p class="description">
                            <?php printf(__('When enabled, breadcrumbs will be displayed on all single %s posts (post_type="%s"). When disabled, breadcrumbs will be hidden on all %s posts.', 'omsar'), strtolower($post_type_label), $post_type_key, strtolower($post_type_label)); ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>

                <tr>
                    <th colspan="2"><h2><?php _e('Back Button', 'omsar'); ?></h2></th>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="description" style="margin-bottom: 16px;"><?php _e('When enabled, a "Back" button with a rounded icon appears under the breadcrumb on single pages. Configure per content type below.', 'omsar'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th colspan="2"><h3 style="margin: 0; font-size: 14px;"><?php _e('Post Type Options (Posts with custom field)', 'omsar'); ?></h3></th>
                </tr>
                <?php foreach ($post_type_options as $post_type_key => $post_type_label) :
                    $option_name = 'omsar_back_button_enable_' . $post_type_key;
                    $current_value = get_option($option_name, '1');
                    $checked = ($current_value === '1' || $current_value === 1 || $current_value === true) ? 'checked="checked"' : '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($post_type_label); ?></th>
                    <td>
                        <input type="hidden" name="<?php echo esc_attr($option_name); ?>" value="0" />
                        <div class="omsar-toggle-wrapper">
                            <label class="omsar-toggle-label">
                                <span class="omsar-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="1" <?php echo $checked; ?> />
                                    <span class="omsar-toggle-slider"></span>
                                </span>
                                <span class="omsar-toggle-text <?php echo $checked ? 'on' : 'off'; ?>">
                                    <?php echo $checked ? 'Yes' : 'No'; ?>
                                </span>
                            </label>
                        </div>
                        <p class="description">
                            <?php printf(__('Show back button on single %s posts.', 'omsar'), strtolower($post_type_label)); ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="2"><h3 style="margin: 0; font-size: 14px;"><?php _e('Custom Post Types', 'omsar'); ?></h3></th>
                </tr>
                <?php foreach ($post_types as $post_type_key => $post_type_label) :
                    $option_name = 'omsar_back_button_enable_' . $post_type_key;
                    $current_value = get_option($option_name, '1');
                    $checked = ($current_value === '1' || $current_value === 1 || $current_value === true) ? 'checked="checked"' : '';
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($post_type_label); ?></th>
                    <td>
                        <input type="hidden" name="<?php echo esc_attr($option_name); ?>" value="0" />
                        <div class="omsar-toggle-wrapper">
                            <label class="omsar-toggle-label">
                                <span class="omsar-toggle">
                                    <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="1" <?php echo $checked; ?> />
                                    <span class="omsar-toggle-slider"></span>
                                </span>
                                <span class="omsar-toggle-text <?php echo $checked ? 'on' : 'off'; ?>">
                                    <?php echo $checked ? 'Yes' : 'No'; ?>
                                </span>
                            </label>
                        </div>
                        <p class="description">
                            <?php printf(__('Show back button on single %s posts.', 'omsar'), strtolower($post_type_label)); ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button(__('Save Changes', 'omsar'), 'primary', 'omsar_save_breadcrumb_settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Get menu location for current language (Polylang compatible)
 * This ensures menus work correctly with Polylang language switching
 */
function omsar_get_menu_location_id( $location ) {
    $locations = get_nav_menu_locations();
    
    // Polylang automatically filters menu locations by current language
    // But we ensure the location exists
    if ( isset( $locations[ $location ] ) ) {
        return $locations[ $location ];
    }
    
    return false;
}

/**
 * Get search form action URL (handles Polylang language prefix correctly)
 * Removes page slug from homepage URL to get clean root URL
 * 
 * @return string Search form action URL
 */
function omsar_get_search_url() {
    // If Polylang is active, rely on pll_home_url which respects
    // the "hide default language in URL" setting.
    if (function_exists('pll_home_url')) {
        $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';
        $url = $current_lang ? pll_home_url($current_lang) : pll_home_url();

        // If WordPress front page is set to a static page (e.g., /home),
        // pll_home_url might include that slug. Strip it so search always
        // points to the language root (e.g., /en/ instead of /en/home/).
        $front_page_id = get_option('page_on_front');
        if ($front_page_id) {
            $front_page = get_post($front_page_id);
            if ($front_page && !empty($front_page->post_name)) {
                $front_slug = $front_page->post_name;
                $suffix = '/' . trim($front_slug, '/') . '/';
                if (str_ends_with($url, $suffix)) {
                    $url = substr($url, 0, -strlen($suffix)) . '/';
                }
            }
        }

        return rtrim($url, '/');
    }

    // Fallback: plain site URL
    return rtrim(site_url(), '/');
}

/**
 * Polylang Language Toggle
 * Displays language switcher with "ع" for Arabic and "E" for English
 */
function omsar_lang_toggle() {
    // Check if Polylang is active
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return '';
    }
    
    // Get current language
    $current_lang = pll_current_language();
    
    // Get all available languages
    $languages = pll_the_languages( array( 'raw' => 1 ) );
    
    if ( empty( $languages ) ) {
        return '';
    }
    
    // Find the other language (not current)
    $other_lang = null;
    foreach ( $languages as $lang ) {
        if ( $lang['slug'] !== $current_lang ) {
            $other_lang = $lang;
            break;
        }
    }
    
    // If no other language found, return empty
    if ( ! $other_lang ) {
        return '';
    }
    
    // Determine display text based on the other language
    $display_text = '';
    if ( $other_lang['slug'] === 'ar' ) {
        $display_text = 'ع';
    } elseif ( $other_lang['slug'] === 'en' ) {
        $display_text = 'EN';
    } else {
        // Fallback to language code if not Arabic or English
        $display_text = strtoupper( substr( $other_lang['slug'], 0, 1 ) );
    }
    
    // Output the language toggle link.
    // Preserve query params used by dynamic templates (e.g. procurement_id).
    $other_url = $other_lang['url'];
    $query_params_to_keep = array();
    if ( isset( $_GET['minister_id'] ) && $_GET['minister_id'] !== '' ) {
        $query_params_to_keep['minister_id'] = intval( $_GET['minister_id'] );
    }
    if ( isset( $_GET['procurement_id'] ) && $_GET['procurement_id'] !== '' ) {
        $query_params_to_keep['procurement_id'] = absint( $_GET['procurement_id'] );
    }
    if ( ! empty( $query_params_to_keep ) ) {
        $other_url = add_query_arg( $query_params_to_keep, $other_url );
    }

    echo '<a href="' . esc_url( $other_url ) . '" class="lang-toggle me-3">' . esc_html( $display_text ) . '</a>';
}

//  Allow SVG uploads
// Add SVG to allowed MIME types
function add_svg_mime_type($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'add_svg_mime_type', 10, 1);

// Allow SVG uploads and bypass WordPress check
function allow_svg_upload_force($data, $file, $filename, $mimes, $real_mime = null) {
    // Check if it's an SVG file by extension or MIME type
    $wp_filetype = wp_check_filetype($filename, $mimes);
    $ext = $wp_filetype['ext'];
    $type = $wp_filetype['type'];
    
    if ($ext === 'svg' || $type === 'image/svg+xml' || (isset($real_mime) && $real_mime === 'image/svg+xml')) {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
        $data['proper_filename'] = $filename;
    }
    
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'allow_svg_upload_force', 10, 5);

// Bypass WordPress SVG security validation that causes "unsafe content" error
// This filter runs at high priority to override any security checks
add_filter('wp_handle_upload_prefilter', function($file) {
    if (isset($file['name']) && preg_match('/\.svg$/i', $file['name'])) {
        // Clear any error messages related to security/unsafe content
        if (isset($file['error']) && !empty($file['error'])) {
            $error_lower = strtolower($file['error']);
            if (stripos($error_lower, 'unsafe') !== false || 
                stripos($error_lower, 'security') !== false || 
                stripos($error_lower, 'not allowed') !== false ||
                stripos($error_lower, 'file type') !== false) {
                $file['error'] = '';
            }
        }
    }
    return $file;
}, 99, 1);

//  Fix SVG display in Media Library
function fix_svg_display() {
    echo '<style>
        td.media-icon img[src$=".svg"],
        img[src$=".svg"].attachment-post-thumbnail {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'fix_svg_display');


// Enable Featured Image Support for Posts
add_theme_support('post-thumbnails');

// (Optional) Set default image sizes
set_post_thumbnail_size(1200, 700, true);

// Register optimized image size for news cards
add_image_size('news-card-thumb', 600, 400, true);

// Include shortcodes
require_once get_template_directory() . '/inc/shortcodes.php';

// Include helper functions
require_once get_template_directory() . '/inc/helper-functions.php';

// Procurement notices Apply behavior helpers (per-post enable_popup)
require_once get_template_directory() . '/inc/procurement-apply-behavior.php';

// Include ACF functions
require_once get_template_directory() . '/inc/acf-functions.php';

// Include admin columns
require_once get_template_directory() . '/inc/admin-columns.php';

// Include AJAX handlers
require_once get_template_directory() . '/inc/ajax-handlers.php';

// Include contact form handler
require_once get_template_directory() . '/inc/contact-form-handler.php';

// Include partnership form handler
require_once get_template_directory() . '/inc/partnership-form-handler.php';

// Include complaint / inquiry form handler
require_once get_template_directory() . '/inc/complaint-inquiry-form-handler.php';

// Include survey form handler
require_once get_template_directory() . '/inc/survey-form-handler.php';
require_once get_template_directory() . '/inc/citizen-survey-handler.php';

// Include recruitment notify handler
require_once get_template_directory() . '/inc/recruitment-notify-handler.php';
// Include recruitment status management (date-based status and email notifications)
require_once get_template_directory() . '/inc/recruitment-status.php';
// Include procurement status management (date-based status)
require_once get_template_directory() . '/inc/procurement-status.php';

// Include recruitment ACF field validation
require_once get_template_directory() . '/inc/recruitment-validation.php';
require_once get_template_directory() . '/inc/procurement-validation.php';

require_once get_template_directory() . '/inc/procurement-apply-handler.php';

// Include recruitment subscribers access control
require_once get_template_directory() . '/inc/recruitment-sub-access-control.php';

// Include procurement subscribers access control
require_once get_template_directory() . '/inc/procurement-sub-access-control.php';

// Include survey submissions access control
require_once get_template_directory() . '/inc/survey-submissions-access-control.php';

// Include banner functions
require_once get_template_directory() . '/inc/banner.php';

// Include sidebar functions
require_once get_template_directory() . '/inc/sidebar.php';

// Include ACF sidebar field registration
require_once get_template_directory() . '/inc/acf-sidebar-field.php';

// Include Elementor Icon Box extension
require_once get_template_directory() . '/inc/elementor-icon-box-extension.php';


// Include Elementor Related Posts widget
require_once get_template_directory() . '/inc/elementor-related-posts-widget.php';

// Include Elementor Statistics Card Widget
require_once get_template_directory() . '/inc/elementor-stat-card-widget.php';


// Include Elementor Chart Widgets (Bar Chart, Circular Chart, Line Chart)
require_once get_template_directory() . '/inc/elementor-chart-widgets.php';

// Include Elementor Custom Table Widget
require_once get_template_directory() . '/inc/elementor-custom-table-widget.php';

// Include Elementor Custom Timeline Widget
require_once get_template_directory() . '/inc/elementor-timeline-widget.php';

// Include Elementor Custom Carousel Media Widget
require_once get_template_directory() . '/inc/elementor-carousel-media-widget.php';

// Include Elementor Custom Hero Section Widget
require_once get_template_directory() . '/inc/elementor-hero-section-widget.php';

// Include Elementor Custom Progress Bar Widget
require_once get_template_directory() . '/inc/elementor-progress-bar-widget.php';

// Include Elementor Animated Stats Counter Widget
require_once get_template_directory() . '/inc/elementor-animated-stats-counter-widget.php';

// Include Elementor Static Card List Widget
require_once get_template_directory() . '/inc/elementor-static-card-list-widget.php';

// Breakdance custom elements (theme-based, mirrors elementor-widgets).
require_once get_template_directory() . '/inc/breakdance-widgets.php';

// Include Editor Access Control functionality
require_once get_template_directory() . '/inc/editor-access.php';

// Include Theme Installer Workflow Components
// TGM Plugin Activation
require_once get_template_directory() . '/inc/tgm-plugin-activation.php';

// One Click Demo Import Configuration
require_once get_template_directory() . '/inc/demo-import.php';

// Custom Post Types and Taxonomies
require_once get_template_directory() . '/inc/custom-post-types.php';

// ACF field groups are managed in wp-admin only (Local JSON disabled).
// require_once get_template_directory() . '/inc/acf-json-loader.php';

// ACF Recruitment Email Templates
require_once get_template_directory() . '/inc/acf-recruitment-email-templates.php';

/**
 * Elementor Compatibility Fix for Staging Environment
 * 
 * Since it works on localhost but not staging, this ensures the_content() 
 * is always available for Elementor regardless of which template is used.
 * 
 * Common causes: Different template assignment, caching, or file sync issues
 */
add_action('template_redirect', function() {
    if (!is_page() || !did_action('elementor/loaded')) {
        return;
    }
    
    // Detect Elementor editor/preview mode
    $is_elementor_mode = (
        isset($_GET['elementor-preview']) || 
        (isset($_GET['action']) && $_GET['action'] === 'elementor')
    );
    
    if ($is_elementor_mode) {
        // Use output buffering to inject content area if missing
        ob_start(function($html) {
            // If the_content hasn't been called, inject it before </body>
            if (!did_action('the_content') && strpos($html, 'elementor-post-') === false) {
                $body_end = strripos($html, '</body>');
                if ($body_end !== false) {
                    $before_body = substr($html, 0, $body_end);
                    $after_body = substr($html, $body_end);
                    
                    // Get and output content
                    // Cannot use ob_start() inside ob_start callback, so use get_post_field + apply_filters
                    $content_output = '';
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            // Get post content and apply filters without output buffering
                            $post_content = get_post_field('post_content', get_the_ID());
                            if (!empty($post_content)) {
                                $content_output = apply_filters('the_content', $post_content);
                            }
                        }
                        wp_reset_postdata();
                    }
                    
                    if (!empty($content_output)) {
                        return $before_body . '<div id="elementor-content-area" style="display:none;">' . $content_output . '</div>' . $after_body;
                    }
                }
            }
            return $html;
        });
    }
}, 1);

add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

add_action('admin_menu', function () {
    remove_menu_page('edit.php?post_type=partnerships');
}, 999);

/**
 * Get the URL for the procurement single page template
 * 
 * @param int $procurement_id The procurement notice post ID
 * @return string|false The page URL with procurement_id parameter, or false if page not found
 */
function omsar_get_procurement_single_page_url( $procurement_id ) {
	if ( empty( $procurement_id ) ) {
		return false;
	}
	
	// Find page with "Procurement Single Page" template
	$pages = get_pages( array(
		'meta_key' => '_wp_page_template',
		'meta_value' => 'templates/procurement-single-page.php',
		'number' => 1,
		'post_status' => 'publish'
	) );
	
	if ( empty( $pages ) ) {
		// Fallback: try without templates/ prefix
		$pages = get_pages( array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'procurement-single-page.php',
			'number' => 1,
			'post_status' => 'publish'
		) );
	}
	
	if ( ! empty( $pages ) ) {
		$page = $pages[0];
		$page_url = get_permalink( $page->ID );
		// Add procurement_id as query parameter
		return add_query_arg( 'procurement_id', $procurement_id, $page_url );
	}
	
	return false;
}



add_action('admin_head', function() {
    $screen = get_current_screen();
    
    // Only target the 'complaint' and 'contact_us_forms' post type edit screens
    if ($screen && ($screen->post_type === 'complaint' || $screen->post_type === 'contact_us_forms' || $screen->post_type === 'survey_submissions' || $screen->post_type === 'recruitment_sub' || $screen->post_type === 'procurement_sub' || $screen->post_type === 'citizen_survey_sub')) {
        echo '<style>
            /* Hide the entire Publish metabox in Classic Editor */
            #submitdiv {
                display: none !important;
            }

            /* Hide the Publish/Update button in Classic Editor */
            #publishing-action, 
            #save-action { 
                display: none !important; 
            }
            
            /* Hide the Status/Visibility/Date settings (the whole box) */
            #submitdiv .inside .misc-pub-section,
            #submitdiv .inside #major-publishing-actions {
                display: none !important;
            }

            /* Hide the Update button in the Block Editor (Gutenberg) */
            .editor-post-publish-panel__toggle,
            .editor-post-publish-button,
            .components-panel__body.edit-post-last-revision__panel {
                display: none !important;
            }

            /* Hide the entire Publish panel in Block Editor (Gutenberg) */
            .editor-post-publish-panel,
            .editor-post-publish-panel__header {
                display: none !important;
            }

            /* Hide the Preview Changes button in Classic Editor */
            #post-preview,
            .preview {
                display: none !important;
            }

            /* Hide the Preview Changes button in Block Editor (Gutenberg) */
            .editor-post-preview__button,
            .editor-post-preview__button-toggle,
            .editor-post-preview__dropdown {
                display: none !important;
            }

            /* Optional: Hide the "Move to Trash" link */
            #delete-action {
                display: none !important;
            }
        </style>';
    }
});


// Hide "Add New" completely for complaints and Contact Submissions
function hide_add_new_for_cpts() {
    $cpts = ['contact_us_forms', 'complaint','survey_submissions','recruitment_sub','procurement_sub','citizen_survey_sub'];

    foreach ($cpts as $cpt) {

        // Override create_posts capability to disallow creation
        add_filter("register_post_type_args", function($args, $post_type) use ($cpt) {
            if ($post_type === $cpt) {
                $args['capabilities']['create_posts'] = 'do_not_allow';
            }
            return $args;
        }, 10, 2);

        // Redirect anyone trying to access Add New page directly
        add_action('admin_init', function() use ($cpt) {
            global $pagenow;
            if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === $cpt) {
                wp_redirect(admin_url('edit.php?post_type=' . $cpt));
                exit;
            }
        });
    }
}
add_action('init', 'hide_add_new_for_cpts', 1);

// Optional CSS to hide top Add New button as a fallback
function hide_add_new_button_css() {
    $screen = get_current_screen();
    $cpts = ['contact_us_forms', 'complaint','recruitment_sub','procurement_sub'];
    if (in_array($screen->post_type, $cpts)) {
        echo '<style>
            .page-title-action { display: none !important; }
        </style>';
    }
}
add_action('admin_head', 'hide_add_new_button_css');



// 1️⃣ Hide "View" and "Edit" links in dashboard for specific CPTs
add_filter( 'post_row_actions', function( $actions, $post ) {

    $read_only_cpts = ['procurement_sub', 'recruitment_sub'];

    if ( in_array( $post->post_type, $read_only_cpts ) ) {
        // Remove links
        unset( $actions['view'] );
        unset( $actions['edit'] );
        unset( $actions['inline hide-if-no-js'] ); // removes Quick Edit
    }

    return $actions;

}, 10, 2 );

// 2️⃣ Hide "Add New" button above the list
add_action( 'admin_head', function() {
    global $typenow;
    $read_only_cpts = ['procurement_sub', 'recruitment_sub'];

    if ( in_array( $typenow, $read_only_cpts ) ) {
        echo '<style>
            .page-title-action { display: none !important; }
        </style>';
    }
});

// 3️⃣ Make CPTs read-only (optional: restrict access if someone types the edit URL)
add_action( 'admin_init', function() {
    $read_only_cpts = ['procurement_sub', 'recruitment_sub'];

    foreach ( $read_only_cpts as $cpt ) {
        // Remove access to editing capabilities
        remove_post_type_support( $cpt, 'editor' );
        remove_post_type_support( $cpt, 'author' );
        remove_post_type_support( $cpt, 'custom-fields' );
        remove_post_type_support( $cpt, 'thumbnail' );
        remove_post_type_support( $cpt, 'revisions' );
        remove_post_type_support( $cpt, 'excerpt' );
        remove_post_type_support( $cpt, 'comments' );
    }
});


/**
 * Load reCAPTCHA v3 script on the front-end (site key in URL is public; never expose the secret key).
 * Loaded globally so any form can call grecaptcha.execute() and submit the token for server-side verification.
 */
function load_recaptcha_v3_globally() {
    if ( is_admin() ) {
        return;
    }
    wp_enqueue_script(
        'google-recaptcha',
        'https://www.google.com/recaptcha/api.js?render=6Ldr-XUsAAAAAP8CPwasWGVE675VDOiINzF-CdHi',
        [],
        null,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'load_recaptcha_v3_globally' );

add_filter('register_post_type_args', function ($args, $post_type) {
    switch ($post_type) {
        case 'testimonials':
            $args['capability_type'] = ['testimonial', 'testimonials'];
            $args['map_meta_cap']    = true;
            break;

        case 'projects':
            $args['capability_type'] = ['project', 'projects'];
            $args['map_meta_cap']    = true;
            break;

        case 'former_ministers':
            $args['capability_type'] = ['former_minister', 'former_ministers'];
            $args['map_meta_cap']    = true;
            break;

        case 'publication':
            $args['capability_type'] = ['publication', 'publications'];
            $args['map_meta_cap']    = true;
            break;

        case 'contact_us_forms':
            $args['capability_type'] = ['contact_us_form', 'contact_us_forms'];
            $args['map_meta_cap']    = true;
            break;

        case 'partnerships':
            $args['capability_type'] = ['partnership', 'partnerships'];
            $args['map_meta_cap']    = true;
            break;

        case 'recruitments':
            $args['capability_type'] = ['recruitment', 'recruitments'];
            $args['map_meta_cap']    = true;
            break;

        case 'complaint':
            $args['capability_type'] = ['complaint', 'complaints'];
            $args['map_meta_cap']    = true;
            break;

        case 'recruitment_sub':
            $args['capability_type'] = ['recruitment_subscriber', 'recruitment_sub'];
            $args['map_meta_cap']    = true;
            break;

        case 'procurement_notices':
            $args['capability_type'] = ['procurement_notice', 'procurement_notices'];
            $args['map_meta_cap']    = true;
            break;

        case 'procurement_sub':
            $args['capability_type'] = ['procurement_subscriber', 'procurement_sub'];
            $args['map_meta_cap']    = true;
            break;

        case 'knowledge_resources':
            $args['capability_type'] = ['knowledge_resource', 'knowledge_resources'];
            $args['map_meta_cap']    = true;
            break;

        case 'survey_submissions':
            $args['capability_type'] = ['survey_submission', 'survey_submissions'];
            $args['map_meta_cap']    = true;
            break;

        case 'citizen_survey_sub':
            $args['capability_type'] = ['citizen_survey_submission', 'citizen_survey_sub'];
            $args['map_meta_cap']    = true;
            break;
    }

    return $args;
}, 10, 2);


/**
 * Enable revisions for all public post types (including `post` and `page`).
 *
 * This is more flexible than enabling it only for `post`.
 */
add_action( 'init', function () {
	$public_post_types = get_post_types( array( 'public' => true ), 'names' );

	foreach ( $public_post_types as $post_type ) {
		// Avoid re-adding if already supported.
		if ( post_type_supports( $post_type, 'revisions' ) ) {
			continue;
		}

		add_post_type_support( $post_type, 'revisions' );
	}
}, 99 );

/**
 * About Us Breakdance sidebar toggle.
 * Uses element IDs because custom classes/selectors require Breakdance Pro.
 */
add_action('wp_footer', function () {
    // Only load on the Breakdance About Us page.
    if (!is_page(26920)) {
        return;
    }
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById("aboutSidebarToggle");
        const sidebar = document.getElementById("aboutSidebar");

        if (!toggle || !sidebar) {
            return;
        }

        toggle.setAttribute("aria-expanded", "true");

        toggle.addEventListener("click", function (e) {
            e.preventDefault();

            const isHidden = sidebar.style.display === "none";
            sidebar.style.display = isHidden ? "" : "none";
            toggle.setAttribute("aria-expanded", isHidden ? "true" : "false");
        });
    });
    </script>
    <?php
}, 100);
