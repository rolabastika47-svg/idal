<?php
/**
 * Banner Functions
 * 
 * Handles page banner display and background pattern functionality
 *
 * @package OMSAR
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get background pattern URL from option meta key or return default
 * 
 * @return string Background pattern image URL
 */
function omsar_get_background_pattern_url() {
    $background_pattern_url = '';
    
    // Detect current language (Polylang)
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';

    // Decide which ACF option fields to use
    if ($current_lang === 'ar') {
        $homepage_field   = 'homepage_pattern_ar';
        $innerpage_field  = 'inner_page_pattern_ar';
    } else {
        $homepage_field   = 'homepage_pattern';
        $innerpage_field  = 'inner_page_pattern';
    }

    // Select correct field based on page type
    if (is_front_page()) {
        $background_pattern = get_field($homepage_field, 'option');
    } else {
        $background_pattern = get_field($innerpage_field, 'option');
    }

    
    if ($background_pattern) {
        // Handle different return formats from ACF file field
        if (is_array($background_pattern) && isset($background_pattern['url'])) {
            // Array format: get URL from array
            $background_pattern_url = $background_pattern['url'];
        } elseif (is_numeric($background_pattern)) {
            // ID format: convert attachment ID to URL
            $background_pattern_url = wp_get_attachment_url($background_pattern);
        } elseif (is_string($background_pattern) && filter_var($background_pattern, FILTER_VALIDATE_URL)) {
            // URL format: use directly
            $background_pattern_url = $background_pattern;
        }
    }
    
    // If no option field or invalid format, use default from assets
    if (!$background_pattern_url) {
        $background_pattern_url = get_template_directory_uri() . '/assets/images/background.svg';
    }
    
    return $background_pattern_url;
}

/**
 * Get background pattern inline style attribute
 * 
 * @return string Inline style attribute for background pattern
 */
function omsar_get_background_pattern_style() {
    $background_pattern_url = omsar_get_background_pattern_url();
    return ' style="--background-pattern-url: url(\'' . esc_url($background_pattern_url) . '\');"';
}

/**
 * Get footer pattern URL from option meta key or return default
 * 
 * @return string Footer pattern image URL
 */
function omsar_get_footer_pattern_url() {
    $footer_pattern_url = '';
    
    

    // Only use footer_pattern for inner pages (not front page)
    if (!is_front_page()) {
        $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';

        // Decide which ACF option fields to use
        if ($current_lang === 'ar') {
            $footer_pattern = get_field('footer_pattern_ar', 'option');

        } else {
            $footer_pattern = get_field('footer_pattern', 'option');

        }

        if ($footer_pattern) {
            // Handle different return formats from ACF file field
            if (is_array($footer_pattern) && isset($footer_pattern['url'])) {
                // Array format: get URL from array
                $footer_pattern_url = $footer_pattern['url'];
            } elseif (is_numeric($footer_pattern)) {
                // ID format: convert attachment ID to URL
                $footer_pattern_url = wp_get_attachment_url($footer_pattern);
            } elseif (is_string($footer_pattern) && filter_var($footer_pattern, FILTER_VALIDATE_URL)) {
                // URL format: use directly
                $footer_pattern_url = $footer_pattern;
            }
        }
    }
    
    return $footer_pattern_url;
}

/**
 * Get footer pattern inline style attribute
 * 
 * @return string Inline style attribute for footer pattern (empty if not applicable)
 */
function omsar_get_footer_pattern_style() {
    $footer_pattern_url = omsar_get_footer_pattern_url();
    
    // Only return style if we have a footer pattern URL and it's not the front page
    if ($footer_pattern_url && !is_front_page()) {
        return ' style="--footer-pattern-url: url(\'' . esc_url($footer_pattern_url) . '\');"';
    }
    
    return '';
}

/**
 * Automatically display page banner on all inner pages (except homepage)
 * Background div is now opened in header.php right after body tag
 */
function omsar_auto_display_page_banner() {
    // Don't display on homepage
    if (is_front_page()) {
        return;
    }
    
    // Display on pages and single posts
    if (!is_page() && !is_single()) {
        return;
    }
    
    // Get post ID - try multiple methods to ensure we get it
    global $post;
    $post_id = 0;
    if ($post && isset($post->ID)) {
        $post_id = $post->ID;
    } elseif (function_exists('get_the_ID')) {
        $post_id = get_the_ID();
    } elseif (isset($GLOBALS['wp_query']->queried_object_id)) {
        $post_id = $GLOBALS['wp_query']->queried_object_id;
    }
    
    // Check if this is an Elementor page
    $is_elementor_page = false;
    if ($post_id && did_action('elementor/loaded') && class_exists('\Elementor\Plugin')) {
        if (isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->db)) {
            try {
                $is_elementor = \Elementor\Plugin::$instance->db->is_built_with_elementor($post_id);
                if ($is_elementor === true) {
                    $is_elementor_page = true;
                }
            } catch (Exception $e) {
                $is_elementor_page = false;
            } catch (Error $e) {
                $is_elementor_page = false;
            }
        }
    }
    
    // Background div is now opened in header.php right after body tag
    // No need to open it here anymore
    
    // Conditionally show banner (skip for Elementor pages if explicitly disabled)
    $show_banner = true;
    if ($is_elementor_page === true && function_exists('get_field')) {
        $hide_banner_on_elementor = get_field('hide_banner_on_elementor', $post_id);
        // Only skip if explicitly set to hide
        if ($hide_banner_on_elementor === true || $hide_banner_on_elementor === 1 || $hide_banner_on_elementor === '1') {
            $show_banner = false;
        }
    }
    
    // Output the page banner if it should be displayed
    if ($show_banner) {
        // Check if this is the single minister page template
        $banner_title = get_the_title();
        $banner_args = array('title' => $banner_title);
        
        // Get current language
        $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
        
        // Check if current post is former_ministers post type
        $current_post_type = get_post_type($post_id);
        $is_former_minister = ($current_post_type === 'former_ministers');
        
        // Check if this is the single minister page template
        if (is_page_template('single-minister.php')) {
            // Get minister ID from GET parameter or ACF field
            $minister_id = isset($_GET['minister_id']) ? intval($_GET['minister_id']) : 0;
            if (!$minister_id && function_exists('get_field')) {
                $minister_field = get_field('minister');
                if (is_object($minister_field) || is_array($minister_field)) {
                    $minister_id = is_array($minister_field) ? ($minister_field['ID'] ?? 0) : ($minister_field->ID ?? 0);
                } else {
                    $minister_id = get_field('minister_id');
                }
            }
            if ($minister_id && get_post_type($minister_id) === 'former_ministers') {
                // Check language and use appropriate title
                if ($current_lang === 'ar' && function_exists('get_field')) {
                    // For Arabic, use minister_ar_name field if available
                    $minister_ar_name = get_field('minister_ar_name', $minister_id);
                    if (!empty($minister_ar_name)) {
                        $banner_title = $minister_ar_name;
                    } else {
                        $banner_title = get_the_title($minister_id);
                    }
                } else {
                    // For English or other languages, use post title
                    $banner_title = get_the_title($minister_id);
                }
                $banner_args['title'] = $banner_title;

                // Subtitle: "Current Minister of OMSAR" for current minister, otherwise page excerpt (no override)
                if (function_exists('omsar_is_current_minister') && omsar_is_current_minister($minister_id)) {
                    $banner_args['subtitle'] = function_exists('pll__') ? pll__('Current Minister of OMSAR') : __('Current Minister of OMSAR', 'omsar');
                }
            }
        }
        // Check if current post is former_ministers post type
        elseif ($is_former_minister) {
            // Check language and use appropriate title
            if ($current_lang === 'ar' && function_exists('get_field')) {
                // For Arabic, use minister_ar_name field if available
                $minister_ar_name = get_field('minister_ar_name', $post_id);
                if (!empty($minister_ar_name)) {
                    $banner_title = $minister_ar_name;
                } else {
                    $banner_title = get_the_title();
                }
            } else {
                // For English or other languages, use post title
                $banner_title = get_the_title();
            }
            $banner_args['title'] = $banner_title;
        }
        // Check if this is the procurement single page template
        elseif (is_page_template('templates/procurement-single-page.php')) {
            // Get procurement notice ID from page custom field, query parameter, or URL slug (same logic as template)
            $procurement_id = null;
            
            // First, try to get from page custom field
            if (function_exists('get_field')) {
                $procurement_id = get_field('selected_procurement_notice', $post_id);
                // If ACF field returns an object/array, extract the ID
                if (is_object($procurement_id) && isset($procurement_id->ID)) {
                    $procurement_id = $procurement_id->ID;
                } elseif (is_array($procurement_id) && isset($procurement_id['ID'])) {
                    $procurement_id = $procurement_id['ID'];
                }
            }
            
            // If not found, try query parameter
            if (empty($procurement_id) && isset($_GET['procurement_id'])) {
                $procurement_id = absint($_GET['procurement_id']);
            }
            
            // If still not found, try to get from URL slug
            if (empty($procurement_id)) {
                global $wp;
                $request_uri = $_SERVER['REQUEST_URI'];
                $page_slug = get_post_field('post_name', $post_id);
                
                if ($page_slug && strpos($request_uri, '/' . $page_slug . '/') !== false) {
                    $url_parts = explode('/' . $page_slug . '/', $request_uri);
                    if (isset($url_parts[1]) && !empty($url_parts[1])) {
                        $potential_slug = trim($url_parts[1], '/');
                        $procurement_post_by_slug = get_page_by_path($potential_slug, OBJECT, 'procurement_notices');
                        if ($procurement_post_by_slug) {
                            $procurement_id = $procurement_post_by_slug->ID;
                        }
                    }
                }
            }
            
            // If we have a procurement ID, get the title
            if (!empty($procurement_id) && get_post($procurement_id) && get_post_type($procurement_id) === 'procurement_notices') {
                // Check language and use appropriate title
                if ($current_lang === 'ar' && function_exists('get_field')) {
                    // For Arabic, use procurement_title_ar field if available
                    $procurement_title_ar = get_field('procurement_title_ar', $procurement_id);
                    if (!empty($procurement_title_ar)) {
                        $banner_title = $procurement_title_ar;
                    } else {
                        $banner_title = get_the_title($procurement_id);
                    }
                } else {
                    // For English or other languages, use post title
                    $banner_title = get_the_title($procurement_id);
                }
                $banner_args['title'] = $banner_title;
            }
        }
        // Check if page uses sidebar layout - if so, use top-level parent page's title and subtitle
        elseif (is_page() && function_exists('omsar_should_display_sidebar') && omsar_should_display_sidebar()) {
            // Get top-level parent page ID
            $ancestors = get_post_ancestors($post_id);
            $top_level_parent_id = $post_id; // Default to current page if no ancestors
            
            if (!empty($ancestors)) {
                // Last element in ancestors array is the topmost parent (has no parent)
                $top_level_parent_id = end($ancestors);
            }
            
            // Get title and subtitle from top-level parent page
            if ($top_level_parent_id) {
                $top_level_parent = get_post($top_level_parent_id);
                if ($top_level_parent) {
                    $banner_title = get_the_title($top_level_parent_id);
                    $banner_args['title'] = $banner_title;
                    
                    // Get subtitle from excerpt (always set subtitle key, even if empty, to prevent fallback)
                    $banner_subtitle = '';
                    if (!empty($top_level_parent->post_excerpt)) {
                        $excerpt = $top_level_parent->post_excerpt;
                        // Clean up the excerpt
                        $excerpt = strip_shortcodes($excerpt);
                        $excerpt = wp_strip_all_tags($excerpt);
                        $excerpt = trim($excerpt);
                        if (!empty($excerpt)) {
                            $banner_subtitle = $excerpt;
                        }
                    }
                    $banner_args['subtitle'] = $banner_subtitle;
                }
            }
        }
        
        // For non-sidebar pages, don't pass subtitle key to preserve original behavior (template will use current page excerpt)
        // For sidebar pages, subtitle key is already set above
        get_template_part('template-parts/page-banner', null, $banner_args);
    }
    
    // Hook for templates to add content inside the background div
    do_action('omsar_after_page_banner');
}
add_action('omsar_after_header', 'omsar_auto_display_page_banner', 10);

/**
 * Close the background div before footer on inner pages (footer outside background)
 */
function omsar_close_background_div_before_footer() {
    // For inner pages only, close the background div BEFORE footer
    // This ensures footer is outside the background div on inner pages
    if (!is_front_page() && (is_page() || is_single())) {
        echo '</div>';
    }
}
add_action('omsar_before_footer', 'omsar_close_background_div_before_footer', 10);

/**
 * Close the background div after footer on front page (footer inside background)
 */
function omsar_close_background_div_after_footer() {
    // For homepage, close the background div AFTER footer
    // This ensures footer is inside the background div on front page
    if (is_front_page()) {
        echo '</div>';
    }
}
add_action('omsar_after_footer', 'omsar_close_background_div_after_footer', 10);

/**
 * Check if a minister is the current minister
 * 
 * @param int $minister_id Minister post ID
 * @return bool
 */
function omsar_is_current_minister($minister_id) {
    if (!$minister_id || get_post_type($minister_id) !== 'former_ministers') {
        return false;
    }
    
    $is_current = get_field('is_current', $minister_id);
    
    // Check if is_current is set to 1
    if (is_array($is_current)) {
        return in_array('1', $is_current, true);
    } elseif ($is_current === '1' || $is_current === 1) {
        return true;
    }
    
    // Also check raw meta value (handles serialized format)
    $is_current_raw = get_post_meta($minister_id, 'is_current', true);
    if (is_string($is_current_raw) && (strpos($is_current_raw, 's:1:"1"') !== false || $is_current_raw === '1')) {
        return true;
    }
    
    return false;
}

/**
 * Check if breadcrumb should be displayed
 * 
 * @param int $post_id Optional post ID, defaults to current post
 * @return bool
 */
function omsar_should_display_breadcrumb($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post ? $post->ID : get_the_ID();
    }
    
    if (!$post_id) {
        return false;
    }
    
    $post_type = get_post_type($post_id);
    
    // Handle pages - check per-page meta setting first
    if (is_page() && $post_type === 'page') {
        // Check for per-page breadcrumb setting (ACF field)
        $show_breadcrumb_page = '';
        if (function_exists('get_field')) {
            $show_breadcrumb_page = get_field('show_breadcrumb_page', $post_id);
        }
        
        // Fallback to post meta if ACF field doesn't exist
        if ($show_breadcrumb_page === '' || $show_breadcrumb_page === null) {
            $show_breadcrumb_page = get_post_meta($post_id, '_show_breadcrumb_page', true);
        }
        
        // If meta is explicitly set, use it
        if ($show_breadcrumb_page !== '' && $show_breadcrumb_page !== null) {
            return ($show_breadcrumb_page === '1' || $show_breadcrumb_page === 1 || $show_breadcrumb_page === true);
        }
        
        // Special handling for single minister page template
        // This is a page template, so check the former_ministers setting
        if (is_page_template('single-minister.php')) {
            $option_name = 'omsar_breadcrumb_enable_former_ministers';
            $breadcrumb_setting = get_option($option_name, '1');
            
            // Return true if setting is '1', false otherwise
            return ($breadcrumb_setting === '1' || $breadcrumb_setting === 1 || $breadcrumb_setting === true);
        }
        
        // For other pages without explicit setting, default to hiding breadcrumbs
        return false;
    }
    
    // Check for custom post types: projects, former_ministers, publication(s), knowledge_resources
    $breadcrumb_post_types = array('projects' => 'projects', 'former_ministers' => 'former_ministers', 'publication' => 'publication', 'publications' => 'publication', 'knowledge_resources' => 'knowledge_resources');
    if (is_singular() && isset($breadcrumb_post_types[$post_type])) {
        $option_key = $breadcrumb_post_types[$post_type];
        $option_name = 'omsar_breadcrumb_enable_' . $option_key;
        $breadcrumb_setting = get_option($option_name, '1');
        
        // Return true if setting is '1', false otherwise
        return ($breadcrumb_setting === '1' || $breadcrumb_setting === 1 || $breadcrumb_setting === true);
    }
    
    // Show breadcrumb only on single views of post type 'post'; hidden everywhere else
    // Check if we're on a single post page AND the post type is 'post'
    if (is_singular() && $post_type === 'post') {
        // Get post_type_option meta field to determine if it's news, events, or workshops
        $post_type_option = '';
        if (function_exists('get_field')) {
            $post_type_option = get_field('post_type_option', $post_id);
        } else {
            $post_type_option = get_post_meta($post_id, 'post_type_option', true);
        }
        
        // If post_type_option is set (news, events, or workshops), check the corresponding setting
        if (!empty($post_type_option) && in_array($post_type_option, array('news', 'events', 'workshops'))) {
            $option_name = 'omsar_breadcrumb_enable_' . $post_type_option;
            $breadcrumb_setting = get_option($option_name, '1');
            
            // Return true if setting is '1', false otherwise
            return ($breadcrumb_setting === '1' || $breadcrumb_setting === 1 || $breadcrumb_setting === true);
        }
        
        // For posts without post_type_option or with other values, default to showing breadcrumb (backward compatibility)
        return true;
    }
    
    return false;
}

/**
 * Check if back button should be displayed
 * Uses the same content-type logic as breadcrumbs; option names: omsar_back_button_enable_*
 *
 * @param int $post_id Optional post ID, defaults to current post
 * @return bool
 */
function omsar_should_display_back_button($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post ? $post->ID : get_the_ID();
    }

    if (!$post_id) {
        return false;
    }

    $post_type = get_post_type($post_id);

    // Pages: only single minister template uses back button setting (former_ministers)
    if (is_page() && $post_type === 'page') {
        if (is_page_template('single-minister.php')) {
            $option_name = 'omsar_back_button_enable_former_ministers';
            $setting = get_option($option_name, '1');
            return ($setting === '1' || $setting === 1 || $setting === true);
        }
        return false;
    }

    // Custom post types: projects, former_ministers, publication, knowledge_resources
    $back_button_post_types = array('projects' => 'projects', 'former_ministers' => 'former_ministers', 'publication' => 'publication', 'publications' => 'publication', 'knowledge_resources' => 'knowledge_resources');
    if (is_singular() && isset($back_button_post_types[$post_type])) {
        $option_key = $back_button_post_types[$post_type];
        $option_name = 'omsar_back_button_enable_' . $option_key;
        $setting = get_option($option_name, '1');
        return ($setting === '1' || $setting === 1 || $setting === true);
    }

    // Post type 'post' with post_type_option (news, events, workshops)
    if (is_singular() && $post_type === 'post') {
        $post_type_option = '';
        if (function_exists('get_field')) {
            $post_type_option = get_field('post_type_option', $post_id);
        } else {
            $post_type_option = get_post_meta($post_id, 'post_type_option', true);
        }
        if (!empty($post_type_option) && in_array($post_type_option, array('news', 'events', 'workshops'))) {
            $option_name = 'omsar_back_button_enable_' . $post_type_option;
            $setting = get_option($option_name, '1');
            return ($setting === '1' || $setting === 1 || $setting === true);
        }
        return true;
    }

    return false;
}

/**
 * Get the URL for the back button (listing page or parent)
 *
 * @return string URL for the back link, or empty string if none
 */
function omsar_get_back_button_url() {
    if (!is_page() && !is_single()) {
        return '';
    }

    global $post;
    $post_id = $post ? $post->ID : get_the_ID();
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');

    // Single minister page template
    if (is_page_template('single-minister.php')) {
        $listing_page_info = omsar_get_listing_page_info('', 'former_ministers');
        if ($listing_page_info && !empty($listing_page_info['url'])) {
            return $listing_page_info['url'];
        }
        return '';
    }

    if (is_page()) {
        $ancestors = get_post_ancestors($post_id);
        if (!empty($ancestors)) {
            $parent_id = end($ancestors);
            return get_permalink($parent_id);
        }
        return home_url('/');
    }

    // Single post or custom post type
    $post_type = get_post_type($post_id);
    $post_type_option = '';
    if (function_exists('get_field')) {
        $post_type_option = get_field('post_type_option', $post_id);
    } else {
        $post_type_option = get_post_meta($post_id, 'post_type_option', true);
    }

    $listing_page_info = false;
    if (!empty($post_type_option)) {
        $listing_page_info = omsar_get_listing_page_info($post_type_option, '');
    }
    if (!$listing_page_info && $post_type && $post_type !== 'post') {
        $listing_page_info = omsar_get_listing_page_info('', $post_type);
    }

    if ($listing_page_info && !empty($listing_page_info['url'])) {
        $listing_url = $listing_page_info['url'];
        if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
            $current_lang_code = pll_current_language();
            if ($current_lang_code) {
                $page_id = url_to_postid($listing_url);
                if ($page_id) {
                    $translated_page_id = pll_get_post($page_id, $current_lang_code);
                    if ($translated_page_id && $translated_page_id != $page_id) {
                        $listing_url = get_permalink($translated_page_id);
                    }
                }
            }
        }
        return $listing_url;
    }

    if ($post_type && $post_type !== 'post') {
        $post_type_obj = get_post_type_object($post_type);
        if ($post_type_obj && $post_type_obj->has_archive) {
            $archive_url = get_post_type_archive_link($post_type);
            if ($archive_url) {
                return $archive_url;
            }
        }
    }

    if (empty($post_type_option) && $post_type === 'post') {
        $categories = get_the_category();
        if (!empty($categories)) {
            return get_category_link($categories[0]->term_id);
        }
    }

    return home_url('/');
}

/**
 * Get back button HTML (rounded icon + "Back" text)
 *
 * @return string HTML for the back button link
 */
function omsar_get_back_button() {
    $url = omsar_get_back_button_url();
    if (empty($url)) {
        return '';
    }

    $back_text = function_exists('pll__') ? pll__('Back') : __('Back', 'omsar');
    $aria_label = sprintf(
        /* translators: %s: Back button context */
        esc_attr__('Back to listing', 'omsar'),
        $back_text
    );

    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';

    return '<a href="' . esc_url($url) . '" class="omsar-back-button" aria-label="' . $aria_label . '">' .
        '<span class="omsar-back-button-icon">' . $icon_svg . '</span>' .
        '<span class="omsar-back-button-text">' . esc_html($back_text) . '</span>' .
        '</a>';
}

/**
 * Get listing page URL and title based on post type option or custom post type
 * 
 * @param string $post_type_option The post_type_option meta value (e.g., 'news', 'events', 'workshops')
 * @param string $post_type The WordPress post type (e.g., 'former_ministers', 'projects', 'publications')
 * @return array|false Array with 'url' and 'title', or false if not found
 */
function omsar_get_listing_page_info($post_type_option = '', $post_type = '') {
    // Template-based mapping (for pages with specific templates)
    $template_mapping = array(
        'news' => 'News Listing Page',
        'events' => 'Events Listing Page',
        'workshops' => 'Workshop Listing Page',
    );
    
    // Custom post type mapping (can be template-based or Elementor-based)
    $post_type_mapping = array(
        'former_ministers' => array(
            'type' => 'template',
            'value' => 'Ministers Listing Page'
        ),
        'projects' => array(
            'type' => 'slug', // Elementor page - search by slug
            'value' => 'projects' // Common slug patterns to try
        ),
        'publications' => array(
            'type' => 'slug', // Elementor page - search by slug
            'value' => 'publications' // Common slug patterns to try
        ),
        'publication' => array(
            'type' => 'slug', // Same as publications - single post type slug
            'value' => 'publications' // Common slug patterns to try for listing page
        ),
        'knowledge_resources' => array(
            'type' => 'slug', // Elementor page - search by slug
            'value' => 'knowledge-resources' // Common slug patterns to try (knowledge-resources, knowledge-and-resources, etc.)
        ),
    );
    
    // Allow filtering of mappings for customization
    $template_mapping = apply_filters('omsar_breadcrumb_template_mapping', $template_mapping, $post_type_option);
    $post_type_mapping = apply_filters('omsar_breadcrumb_post_type_mapping', $post_type_mapping, $post_type);
    
    $search_type = '';
    $search_value = '';
    
    // First check post_type_option (for regular posts with meta field)
    if (!empty($post_type_option) && isset($template_mapping[$post_type_option])) {
        $search_type = 'template';
        $search_value = $template_mapping[$post_type_option];
    }
    // Then check custom post type
    elseif (!empty($post_type) && isset($post_type_mapping[$post_type])) {
        $mapping = $post_type_mapping[$post_type];
        $search_type = $mapping['type'];
        $search_value = $mapping['value'];
    }
    
    if (empty($search_type) || empty($search_value)) {
        return false;
    }
    
    $pages = array();
    
    // Search by template (for template-based pages)
    if ($search_type === 'template') {
        // Convert template name to filename format
        // "News Listing Page" -> "news-listing-page.php"
        $template_filename = strtolower(str_replace(' ', '-', $search_value)) . '.php';
        
        // Try different template path formats that WordPress might use
        $template_paths = array(
            'templates/' . $template_filename,  // Most common: templates/news-listing-page.php
            $template_filename,                  // Just filename: news-listing-page.php
        );
        
        foreach ($template_paths as $template_path) {
            $pages = get_pages(array(
                'meta_key' => '_wp_page_template',
                'meta_value' => $template_path,
                'number' => 1,
                'post_status' => 'publish'
            ));
            
            if (!empty($pages)) {
                break; // Found a page, stop searching
            }
        }
    }
    // Search by slug (for Elementor pages)
    elseif ($search_type === 'slug') {
        // Try to find page by slug variations
        $slug_variations = array(
            $search_value,                    // 'projects'
            $search_value . '-listing',        // 'projects-listing'
            'list-' . $search_value,           // 'list-projects'
            $search_value . 's',                // 'projectss' (plural if singular)
        );
        // Extra slug variations for knowledge_resources listing page (underscore and hyphen variants)
        if ($post_type === 'knowledge_resources' || $search_value === 'knowledge-resources') {
            $slug_variations = array_merge(
                array('knowledge_resources', 'knowledge-and-resources', 'knowledge-center-and-resources', 'knowledge-center-and-resources-listing'),
                $slug_variations
            );
        }
        
        foreach ($slug_variations as $slug) {
            $page = get_page_by_path($slug);
            if ($page && $page->post_status === 'publish') {
                $pages = array($page);
                break;
            }
        }
        
        // For knowledge_resources: get_page_by_path() returns null for child pages (requires full path).
        // Find by post_name (slug) via get_posts so child pages like parent/knowledge_resources are found.
        if (empty($pages) && ($post_type === 'knowledge_resources' || $search_value === 'knowledge-resources')) {
            $found = get_posts(array(
                'post_type'      => 'page',
                'name'           => 'knowledge_resources',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'numberposts'    => 1,
            ));
            if (!empty($found)) {
                $pages = array($found[0]);
            }
        }
        
        // If not found by slug, try searching by title (exact matches first, then partial)
        if (empty($pages)) {
            $search_value_lower = strtolower($search_value);
            $search_value_title = ucwords(str_replace('-', ' ', $search_value)); // 'projects' -> 'Projects'
            
            // Try exact title matches first
            $exact_titles = array(
                $search_value_title,                    // 'Projects'
                $search_value_title . ' Listing',        // 'Projects Listing'
                'List ' . $search_value_title,          // 'List Projects'
            );
            // Extra exact titles for knowledge_resources listing page
            if ($post_type === 'knowledge_resources' || $search_value === 'knowledge-resources') {
                $exact_titles = array_merge(
                    array('Knowledge and Resources', 'Knowledge Center and Resources', 'Knowledge and Resources Listing', 'Knowledge Center and Resources Listing'),
                    $exact_titles
                );
            }
            
            foreach ($exact_titles as $title) {
                $found_pages = get_pages(array(
                    'title' => $title,
                    'number' => 1,
                    'post_status' => 'publish'
                ));
                
                if (!empty($found_pages)) {
                    $pages = $found_pages;
                    break;
                }
            }
            
            // If still not found, try partial title match (but be more specific)
            if (empty($pages)) {
                $all_pages = get_pages(array(
                    'post_status' => 'publish',
                    'number' => 50 // Limit to avoid performance issues
                ));
                
                foreach ($all_pages as $page) {
                    $page_title_lower = strtolower($page->post_title);
                    $page_slug = $page->post_name;
                    
                    // Check for exact word match in title or slug
                    // This prevents matching pages like "Our Projects Team" when searching for "projects"
                    if (preg_match('/\b' . preg_quote($search_value_lower, '/') . '\b/i', $page_title_lower) ||
                        $page_slug === $search_value_lower ||
                        strpos($page_slug, $search_value_lower . '-') === 0 ||
                        strpos($page_slug, '-' . $search_value_lower) !== false) {
                        $pages = array($page);
                        break;
                    }
                }
            }
        }
    }
    
    if (!empty($pages)) {
        $page = $pages[0];
        return array(
            'url' => get_permalink($page->ID),
            'title' => get_the_title($page->ID)
        );
    }
    
    return false;
}

/**
 * Generate breadcrumb HTML
 * 
 * @return string Breadcrumb HTML
 */
function omsar_get_breadcrumb() {
    if (!is_page() && !is_single()) {
        return '';
    }
    
    global $post;
    $home_url = home_url('/');
    $home_text = function_exists('pll__') ? pll__('Home') : __('Home', 'omsar');
    
    $breadcrumb_items = array();
    
    // Add Home link
    $breadcrumb_items[] = '<a href="' . esc_url($home_url) . '">' . esc_html($home_text) . '</a>';
    
    // Get current language
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
    
    // Special handling for single minister page template
    if (is_page_template('single-minister.php')) {
        // Get the minister ID from URL parameter or ACF field
        $minister_id = isset($_GET['minister_id']) ? intval($_GET['minister_id']) : 0;
        if (!$minister_id && function_exists('get_field')) {
            $minister_field = get_field('minister', $post->ID);
            if (is_object($minister_field) || is_array($minister_field)) {
                $minister_id = is_array($minister_field) ? ($minister_field['ID'] ?? 0) : ($minister_field->ID ?? 0);
            } else {
                $minister_id = get_field('minister_id', $post->ID);
            }
        }
        
        // If we have a valid minister ID, add listing page and minister name
        if ($minister_id && get_post_type($minister_id) === 'former_ministers') {
            // Get listing page info
            $listing_page_info = omsar_get_listing_page_info('', 'former_ministers');
            if ($listing_page_info && !empty($listing_page_info['url'])) {
                $breadcrumb_items[] = '<a href="' . esc_url($listing_page_info['url']) . '">' . esc_html($listing_page_info['title']) . '</a>';
            }
            
            // Add minister name - check language and use appropriate title
            if ($current_lang === 'ar' && function_exists('get_field')) {
                // For Arabic, use minister_ar_name field if available
                $minister_ar_name = get_field('minister_ar_name', $minister_id);
                if (!empty($minister_ar_name)) {
                    $minister_title = $minister_ar_name;
                } else {
                    $minister_title = get_the_title($minister_id);
                }
            } else {
                // For English or other languages, use post title
                $minister_title = get_the_title($minister_id);
            }
            $breadcrumb_items[] = '<span class="breadcrumb-current">' . esc_html($minister_title) . '</span>';
        } else {
            // Fallback: just show page title
            $breadcrumb_items[] = '<span class="breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
        }
        
        // Return early for single minister page - don't continue to regular page logic
        $separator = '<span class="breadcrumb-separator"> / </span>';
        return '<nav class="breadcrumb-nav" aria-label="Breadcrumb"><ol class="breadcrumb-list">' . implode($separator, $breadcrumb_items) . '</ol></nav>';
    }
    // For regular pages, get ancestors
    elseif (is_page()) {
        $ancestors = get_post_ancestors($post->ID);
        
        // Reverse to get topmost parent first
        if (!empty($ancestors)) {
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_post($ancestor_id);
                if ($ancestor) {
                    $breadcrumb_items[] = '<a href="' . esc_url(get_permalink($ancestor_id)) . '">' . esc_html($ancestor->post_title) . '</a>';
                }
            }
        }
        
        // Add current page (not linked)
        $breadcrumb_items[] = '<span class="breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_single()) {
        // Get post type
        $post_type = get_post_type();
        $post_id = get_the_ID();
        
        // Check for post_type_option meta field (for regular posts with custom categorization)
        $post_type_option = '';
        if (function_exists('get_field')) {
            $post_type_option = get_field('post_type_option', $post_id);
        } else {
            $post_type_option = get_post_meta($post_id, 'post_type_option', true);
        }
        
        // Try to get listing page info
        $listing_page_info = false;
        if (!empty($post_type_option)) {
            $listing_page_info = omsar_get_listing_page_info($post_type_option, '');
        }
        
        // If no listing page found via post_type_option, check custom post type
        if (!$listing_page_info && $post_type && $post_type !== 'post') {
            $listing_page_info = omsar_get_listing_page_info('', $post_type);
        }
        
        // If we found a listing page, add it to breadcrumb
        if ($listing_page_info && !empty($listing_page_info['url'])) {
            $listing_title = $listing_page_info['title'];
            $listing_url = $listing_page_info['url'];
            
            // Get the translated page URL for current language
            if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
                $current_lang_code = pll_current_language();
                if ($current_lang_code) {
                    // Try to get page ID from URL
                    $page_id = url_to_postid($listing_url);
                    if ($page_id) {
                        // Get translated page ID for current language
                        $translated_page_id = pll_get_post($page_id, $current_lang_code);
                        if ($translated_page_id && $translated_page_id != $page_id) {
                            // Use translated page URL and title
                            $listing_url = get_permalink($translated_page_id);
                            $listing_title = get_the_title($translated_page_id);
                        }
                    }
                }
            }
            
            // Special handling for projects post type - ensure Arabic translation
            if ($post_type === 'projects') {
                if ($current_lang === 'ar') {
                    // Use Polylang translation if available
                    if (function_exists('pll__')) {
                        $translated_label = pll__('Projects');
                        if ($translated_label && $translated_label !== 'Projects') {
                            $listing_title = $translated_label;
                        } else {
                            // Fallback: use Arabic label
                            $listing_title = 'المشاريع';
                        }
                    } else {
                        // Fallback: use Arabic label
                        $listing_title = 'المشاريع';
                    }
                }
            }
            // Special handling for publication(s) post type - ensure Arabic translation
            elseif ($post_type === 'publication' || $post_type === 'publications') {
                if ($current_lang === 'ar') {
                    // Use Polylang translation if available
                    if (function_exists('pll__')) {
                        $translated_label = pll__('Publications');
                        if ($translated_label && $translated_label !== 'Publications') {
                            $listing_title = $translated_label;
                        } else {
                            // Fallback: use Arabic label
                            $listing_title = 'المنشورات';
                        }
                    } else {
                        // Fallback: use Arabic label
                        $listing_title = 'المنشورات';
                    }
                }
            }
            // Special handling for knowledge_resources post type - use Polylang label if available
            elseif ($post_type === 'knowledge_resources') {
                if (function_exists('pll__')) {
                    $listing_title = pll__('Knowledge Center and Resources');
                } else {
                    $listing_title = 'Knowledge Center and Resources';
                }
            }
            
            $breadcrumb_items[] = '<a href="' . esc_url($listing_url) . '">' . esc_html($listing_title) . '</a>';
        }
        // Fallback: For custom post types with archive, add archive link
        elseif ($post_type && $post_type !== 'post') {
            $post_type_obj = get_post_type_object($post_type);
            if ($post_type_obj && $post_type_obj->has_archive) {
                $archive_url = get_post_type_archive_link($post_type);
                if ($archive_url) {
                    // Get archive label (plural name)
                    $archive_label = $post_type_obj->labels->name;
                    
                    // Special handling for projects post type
                    if ($post_type === 'projects') {
                        // Use Polylang translation if available
                        if (function_exists('pll__')) {
                            $translated_label = pll__('Projects');
                            if ($translated_label && $translated_label !== 'Projects') {
                                $archive_label = $translated_label;
                            } else {
                                // Fallback: use Arabic label if current language is Arabic
                                if ($current_lang === 'ar') {
                                    $archive_label = 'المشاريع';
                                }
                            }
                        } elseif ($current_lang === 'ar') {
                            // If Polylang not available but Arabic, use Arabic label
                            $archive_label = 'المشاريع';
                        }
                    } elseif ($post_type === 'publication' || $post_type === 'publications') {
                        // Special handling for publication(s) post type
                        if (function_exists('pll__')) {
                            $translated_label = pll__('Publications');
                            if ($translated_label && $translated_label !== 'Publications') {
                                $archive_label = $translated_label;
                            } elseif ($current_lang === 'ar') {
                                $archive_label = 'المنشورات';
                            }
                        } elseif ($current_lang === 'ar') {
                            $archive_label = 'المنشورات';
                        }
                    } elseif ($post_type === 'knowledge_resources') {
                        // Special handling for knowledge_resources post type - use Polylang label if available
                        if (function_exists('pll__')) {
                            $archive_label = pll__('Knowledge Center and Resources');
                        } else {
                            $archive_label = 'Knowledge Center and Resources';
                        }
                    } else {
                        // For other post types, try to get translated label if Polylang is active
                        if (function_exists('pll__')) {
                            // Try to get translation for common post types
                            $translated_label = pll__($archive_label);
                            if ($translated_label !== $archive_label) {
                                $archive_label = $translated_label;
                            }
                        }
                    }
                    
                    $breadcrumb_items[] = '<a href="' . esc_url($archive_url) . '">' . esc_html($archive_label) . '</a>';
                }
            }
        }
        // Fallback: For regular posts without post_type_option, add category if available
        elseif (empty($post_type_option)) {
            $categories = get_the_category();
            if (!empty($categories)) {
                $category = $categories[0];
                $breadcrumb_items[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
            }
        }
        
        // Add current post (not linked)
        // Ensure we get the translated post title for Polylang
        $current_post_title = get_the_title();
        
        // Check if Polylang is active and ensure we're using the correct language version
        if (function_exists('pll_get_post_language') && function_exists('pll_current_language') && function_exists('pll_get_post')) {
            $current_lang_code = pll_current_language();
            $post_lang = pll_get_post_language($post_id);
            
            // If the current post is not in the current language, get the translated version
            if ($current_lang_code && $post_lang && $post_lang !== $current_lang_code) {
                $translated_post_id = pll_get_post($post_id, $current_lang_code);
                if ($translated_post_id && $translated_post_id != $post_id) {
                    // Use the translated post title
                    $translated_title = get_the_title($translated_post_id);
                    if (!empty($translated_title)) {
                        $current_post_title = $translated_title;
                    }
                }
            } elseif ($current_lang_code && !$post_lang) {
                // If post language is not set, try to get translated version for current language
                $translated_post_id = pll_get_post($post_id, $current_lang_code);
                if ($translated_post_id && $translated_post_id != $post_id) {
                    $translated_title = get_the_title($translated_post_id);
                    if (!empty($translated_title)) {
                        $current_post_title = $translated_title;
                    }
                }
            }
        }
        
        // Special handling for former_ministers post type
        if ($post_type === 'former_ministers' && $current_lang === 'ar' && function_exists('get_field')) {
            // For Arabic, use minister_ar_name field if available
            $minister_ar_name = get_field('minister_ar_name', $post_id);
            if (!empty($minister_ar_name)) {
                $current_post_title = $minister_ar_name;
            }
        }
        
        $breadcrumb_items[] = '<span class="breadcrumb-current">' . esc_html($current_post_title) . '</span>';
    }
    
    $separator = '<span class="breadcrumb-separator"> / </span>';
    
    return '<nav class="breadcrumb-nav" aria-label="Breadcrumb"><ol class="breadcrumb-list">' . implode($separator, $breadcrumb_items) . '</ol></nav>';
}


