<?php
/**
 * AJAX Handlers for OMSAR Theme
 * 
 * This file contains all AJAX handlers for the theme.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX handler for filtering news posts by date
 */
add_action('wp_ajax_filter_news', 'omsar_filter_news');
add_action('wp_ajax_nopriv_filter_news', 'omsar_filter_news');

function omsar_filter_news() {
    // Verify nonce for security
    check_ajax_referer('load_more_news_nonce', 'nonce');

    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'news';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build date query for filtering by month and/or year
    $date_query = array();
    if (!empty($filter_year)) {
        $date_query['year'] = intval($filter_year);
    }
    if (!empty($filter_month)) {
        $date_query['month'] = intval($filter_month);
    }

    // Query for filtered posts (page 1)
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => 1,
        'meta_query' => array(
            array(
                'key'     => 'post_type_option',
                'value'   => $post_type_value,
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC',
        'update_post_term_cache' => false,
        'no_found_rows' => false
    );

    // Add date query if filters are set
    if (!empty($date_query)) {
        $args['date_query'] = array($date_query);
    }

    $news_query = new WP_Query($args);
    $total_found = $news_query->found_posts;

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    ob_start();

    if ($news_query->have_posts()) {
        while ($news_query->have_posts()) {
            $news_query->the_post();
            
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
            if (!$image_url) {
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            }
            if (!$image_url) {
                $image_url = $default_image;
            }
            
            $title = get_the_title();
            $publish_date = get_the_date('d-m-Y');
            $permalink = get_permalink();
            ?>
            <div class="col-lg-3 col-md-6 news-card-item">
                <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                    <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <div class="news-overlay"></div>
                        <div class="news-content">
                            <div class="news-date mb-2">
                                <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                                <span><?php echo esc_html($publish_date); ?></span>
                            </div>
                            <h3 class="news-title pt-4">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
        }
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    // Calculate if there are more posts
    $has_more = ($total_found > $per_page);

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'total' => $total_found,
        'loaded' => $news_query->post_count,
        'count' => $news_query->post_count
    ));
}

/**
 * AJAX handler for loading more news posts
 */
add_action('wp_ajax_load_more_news', 'omsar_load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'omsar_load_more_news');

function omsar_load_more_news() {
    // Verify nonce for security
    check_ajax_referer('load_more_news_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'news';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build date query for filtering by month and/or year
    $date_query = array();
    if (!empty($filter_year)) {
        $date_query['year'] = intval($filter_year);
    }
    if (!empty($filter_month)) {
        $date_query['month'] = intval($filter_month);
    }

    // Single optimized query - more reliable than two-step approach
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => $page,
        'meta_query' => array(
            array(
                'key'     => 'post_type_option',
                'value'   => $post_type_value,
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC',
        // Performance optimizations
        'update_post_term_cache' => false, // Don't update term cache
        'no_found_rows' => false // We need found_posts for pagination
    );

    // Add date query if filters are set
    if (!empty($date_query)) {
        $args['date_query'] = array($date_query);
    }

    // Single query to get all post data
    $news_query = new WP_Query($args);
    $total_found = $news_query->found_posts;
    
    // Calculate if there are more posts after this page
    // Page 1 shows posts 1-4, Page 2 shows posts 5-8, etc.
    // Total loaded after this page = page * per_page (if full page) or less (if last page)
    $posts_on_this_page = $news_query->post_count;
    
    // Calculate total loaded: (page - 1) * per_page + actual posts on this page
    $total_loaded_so_far = ($page - 1) * $per_page + $posts_on_this_page;
    
    // There are more posts if total_found is greater than what we've loaded so far
    $has_more = ($total_found > $total_loaded_so_far);
    
    // Debug logging (remove in production)
    error_log("AJAX Load More Debug - Page: $page, Posts on page: $posts_on_this_page, Total found: $total_found, Total loaded: $total_loaded_so_far, Has more: " . ($has_more ? 'true' : 'false'));

    // If no posts, return early
    if (!$news_query->have_posts()) {
        wp_reset_postdata();
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => $total_found,
            'loaded' => 0,
            'count' => 0
        ));
    }

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    // Use output buffering for faster HTML generation
    ob_start();
    
    while ($news_query->have_posts()) {
        $news_query->the_post();
        
        // Use optimized image size instead of 'full'
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
        if (!$image_url) {
            // Try medium size as fallback
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        }
        if (!$image_url) {
            $image_url = $default_image;
        }
        
        $title = get_the_title();
        $publish_date = get_the_date('d-m-Y');
        $permalink = get_permalink();
        ?>
        <div class="col-lg-3 col-md-6 news-card-item">
            <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                    <div class="news-overlay"></div>
                    <div class="news-content">
                        <div class="news-date mb-2">
                            <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                            <span><?php echo esc_html($publish_date); ?></span>
                        </div>
                        <h3 class="news-title pt-4">
                            <?php echo esc_html($title); ?>
                        </h3>
                        <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                    </div>
                </div>
            </a>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Ensure HTML is always a string (even if empty)
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'next_page' => $page + 1,
        'total' => $total_found,
        'loaded' => $total_loaded_so_far,
        'count' => $posts_on_this_page
    ));
}

/**
 * AJAX handler for loading more posts in taxonomy widget
 */
add_action('wp_ajax_omsar_load_more_taxonomy_posts', 'omsar_load_more_taxonomy_posts');
add_action('wp_ajax_nopriv_omsar_load_more_taxonomy_posts', 'omsar_load_more_taxonomy_posts');

function omsar_load_more_taxonomy_posts() {
    // Verify nonce
    check_ajax_referer('omsar_taxonomy_load_more_nonce', 'nonce');
    
    $widget_id = isset($_POST['widget_id']) ? sanitize_text_field($_POST['widget_id']) : '';
    $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : '';
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
    $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
    $term_id = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
    $term_name = isset($_POST['term_name']) ? sanitize_text_field($_POST['term_name']) : '';
    $use_acf = isset($_POST['use_acf']) ? intval($_POST['use_acf']) : 0;
    $acf_field = isset($_POST['acf_field']) ? sanitize_text_field($_POST['acf_field']) : '';
    $listing_style = isset($_POST['listing_style']) ? sanitize_text_field($_POST['listing_style']) : 'style1';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
    // Get current page from POST (if provided) or calculate from offset
    $current_page = isset($_POST['current_page']) ? intval($_POST['current_page']) : (isset($_POST['offset']) ? (intval($_POST['offset']) / $posts_per_page) + 1 : 1);
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : (($current_page - 1) * $posts_per_page);
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $default_image = isset($_POST['default_image']) ? esc_url_raw($_POST['default_image']) : '';
    $badge_source = isset($_POST['badge_source']) ? sanitize_text_field($_POST['badge_source']) : 'none';
    $badge_taxonomy = isset($_POST['badge_taxonomy']) ? sanitize_text_field($_POST['badge_taxonomy']) : '';
    $badge_custom_field = isset($_POST['badge_custom_field']) ? sanitize_text_field($_POST['badge_custom_field']) : '';
    $badge_taxonomy_display = isset($_POST['badge_taxonomy_display']) ? sanitize_text_field($_POST['badge_taxonomy_display']) : 'name';
    $read_more_text = isset($_POST['read_more_text']) ? sanitize_text_field($_POST['read_more_text']) : __('Read More', 'omsar');
    $show_read_more = isset($_POST['show_read_more']) ? ($_POST['show_read_more'] === 'yes' || $_POST['show_read_more'] === '1' || $_POST['show_read_more'] === true) : true;
    $style4_dropdown_source = isset($_POST['style4_dropdown_source']) ? sanitize_text_field($_POST['style4_dropdown_source']) : '';
    $style4_dropdown_taxonomy = isset($_POST['style4_dropdown_taxonomy']) ? sanitize_text_field($_POST['style4_dropdown_taxonomy']) : '';
    $style4_dropdown_acf_field = isset($_POST['style4_dropdown_acf_field']) ? sanitize_text_field($_POST['style4_dropdown_acf_field']) : '';
    
    // Get filter parameters for Style 4
    $filter_search_term = isset($_POST['filter_search_term']) ? sanitize_text_field($_POST['filter_search_term']) : '';
    $filter_dropdown_value = isset($_POST['filter_dropdown_value']) ? sanitize_text_field($_POST['filter_dropdown_value']) : '';
    $filter_dropdown_taxonomy = isset($_POST['filter_dropdown_taxonomy']) ? sanitize_text_field($_POST['filter_dropdown_taxonomy']) : '';
    $filter_dropdown_custom_field = isset($_POST['filter_dropdown_custom_field']) ? sanitize_text_field($_POST['filter_dropdown_custom_field']) : '';
    
    // Get custom field filter parameters
    $custom_field_meta_key = isset($_POST['custom_field_meta_key']) ? sanitize_text_field($_POST['custom_field_meta_key']) : '';
    $custom_field_meta_value = isset($_POST['custom_field_meta_value']) ? sanitize_text_field($_POST['custom_field_meta_value']) : '';
    
    // Get Style 5 display options
    $style5_content_overlay = isset($_POST['style5_content_overlay']) ? ($_POST['style5_content_overlay'] === '1' || $_POST['style5_content_overlay'] === 'yes' || $_POST['style5_content_overlay'] === true) : false;
    $style5_side_layout = isset($_POST['style5_side_layout']) ? ($_POST['style5_side_layout'] === '1' || $_POST['style5_side_layout'] === 'yes' || $_POST['style5_side_layout'] === true) : false;
    $style5_show_categories = isset($_POST['style5_show_categories']) ? ($_POST['style5_show_categories'] === '1' || $_POST['style5_show_categories'] === 'yes' || $_POST['style5_show_categories'] === true) : true;
    $style5_show_excerpt = isset($_POST['style5_show_excerpt']) ? ($_POST['style5_show_excerpt'] === '1' || $_POST['style5_show_excerpt'] === 'yes' || $_POST['style5_show_excerpt'] === true) : true;
    $style5_show_date = isset($_POST['style5_show_date']) ? ($_POST['style5_show_date'] === '1' || $_POST['style5_show_date'] === 'yes' || $_POST['style5_show_date'] === true) : true;
    $style5_show_author = isset($_POST['style5_show_author']) ? ($_POST['style5_show_author'] === '1' || $_POST['style5_show_author'] === 'yes' || $_POST['style5_show_author'] === true) : true;
    $clickable_cards = isset($_POST['clickable_cards']) ? ($_POST['clickable_cards'] === '1' || $_POST['clickable_cards'] === 'yes' || $_POST['clickable_cards'] === true) : false;
    
    // Helper function to filter posts by custom field
    $filter_posts_by_custom_field = function($posts, $meta_key, $meta_value) {
        if (empty($meta_key) || empty($meta_value)) {
            return $posts;
        }
        $filtered_posts = [];
        foreach ($posts as $post) {
            $post_meta_value = get_post_meta($post->ID, $meta_key, true);
            if (is_array($post_meta_value)) {
                if (in_array($meta_value, $post_meta_value, true)) {
                    $filtered_posts[] = $post;
                }
            } else {
                if ((string) $post_meta_value === (string) $meta_value) {
                    $filtered_posts[] = $post;
                }
            }
        }
        return $filtered_posts;
    };
    
    // Get posts based on tab type
    $posts = [];
    $all_filtered_posts = []; // For calculating total filtered count
    $total_all_posts = 0; // Initialize total posts count for "all" tab without filters
    
    if ($tab === 'all') {
        // Get all posts first (without pagination for filtering)
        $query_args = [
            'post_type' => $post_type,
            'posts_per_page' => -1, // Get all posts for filtering
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
        ];
        
        // Add meta query if custom field filter is set
        if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
            $query_args['meta_query'] = [
                [
                    'key' => $custom_field_meta_key,
                    'value' => $custom_field_meta_value,
                    'compare' => '=',
                ],
            ];
        }
        
        // Polylang: Ensure we get posts in current language
        if (function_exists('pll_current_language')) {
            $current_lang = pll_current_language();
            if ($current_lang) {
                $query_args['lang'] = $current_lang;
            }
        }
        
        $all_posts = get_posts($query_args);
        
        // Additional filtering for array meta values (meta_query doesn't handle arrays well)
        if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
            $all_posts = $filter_posts_by_custom_field($all_posts, $custom_field_meta_key, $custom_field_meta_value);
        }
        
        // Sort posts first (before filtering) to ensure consistent ordering
        if ($orderby === 'date') {
            usort($all_posts, function($a, $b) use ($order) {
                $date_a = strtotime($a->post_date);
                $date_b = strtotime($b->post_date);
                return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
            });
        } elseif ($orderby === 'title') {
            usort($all_posts, function($a, $b) use ($order) {
                $cmp = strcasecmp($a->post_title, $b->post_title);
                return $order === 'DESC' ? -$cmp : $cmp;
            });
        } elseif ($orderby === 'menu_order') {
            usort($all_posts, function($a, $b) use ($order) {
                $result = $a->menu_order - $b->menu_order;
                return $order === 'DESC' ? -$result : $result;
            });
        }
        
        // Apply filters if search term or dropdown filter is active (for all styles)
        if (!empty($filter_search_term) || !empty($filter_dropdown_value)) {
            $all_filtered_posts = [];
            
            foreach ($all_posts as $post_filter) {
                $matches = true;
                
                // Apply search filter - search ONLY in post title
                if (!empty($filter_search_term)) {
                    $post_title = strtolower(get_the_title($post_filter->ID));
                    $search_term_lower = strtolower($filter_search_term);
                    
                    if (strpos($post_title, $search_term_lower) === false) {
                        $matches = false;
                    }
                }
                
                // Apply dropdown filter
                if ($matches && !empty($filter_dropdown_value)) {
                    $post_filter_value = '';
                    
                    if (!empty($filter_dropdown_taxonomy)) {
                        // Filter by taxonomy
                        if (!empty($filter_dropdown_custom_field) && function_exists('get_field')) {
                            // Get value from ACF field and match to taxonomy term
                            $acf_field_value = get_field($filter_dropdown_custom_field, $post_filter->ID);
                            $acf_field_type = omsar_get_acf_field_type_for_ajax($filter_dropdown_custom_field);
                            
                            if (!empty($acf_field_value)) {
                                if ($acf_field_type === 'taxonomy') {
                                    $term_id = null;
                                    if (is_array($acf_field_value)) {
                                        $term_item = reset($acf_field_value);
                                        if (is_object($term_item) && isset($term_item->term_id)) {
                                            $term_id = $term_item->term_id;
                                        } elseif (is_numeric($term_item)) {
                                            $term_id = $term_item;
                                        }
                                    } elseif (is_object($acf_field_value) && isset($acf_field_value->term_id)) {
                                        $term_id = $acf_field_value->term_id;
                                    } elseif (is_numeric($acf_field_value)) {
                                        $term_id = $acf_field_value;
                                    }
                                    
                                    if ($term_id) {
                                        $term = get_term($term_id);
                                        if ($term && !is_wp_error($term) && $term->taxonomy === $filter_dropdown_taxonomy) {
                                            $post_filter_value = (string) $term_id;
                                        }
                                    }
                                } else {
                                    // For non-taxonomy fields, get value as text and match by name
                                    $acf_value_text = omsar_get_badge_text_for_ajax($post_filter->ID, 'custom_field', '', $filter_dropdown_custom_field, 'name');
                                    if (!empty($acf_value_text)) {
                                        $filter_terms = get_terms([
                                            'taxonomy' => $filter_dropdown_taxonomy,
                                            'hide_empty' => false,
                                        ]);
                                        if (!is_wp_error($filter_terms) && !empty($filter_terms)) {
                                            foreach ($filter_terms as $term) {
                                                if (strcasecmp($term->name, $acf_value_text) === 0) {
                                                    $post_filter_value = (string) $term->term_id;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            // Default: get from taxonomy assignment
                            $filter_terms = get_the_terms($post_filter->ID, $filter_dropdown_taxonomy);
                            if ($filter_terms && !is_wp_error($filter_terms) && !empty($filter_terms)) {
                                $filter_term = array_shift($filter_terms);
                                $post_filter_value = (string) $filter_term->term_id;
                            }
                        }
                    }
                    
                    if ($post_filter_value !== $filter_dropdown_value) {
                        $matches = false;
                    }
                }
                
                if ($matches) {
                    $all_filtered_posts[] = $post_filter;
                }
            }
            
            // Posts are already sorted above, so filtered posts maintain the same order
            // No need to sort again - just apply pagination
            
            // Apply pagination to filtered posts
            // Ensure offset doesn't exceed array length
            $total_filtered = count($all_filtered_posts);
            
            if ($offset >= $total_filtered) {
                // No more posts to show
                $posts = [];
            } else {
                // Apply pagination: get posts starting from offset
                $posts = array_slice($all_filtered_posts, $offset, $posts_per_page);
            }
        } else {
            // No filters: paginate the same sorted list used for the initial render.
            $total_all_posts = count( $all_posts );
            if ( $offset >= $total_all_posts ) {
                $posts = [];
            } else {
                $posts = array_slice( $all_posts, $offset, $posts_per_page );
            }
        }
    } else {
        // Get posts for specific term
        if ($use_acf && function_exists('get_field')) {
            // Filter by ACF field
            $all_posts_for_filter_args = [
                'post_type' => $post_type,
                'posts_per_page' => -1,
                'post_status' => 'publish',
            ];
            
            // Add meta query if custom field filter is set
            if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
                $all_posts_for_filter_args['meta_query'] = [
                    [
                        'key' => $custom_field_meta_key,
                        'value' => $custom_field_meta_value,
                        'compare' => '=',
                    ],
                ];
            }
            
            $all_posts_for_filter = get_posts($all_posts_for_filter_args);
            
            $filtered_posts = [];
            foreach ($all_posts_for_filter as $post_filter) {
                $acf_category = get_field($acf_field, $post_filter->ID);
                $matches = false;
                
                if (!empty($acf_category)) {
                    if (is_array($acf_category)) {
                        foreach ($acf_category as $cat) {
                            $cat_name = is_array($cat) ? ($cat['label'] ?? $cat['value'] ?? '') : $cat;
                            if ($cat_name === $term_name) {
                                $matches = true;
                                break;
                            }
                        }
                    } else {
                        $cat_name = is_array($acf_category) ? ($acf_category['label'] ?? $acf_category['value'] ?? '') : $acf_category;
                        if ($cat_name === $term_name) {
                            $matches = true;
                        }
                    }
                }
                
                if ($matches) {
                    $filtered_posts[] = $post_filter;
                }
            }
            
            // Apply custom field filter if set
            if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
                $filtered_posts = $filter_posts_by_custom_field($filtered_posts, $custom_field_meta_key, $custom_field_meta_value);
            }
            
            // Sort posts
            if ($orderby === 'date') {
                usort($filtered_posts, function($a, $b) use ($order) {
                    $date_a = strtotime($a->post_date);
                    $date_b = strtotime($b->post_date);
                    return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
                });
            } elseif ($orderby === 'title') {
                usort($filtered_posts, function($a, $b) use ($order) {
                    $cmp = strcasecmp($a->post_title, $b->post_title);
                    return $order === 'DESC' ? -$cmp : $cmp;
                });
            }
            
            // Apply offset and limit
            $posts = array_slice($filtered_posts, $offset, $posts_per_page);
        } else {
            // Use standard taxonomy query
            $query_args = [
                'post_type' => $post_type,
                'posts_per_page' => $posts_per_page,
                'offset' => $offset,
                'post_status' => 'publish',
                'tax_query' => [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $term_id,
                        'include_children' => false,
                    ],
                ],
                'orderby' => $orderby,
                'order' => $order,
                'suppress_filters' => false,
                'no_found_rows' => true,
            ];
            
            // Add meta query if custom field filter is set
            if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
                $query_args['meta_query'] = [
                    [
                        'key' => $custom_field_meta_key,
                        'value' => $custom_field_meta_value,
                        'compare' => '=',
                    ],
                ];
            }
            
            // Use WP_Query for better Polylang support
            $term_query = new WP_Query($query_args);
            $posts = $term_query->posts;
            
            // Apply custom field filter if set (for array meta values)
            if (!empty($custom_field_meta_key) && !empty($custom_field_meta_value)) {
                $posts = $filter_posts_by_custom_field($posts, $custom_field_meta_key, $custom_field_meta_value);
            }
            
            // If no posts found and Polylang is active, try getting all language versions of the term
            if (empty($posts) && function_exists('pll_get_term_translations') && $term_id) {
                $term_translations = pll_get_term_translations($term_id);
                if (!empty($term_translations) && count($term_translations) > 1) {
                    $all_term_ids = array_values($term_translations);
                    $query_args['tax_query'][0]['terms'] = $all_term_ids;
                    $term_query = new WP_Query($query_args);
                    $posts = $term_query->posts;
                }
            }
            
            // If still no posts, try with slug instead of term_id
            if (empty($posts) && !empty($term_name)) {
                $query_args['tax_query'][0]['field'] = 'slug';
                $query_args['tax_query'][0]['terms'] = sanitize_title($term_name);
                $term_query = new WP_Query($query_args);
                $posts = $term_query->posts;
            }
            
            // Last resort: Get all posts and filter manually
            if (empty($posts) && $term_id) {
                $all_posts_check = get_posts([
                    'post_type' => $post_type,
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'suppress_filters' => false,
                ]);
                
                $filtered_posts = [];
                foreach ($all_posts_check as $post_check) {
                    $post_terms = wp_get_post_terms($post_check->ID, $taxonomy, ['fields' => 'ids']);
                    if (in_array($term_id, $post_terms)) {
                        $filtered_posts[] = $post_check;
                    }
                }
                
                // Sort and apply offset/limit
                if ($orderby === 'date') {
                    usort($filtered_posts, function($a, $b) use ($order) {
                        $date_a = strtotime($a->post_date);
                        $date_b = strtotime($b->post_date);
                        return $order === 'DESC' ? $date_b - $date_a : $date_a - $date_b;
                    });
                } elseif ($orderby === 'title') {
                    usort($filtered_posts, function($a, $b) use ($order) {
                        $cmp = strcasecmp($a->post_title, $b->post_title);
                        return $order === 'DESC' ? -$cmp : $cmp;
                    });
                }
                
                // Apply offset and limit
                $posts = array_slice($filtered_posts, $offset, $posts_per_page);
            }
        }
    }
    
    if (empty($posts)) {
        // Calculate total filtered posts for response
        $total_filtered_posts = 0;
        if ($listing_style === 'style4' && (!empty($filter_search_term) || !empty($filter_dropdown_value))) {
            $total_filtered_posts = count($all_filtered_posts);
        }
        
        wp_send_json_success([
            'html' => '',
            'has_more' => false,
            'total_filtered_posts' => $total_filtered_posts,
        ]);
    }
    
    // Render post cards
    ob_start();
    foreach ($posts as $post) {
        setup_postdata($post);
        $post_id = $post->ID;
        
        // Get featured image or default image
        $post_image = '';
        if (has_post_thumbnail($post_id)) {
            $post_image = get_the_post_thumbnail_url($post_id, ($listing_style === 'style4' || $listing_style === 'style5') ? 'large' : 'medium');
        } elseif (!empty($default_image)) {
            $post_image = $default_image;
        }
        
        $post_title = get_the_title($post_id);
        $post_date = get_the_date('F j, Y', $post_id);
        $post_link = get_permalink($post_id);
        
        // Get excerpt for style4 and style5
        $post_excerpt = '';
        $post_excerpt_for_search = '';
        if ($listing_style === 'style4' || $listing_style === 'style5') {
            if (has_excerpt($post_id)) {
                $post_excerpt = wp_trim_words(get_the_excerpt($post_id), 20, '...');
                $post_excerpt_for_search = strtolower(strip_tags(get_the_excerpt($post_id)));
            } elseif (!empty($post->post_content)) {
                $post_excerpt = wp_trim_words(strip_shortcodes($post->post_content), 20, '...');
                $post_excerpt_for_search = strtolower(strip_tags(wp_trim_words(strip_shortcodes($post->post_content), 50, '')));
            }
        }
        
        // Get post author for style5
        $post_author = '';
        if ($listing_style === 'style5') {
            $post_author = get_the_author_meta('display_name', $post->post_author);
        }
        
        // Get post categories for style5
        $post_categories = [];
        if ($listing_style === 'style5') {
            $categories = get_the_category($post_id);
            if (!empty($categories) && !is_wp_error($categories)) {
                $post_categories = $categories;
            }
        }
        
        // Update date format for style5
        if ($listing_style === 'style5') {
            $post_date = get_the_date('M j, Y', $post_id);
        }
        
        // Get badge text based on badge source (style4)
        $badge_text = '';
        if ($listing_style === 'style4') {
            $badge_text = omsar_get_badge_text_for_ajax($post_id, $badge_source, $badge_taxonomy, $badge_custom_field, $badge_taxonomy_display);
        }

        // Get filter value for Style 4 dropdown filter
        $filter_value = '';
        if ($listing_style === 'style4' && !empty($style4_dropdown_source)) {
            if ($style4_dropdown_source === 'taxonomy' && !empty($style4_dropdown_taxonomy)) {
                $filter_terms = get_the_terms($post_id, $style4_dropdown_taxonomy);
                if ($filter_terms && !is_wp_error($filter_terms) && !empty($filter_terms)) {
                    $filter_term = array_shift($filter_terms);
                    $filter_value = (string) $filter_term->term_id;
                }
            } elseif ($style4_dropdown_source === 'acf_field' && !empty($style4_dropdown_acf_field) && function_exists('get_field')) {
                $filter_field_value = get_field($style4_dropdown_acf_field, $post_id);
                $filter_field_type = omsar_get_acf_field_type_for_ajax($style4_dropdown_acf_field);
                
                if (!empty($filter_field_value)) {
                    if ($filter_field_type === 'taxonomy') {
                        // Handle taxonomy field - ACF can return various formats
                        $term = null;
                        $term_id = null;
                        
                        // Handle array of terms
                        if (is_array($filter_field_value)) {
                            // Get first term from array
                            $term_item = reset($filter_field_value);
                            
                            if (is_object($term_item)) {
                                // Check if it's a term object
                                if (isset($term_item->term_id)) {
                                    $term_id = $term_item->term_id;
                                } elseif (isset($term_item->ID)) {
                                    // Might be a post object, try to get term
                                    $term_id = $term_item->ID;
                                }
                            } elseif (is_numeric($term_item)) {
                                // Term ID
                                $term_id = $term_item;
                            } elseif (is_array($term_item) && isset($term_item['term_id'])) {
                                // Array with term_id
                                $term_id = $term_item['term_id'];
                            } elseif (is_string($term_item) && is_numeric($term_item)) {
                                // String that's a number
                                $term_id = (int) $term_item;
                            }
                            
                            if ($term_id) {
                                $term = get_term($term_id);
                            }
                        } elseif (is_object($filter_field_value)) {
                            // Single term object
                            if (isset($filter_field_value->term_id)) {
                                $term_id = $filter_field_value->term_id;
                                $term = $filter_field_value;
                            } elseif (isset($filter_field_value->ID)) {
                                $term_id = $filter_field_value->ID;
                                $term = get_term($term_id);
                            }
                        } elseif (is_numeric($filter_field_value)) {
                            // Term ID (numeric)
                            $term_id = $filter_field_value;
                            $term = get_term($term_id);
                        } elseif (is_string($filter_field_value) && is_numeric($filter_field_value)) {
                            // Term ID (string)
                            $term_id = (int) $filter_field_value;
                            $term = get_term($term_id);
                        }

                        // If we have a term, use its ID; otherwise try to use the term_id we extracted
                        if ($term && !is_wp_error($term)) {
                            $filter_value = (string) $term->term_id;
                        } elseif ($term_id) {
                            $filter_value = (string) $term_id;
                        } else {
                            // Last resort: try to get field object and extract taxonomy
                            // This handles cases where ACF returns values in unexpected formats
                            if (function_exists('get_field_object')) {
                                $field_object = get_field_object($style4_dropdown_acf_field, $post_id);
                                if ($field_object && isset($field_object['taxonomy'])) {
                                    $taxonomy_name = $field_object['taxonomy'];
                                    // Try to get terms for this post in the taxonomy
                                    $post_terms = get_the_terms($post_id, $taxonomy_name);
                                    if ($post_terms && !is_wp_error($post_terms) && !empty($post_terms)) {
                                        $first_term = array_shift($post_terms);
                                        $filter_value = (string) $first_term->term_id;
                                    }
                                }
                            }
                        }
                    } else {
                        // Regular custom field - use value as is
                        if (is_array($filter_field_value) && !empty($filter_field_value[0])) {
                            $filter_value = is_object($filter_field_value[0]) ? ($filter_field_value[0]->name ?? (string) $filter_field_value[0]) : (string) $filter_field_value[0];
                        } else {
                            $filter_value = is_object($filter_field_value) ? ($filter_field_value->name ?? (string) $filter_field_value) : (string) $filter_field_value;
                        }
                    }
                }
            }
        }
        
        // Get PDFs from documents repeater field
        $publication_pdfs = [];
        if (function_exists('get_field') && function_exists('have_rows')) {
            if (have_rows('documents', $post_id)) {
                while (have_rows('documents', $post_id)) {
                    the_row();
                    $pdf_file = get_sub_field('pdf');
                    if ($pdf_file) {
                        // Handle both file ID and file array
                        if (is_numeric($pdf_file)) {
                            $pdf_url = wp_get_attachment_url($pdf_file);
                            $pdf_title = get_the_title($pdf_file);
                        } elseif (is_array($pdf_file)) {
                            $pdf_url = $pdf_file['url'] ?? '';
                            $pdf_title = $pdf_file['title'] ?? basename($pdf_url);
                        } else {
                            $pdf_url = $pdf_file;
                            $pdf_title = basename($pdf_url);
                        }
                        
                        if ($pdf_url) {
                            $publication_pdfs[] = [
                                'url' => $pdf_url,
                                'title' => $pdf_title,
                            ];
                        }
                    }
                }
            }
        }
        
        // Build style attribute for style4 background image
        $item_style = '';
        if ($listing_style === 'style4' && !empty($post_image)) {
            $item_style = 'style="background-image: url(' . esc_url($post_image) . ');"';
        }

        // Build data attributes for filtering
        $filter_data_attrs = '';
        if ($listing_style === 'style4' && !empty($filter_value)) {
            $filter_data_attrs = 'data-filter-value="' . esc_attr($filter_value) . '"';
        }

        // Build card classes and clickable data attribute
        $card_classes = 'omsar-post-card omsar-post-card-' . esc_attr($listing_style);
        if ($clickable_cards) {
            $card_classes .= ' omsar-post-card-clickable';
        }
        ?>
        <div class="<?php echo esc_attr($card_classes); ?>" 
             data-post-title="<?php echo esc_attr(strtolower($post_title)); ?>" 
             data-post-excerpt="<?php echo esc_attr($post_excerpt_for_search); ?>"
             <?php echo $clickable_cards ? 'data-post-link="' . esc_url($post_link) . '"' : ''; ?>
             <?php echo $item_style; ?>
             <?php echo $filter_data_attrs; ?>>
            <?php if ($listing_style === 'style4'): ?>
                <!-- Style 4: Related Posts Style with gradient overlay -->
                <div class="omsar-post-content">
                    <h3 class="omsar-post-title">
                        <?php if ($clickable_cards): ?>
                            <a href="<?php echo esc_url($post_link); ?>">
                                <?php echo esc_html($post_title); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($post_title); ?>
                        <?php endif; ?>
                    </h3>
                    
                    <?php if (!empty($badge_text)): ?>
                        <span class="omsar-post-badge"><?php echo esc_html($badge_text); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($post_excerpt)): ?>
                        <div class="omsar-post-excerpt">
                            <?php echo wp_kses_post($post_excerpt); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_read_more): ?>
                        <a href="<?php echo esc_url($post_link); ?>" class="omsar-post-read-more">
                            <?php echo !empty($read_more_text) ? esc_html($read_more_text) : (function_exists('pll__') ? pll__('Read More') : esc_html__('Read More', 'omsar')); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif ($listing_style === 'style3'): ?>
                <div class="omsar-post-content">
                    <h3 class="omsar-post-title">
                        <?php if ($clickable_cards): ?>
                            <a href="<?php echo esc_url($post_link); ?>">
                                <?php echo esc_html($post_title); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($post_title); ?>
                        <?php endif; ?>
                    </h3>
                    <?php if ($post_date): ?>
                        <div class="omsar-post-date">
                            <i class="bi bi-calendar3"></i>
                            <?php echo esc_html($post_date); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($publication_pdfs)): ?>
                        <div class="omsar-post-pdfs">
                            <strong><?php echo function_exists('pll__') ? pll__('Downloads') : esc_html__('Downloads', 'omsar'); ?>:</strong>
                            <ul class="pdf-list">
                                <?php foreach ($publication_pdfs as $pdf):
                                    $pdf_url = $pdf['url'] ?? '';
                                    $pdf_title = $pdf['title'] ?? basename($pdf_url);
                                    if ($pdf_url):
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <?php echo esc_html($pdf_title); ?>
                                        </a>
                                    </li>
                                <?php
                                    endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($listing_style === 'style5'): ?>
                <?php if ($style5_content_overlay && $post_image): ?>
                    <!-- Style 5: Content Overlay on Image -->
                    <div class="omsar-post-card-style5-overlay" style="background-image: url(<?php echo esc_url($post_image); ?>);">
                        <div class="omsar-post-card-style5-overlay-content">
                            <?php if ($style5_show_categories && !empty($post_categories)): ?>
                                <div class="omsar-post-categories">
                                    <?php foreach ($post_categories as $category): ?>
                                        <span class="omsar-post-category-tag"><?php echo esc_html(strtoupper($category->name)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="omsar-post-title">
                                <?php if ($clickable_cards): ?>
                                    <a href="<?php echo esc_url($post_link); ?>">
                                        <?php echo esc_html($post_title); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo esc_html($post_title); ?>
                                <?php endif; ?>
                            </h3>
                            
                            <?php if ($style5_show_excerpt && !empty($post_excerpt)): ?>
                                <div class="omsar-post-excerpt">
                                    <?php echo esc_html($post_excerpt); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($style5_show_date || $style5_show_author): ?>
                                <div class="omsar-post-meta">
                                    <?php if ($style5_show_date && $post_date): ?>
                                        <span class="omsar-post-date"><?php echo esc_html($post_date); ?></span>
                                    <?php endif; ?>
                                    <?php if ($style5_show_author && !empty($post_author)): ?>
                                        <span class="omsar-post-author"><?php echo esc_html__('By', 'omsar'); ?> <?php echo esc_html($post_author); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($style5_side_layout): ?>
                    <!-- Style 5: Side Layout (Image Left, Content Right) -->
                    <?php if ($post_image): ?>
                        <div class="omsar-post-image-side">
                            <?php if ($clickable_cards): ?>
                                <a href="<?php echo esc_url($post_link); ?>">
                                    <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="omsar-post-content-side">
                        <?php if ($style5_show_categories && !empty($post_categories)): ?>
                            <div class="omsar-post-categories">
                                <?php foreach ($post_categories as $category): ?>
                                    <span class="omsar-post-category-tag"><?php echo esc_html(strtoupper($category->name)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="omsar-post-title">
                            <?php if ($clickable_cards): ?>
                                <a href="<?php echo esc_url($post_link); ?>">
                                    <?php echo esc_html($post_title); ?>
                                </a>
                            <?php else: ?>
                                <?php echo esc_html($post_title); ?>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if ($style5_show_date && $post_date): ?>
                            <div class="omsar-post-date-side">
                                <?php echo esc_html($post_date); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($style5_show_excerpt && !empty($post_excerpt)): ?>
                            <div class="omsar-post-excerpt">
                                <?php echo esc_html($post_excerpt); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Style 5: Normal Layout (Image and Content Separate) -->
                    <?php if ($post_image): ?>
                        <div class="omsar-post-image-header">
                            <?php if ($clickable_cards): ?>
                                <a href="<?php echo esc_url($post_link); ?>">
                                    <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="omsar-post-content">
                        <?php if ($style5_show_categories && !empty($post_categories)): ?>
                            <div class="omsar-post-categories">
                                <?php foreach ($post_categories as $category): ?>
                                    <span class="omsar-post-category-tag"><?php echo esc_html(strtoupper($category->name)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="omsar-post-title">
                            <?php if ($clickable_cards): ?>
                                <a href="<?php echo esc_url($post_link); ?>">
                                    <?php echo esc_html($post_title); ?>
                                </a>
                            <?php else: ?>
                                <?php echo esc_html($post_title); ?>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if ($style5_show_excerpt && !empty($post_excerpt)): ?>
                            <div class="omsar-post-excerpt">
                                <?php echo esc_html($post_excerpt); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($style5_show_date || $style5_show_author): ?>
                            <div class="omsar-post-meta">
                                <?php if ($style5_show_date && $post_date): ?>
                                    <span class="omsar-post-date"><?php echo esc_html($post_date); ?></span>
                                <?php endif; ?>
                                <?php if ($style5_show_author && !empty($post_author)): ?>
                                    <span class="omsar-post-author"><?php echo esc_html__('By', 'omsar'); ?> <?php echo esc_html($post_author); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php elseif ($listing_style !== 'style4' && $post_image): ?>
                <div class="omsar-post-image">
                    <?php if ($clickable_cards): ?>
                        <a href="<?php echo esc_url($post_link); ?>">
                            <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo esc_url($post_image); ?>" alt="<?php echo esc_attr($post_title); ?>">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($listing_style !== 'style3' && $listing_style !== 'style4' && $listing_style !== 'style5'): ?>
                <div class="omsar-post-content">
                    <h3 class="omsar-post-title">
                        <?php if ($clickable_cards): ?>
                            <a href="<?php echo esc_url($post_link); ?>">
                                <?php echo esc_html($post_title); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html($post_title); ?>
                        <?php endif; ?>
                    </h3>
                    <?php if ($post_date): ?>
                        <div class="omsar-post-date">
                            <i class="bi bi-calendar3"></i>
                            <?php echo esc_html($post_date); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($publication_pdfs)): ?>
                        <div class="omsar-post-pdfs">
                            <strong><?php echo function_exists('pll__') ? pll__('Downloads') : esc_html__('Downloads', 'omsar'); ?>:</strong>
                            <ul class="pdf-list">
                                <?php foreach ($publication_pdfs as $pdf):
                                    $pdf_url = $pdf['url'] ?? '';
                                    $pdf_title = $pdf['title'] ?? basename($pdf_url);
                                    if ($pdf_url):
                                ?>
                                    <li>
                                        <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener noreferrer" class="document-link">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <?php echo esc_html($pdf_title); ?>
                                        </a>
                                    </li>
                                <?php
                                    endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Calculate has_more properly
    $total_loaded = $offset + count($posts);
    $has_more = false;
    $total_filtered_posts = 0;
    
    // If filters are applied, use filtered posts count (for all styles)
    if (!empty($filter_search_term) || !empty($filter_dropdown_value)) {
        $total_filtered_posts = count($all_filtered_posts);
        $has_more = $total_loaded < $total_filtered_posts;
    } elseif ($tab === 'all' && empty($filter_search_term) && empty($filter_dropdown_value)) {
        // For "all" tab without filters, use total from WP_Query
        if (isset($total_all_posts) && $total_all_posts > 0) {
            $total_filtered_posts = $total_all_posts;
            $has_more = $total_loaded < $total_all_posts;
        } else {
            // Fallback: use simple check if total_all_posts not available
            $has_more = count($posts) === $posts_per_page;
        }
    } elseif ($use_acf && $tab !== 'all' && !empty($acf_field)) {
        // Count total filtered posts for ACF filtering
        $all_posts_for_count = get_posts([
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);
        
        $total_filtered = 0;
        foreach ($all_posts_for_count as $post_filter) {
            $acf_category = get_field($acf_field, $post_filter->ID);
            $matches = false;
            
            if (!empty($acf_category)) {
                if (is_array($acf_category)) {
                    foreach ($acf_category as $cat) {
                        $cat_name = is_array($cat) ? ($cat['label'] ?? $cat['value'] ?? '') : $cat;
                        if ($cat_name === $term_name) {
                            $matches = true;
                            break;
                        }
                    }
                } else {
                    $cat_name = is_array($acf_category) ? ($acf_category['label'] ?? $acf_category['value'] ?? '') : $acf_category;
                    if ($cat_name === $term_name) {
                        $matches = true;
                    }
                }
            }
            
            if ($matches) {
                $total_filtered++;
            }
        }
        
        $total_filtered_posts = $total_filtered;
        $has_more = $total_loaded < $total_filtered;
    } else {
        // For other cases, use simple check
        $has_more = count($posts) === $posts_per_page;
    }
    
    wp_send_json_success([
        'html' => $html,
        'has_more' => $has_more,
        'total_filtered_posts' => $total_filtered_posts > 0 ? $total_filtered_posts : ($total_all_posts > 0 ? $total_all_posts : null),
    ]);
}

/**
 * Custom orderby filter for events to sort by custom_date properly
 * Converts date strings to timestamps for correct chronological sorting
 * 
 * IMPORTANT: This is needed if custom_date is stored in d/m/Y format (e.g., "19/12/2025")
 * If dates are stored in Y-m-d format (e.g., "2025-12-19"), you can remove this function
 * and WordPress's default meta_value sorting will work fine.
 */
add_filter('posts_orderby', 'omsar_events_custom_date_orderby', 10, 2);
function omsar_events_custom_date_orderby($orderby, $query) {
    // Only apply to events queries that order by custom_date
    if (isset($query->query_vars['meta_key']) && $query->query_vars['meta_key'] === 'custom_date' 
        && isset($query->query_vars['orderby']) && $query->query_vars['orderby'] === 'meta_value') {
        
        global $wpdb;
        $order = isset($query->query_vars['order']) ? $query->query_vars['order'] : 'DESC';
        
        // Use STR_TO_DATE to convert date strings to MySQL datetime for proper sorting
        // Handles format: "19/12/2025 7:05 am" (d/m/Y g:i a)
        // MySQL will try to parse the date, and if it fails, it will use NULL (which sorts last)
        // We use COALESCE to handle NULL values by falling back to the original string
        $orderby = "COALESCE(STR_TO_DATE({$wpdb->postmeta}.meta_value, '%d/%m/%Y %h:%i %p'), " .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%d/%m/%Y'), " .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%Y-%m-%d'), " .
                   "{$wpdb->postmeta}.meta_value) " . $order;
    }
    return $orderby;
}

/**
 * Custom orderby filter for recruitments to sort by closing_date properly
 * Handles multiple date formats: YYYYMMDD (numeric), Y-m-d, and d/m/Y
 */
add_filter('posts_orderby', 'omsar_recruitments_closing_date_orderby', 10, 2);
function omsar_recruitments_closing_date_orderby($orderby, $query) {
    // Only apply to recruitments queries that order by closing_date
    if (isset($query->query_vars['meta_key']) && $query->query_vars['meta_key'] === 'closing_date' 
        && isset($query->query_vars['orderby']) && $query->query_vars['orderby'] === 'meta_value') {
        
        global $wpdb;
        $order = isset($query->query_vars['order']) ? $query->query_vars['order'] : 'DESC';
        
        // Handle multiple date formats:
        // 1. YYYYMMDD (numeric, 8 digits) - convert to date for proper sorting
        // 2. Y-m-d format (e.g., "2025-12-19")
        // 3. d/m/Y format (e.g., "19/12/2025")
        // NULL values (posts without closing_date) will sort last
        $orderby = "COALESCE(" .
                   "CASE " .
                   "WHEN {$wpdb->postmeta}.meta_value REGEXP '^[0-9]{8}$' THEN " .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%Y%m%d') " .
                   "ELSE " .
                   "COALESCE(" .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%Y-%m-%d'), " .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%d/%m/%Y'), " .
                   "STR_TO_DATE({$wpdb->postmeta}.meta_value, '%Y%m%d')" .
                   ") " .
                   "END, " .
                   "'9999-12-31'" . // Posts without closing_date sort last
                   ") " . $order;
    }
    return $orderby;
}

/**
 * AJAX handler for filtering events posts by custom_date field
 */
add_action('wp_ajax_filter_events', 'omsar_filter_events');
add_action('wp_ajax_nopriv_filter_events', 'omsar_filter_events');

function omsar_filter_events() {
    // Verify nonce for security
    check_ajax_referer('load_more_events_nonce', 'nonce');

    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'events';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build meta_query for filtering by custom_date
    $meta_query = array(
        array(
            'key'     => 'post_type_option',
            'value'   => $post_type_value,
            'compare' => '='
        )
    );

    // Add custom_date filtering if month or year is set
    if (!empty($filter_year) || !empty($filter_month)) {
        if (!empty($filter_year) && !empty($filter_month)) {
            // Both month and year specified - filter by exact match
            $year = intval($filter_year);
            $month = intval($filter_month);
            $start_date = sprintf('%04d-%02d-01', $year, $month);
            $end_date = date('Y-m-t', strtotime($start_date)); // Last day of the month
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_year)) {
            // Only year specified
            $year = intval($filter_year);
            $start_date = sprintf('%04d-01-01', $year);
            $end_date = sprintf('%04d-12-31', $year);
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_month)) {
            // Only month specified - need to check across all years
            $month = str_pad(intval($filter_month), 2, '0', STR_PAD_LEFT);
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => '-'.$month.'-',
                'compare' => 'LIKE'
            );
        }
    }

    // Query for filtered posts (page 1)
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => 1,
        'meta_query' => $meta_query,
        'meta_key' => 'custom_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'update_post_term_cache' => false,
        'no_found_rows' => false
    );

    $events_query = new WP_Query($args);
    $total_found = $events_query->found_posts;

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    ob_start();

    if ($events_query->have_posts()) {
        while ($events_query->have_posts()) {
            $events_query->the_post();
            
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
            if (!$image_url) {
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            }
            if (!$image_url) {
                $image_url = $default_image;
            }
            
            $title = get_the_title();
            $custom_date_raw = get_post_meta(get_the_ID(), 'custom_date', true);

            $date = '';
            $is_upcoming = false;

            if ($custom_date_raw) {
                $custom_timestamp = strtotime($custom_date_raw);
                $today_timestamp  = strtotime(date('Y-m-d'));

                $date = date('d M Y', $custom_timestamp);

                if ($custom_timestamp >= $today_timestamp) {
                    $is_upcoming = true;
                }
            }
            
            $permalink = get_permalink();
            ?>
            <div class="col-lg-3 col-md-6 news-card-item">
                <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                    <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <?php if ($is_upcoming) : ?>
                            <span class="news-badge"><?php pll_e('Upcoming'); ?></span>
                        <?php endif; ?>
                        <div class="news-overlay"></div>
                        <div class="news-content">
                            <div class="news-date mb-2">
                                <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                                <span><?php echo esc_html($date); ?></span>
                            </div>
                            <h3 class="news-title pt-4">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
        }
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    // Calculate if there are more posts
    $has_more = ($total_found > $per_page);

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'total' => $total_found,
        'loaded' => $events_query->post_count,
        'count' => $events_query->post_count
    ));
}

/**
 * AJAX handler for loading more events posts
 */
add_action('wp_ajax_load_more_events', 'omsar_load_more_events');
add_action('wp_ajax_nopriv_load_more_events', 'omsar_load_more_events');

function omsar_load_more_events() {
    // Verify nonce for security
    check_ajax_referer('load_more_events_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'events';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build meta_query for filtering by custom_date
    $meta_query = array(
        array(
            'key'     => 'post_type_option',
            'value'   => $post_type_value,
            'compare' => '='
        )
    );

    // Add custom_date filtering if month or year is set
    if (!empty($filter_year) || !empty($filter_month)) {
        if (!empty($filter_year) && !empty($filter_month)) {
            // Both month and year specified - filter by exact match
            $year = intval($filter_year);
            $month = intval($filter_month);
            $start_date = sprintf('%04d-%02d-01', $year, $month);
            $end_date = date('Y-m-t', strtotime($start_date)); // Last day of the month
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_year)) {
            // Only year specified
            $year = intval($filter_year);
            $start_date = sprintf('%04d-01-01', $year);
            $end_date = sprintf('%04d-12-31', $year);
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_month)) {
            // Only month specified - need to check across all years
            $month = str_pad(intval($filter_month), 2, '0', STR_PAD_LEFT);
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => '-'.$month.'-',
                'compare' => 'LIKE'
            );
        }
    }

    // Single optimized query - more reliable than two-step approach
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => $page,
        'meta_query' => $meta_query,
        'meta_key' => 'custom_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        // Performance optimizations
        'update_post_term_cache' => false, // Don't update term cache
        'no_found_rows' => false // We need found_posts for pagination
    );

    // Single query to get all post data
    $events_query = new WP_Query($args);
    $total_found = $events_query->found_posts;
    
    // Calculate if there are more posts after this page
    // Page 1 shows posts 1-8, Page 2 shows posts 9-16, etc.
    // Total loaded after this page = page * per_page (if full page) or less (if last page)
    $posts_on_this_page = $events_query->post_count;
    
    // Calculate total loaded: (page - 1) * per_page + actual posts on this page
    $total_loaded_so_far = ($page - 1) * $per_page + $posts_on_this_page;
    
    // There are more posts if total_found is greater than what we've loaded so far
    $has_more = ($total_found > $total_loaded_so_far);
    
    // Debug logging (remove in production)
    error_log("AJAX Load More Events Debug - Page: $page, Posts on page: $posts_on_this_page, Total found: $total_found, Total loaded: $total_loaded_so_far, Has more: " . ($has_more ? 'true' : 'false'));

    // If no posts, return early
    if (!$events_query->have_posts()) {
        wp_reset_postdata();
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => $total_found,
            'loaded' => 0,
            'count' => 0
        ));
    }

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    // Use output buffering for faster HTML generation
    ob_start();
    
    while ($events_query->have_posts()) {
        $events_query->the_post();
        
        // Use optimized image size instead of 'full'
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
        if (!$image_url) {
            // Try medium size as fallback
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        }
        if (!$image_url) {
            $image_url = $default_image;
        }
        
        $title = get_the_title();
        $custom_date_raw = get_post_meta(get_the_ID(), 'custom_date', true);

        $date = '';
        $is_upcoming = false;

        if ($custom_date_raw) {
            $custom_timestamp = strtotime($custom_date_raw);
            $today_timestamp  = strtotime(date('Y-m-d'));

            $date = date('d M Y', $custom_timestamp);

            if ($custom_timestamp >= $today_timestamp) {
                $is_upcoming = true;
            }
        }        
        
        $permalink = get_permalink();
        ?>
        <div class="col-lg-3 col-md-6 news-card-item">
            <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                    <?php if ($is_upcoming) : ?>
                        <span class="news-badge"><?php pll_e('Upcoming'); ?></span>
                    <?php endif; ?> 
                    <div class="news-overlay"></div>
                    <div class="news-content">
                        <div class="news-date mb-2">
                            <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                            <span><?php echo esc_html($date); ?></span>
                        </div>
                        <h3 class="news-title pt-4">
                            <?php echo esc_html($title); ?>
                        </h3>
                        <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                    </div>
                </div>
            </a>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Ensure HTML is always a string (even if empty)
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'next_page' => $page + 1,
        'total' => $total_found,
        'loaded' => $total_loaded_so_far,
        'count' => $posts_on_this_page
    ));
}

/**
 * AJAX handler for filtering workshop posts by custom_date field
 */
add_action('wp_ajax_filter_workshops', 'omsar_filter_workshops');
add_action('wp_ajax_nopriv_filter_workshops', 'omsar_filter_workshops');

function omsar_filter_workshops() {
    // Verify nonce for security
    check_ajax_referer('load_more_workshop_nonce', 'nonce');

    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'workshops';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build meta_query for filtering by custom_date
    $meta_query = array(
        array(
            'key'     => 'post_type_option',
            'value'   => $post_type_value,
            'compare' => '='
        )
    );

    // Add custom_date filtering if month or year is set
    if (!empty($filter_year) || !empty($filter_month)) {
        if (!empty($filter_year) && !empty($filter_month)) {
            // Both month and year specified - filter by exact match
            $year = intval($filter_year);
            $month = intval($filter_month);
            $start_date = sprintf('%04d-%02d-01', $year, $month);
            $end_date = date('Y-m-t', strtotime($start_date)); // Last day of the month
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_year)) {
            // Only year specified
            $year = intval($filter_year);
            $start_date = sprintf('%04d-01-01', $year);
            $end_date = sprintf('%04d-12-31', $year);
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_month)) {
            // Only month specified - need to check across all years
            $month = str_pad(intval($filter_month), 2, '0', STR_PAD_LEFT);
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => '-'.$month.'-',
                'compare' => 'LIKE'
            );
        }
    }

    // Query for filtered posts (page 1)
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => 1,
        'meta_query' => $meta_query,
        'orderby' => 'date',
        'order' => 'DESC',
        'update_post_term_cache' => false,
        'no_found_rows' => false
    );

    $workshop_query = new WP_Query($args);
    $total_found = $workshop_query->found_posts;

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    ob_start();

    if ($workshop_query->have_posts()) {
        while ($workshop_query->have_posts()) {
            $workshop_query->the_post();
            
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
            if (!$image_url) {
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            }
            if (!$image_url) {
                $image_url = $default_image;
            }
            
            $title = get_the_title();
            $custom_date_raw = get_post_meta(get_the_ID(), 'custom_date', true);

            $date = '';
            $is_upcoming = false;

            if ($custom_date_raw) {
                $custom_timestamp = strtotime($custom_date_raw);
                $today_timestamp  = strtotime(date('Y-m-d'));

                $date = date('d M Y', $custom_timestamp);

                if ($custom_timestamp >= $today_timestamp) {
                    $is_upcoming = true;
                }
            }
            
            $permalink = get_permalink();
            ?>
            <div class="col-lg-3 col-md-6 news-card-item">
                <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                    <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <?php if ($is_upcoming) : ?>
                            <span class="news-badge"><?php pll_e('Upcoming'); ?></span>
                        <?php endif; ?>
                        <div class="news-overlay"></div>
                        <div class="news-content">
                            <div class="news-date mb-2">
                                <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                                <span><?php echo esc_html($date); ?></span>
                            </div>
                            <h3 class="news-title pt-4">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
        }
    }

    wp_reset_postdata();
    $html = ob_get_clean();

    // Calculate if there are more posts
    $has_more = ($total_found > $per_page);

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'total' => $total_found,
        'loaded' => $workshop_query->post_count,
        'count' => $workshop_query->post_count
    ));
}

/**
 * AJAX handler for loading more workshop posts
 */
add_action('wp_ajax_load_more_workshop', 'omsar_load_more_workshop');
add_action('wp_ajax_nopriv_load_more_workshop', 'omsar_load_more_workshop');

function omsar_load_more_workshop() {
    // Verify nonce for security
    check_ajax_referer('load_more_workshop_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $post_type_value = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'workshops';
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 8;
    $filter_month = isset($_POST['filter_month']) ? sanitize_text_field($_POST['filter_month']) : '';
    $filter_year = isset($_POST['filter_year']) ? sanitize_text_field($_POST['filter_year']) : '';

    // Build meta_query for filtering by custom_date
    $meta_query = array(
        array(
            'key'     => 'post_type_option',
            'value'   => $post_type_value,
            'compare' => '='
        )
    );

    // Add custom_date filtering if month or year is set
    if (!empty($filter_year) || !empty($filter_month)) {
        if (!empty($filter_year) && !empty($filter_month)) {
            // Both month and year specified - filter by exact match
            $year = intval($filter_year);
            $month = intval($filter_month);
            $start_date = sprintf('%04d-%02d-01', $year, $month);
            $end_date = date('Y-m-t', strtotime($start_date)); // Last day of the month
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_year)) {
            // Only year specified
            $year = intval($filter_year);
            $start_date = sprintf('%04d-01-01', $year);
            $end_date = sprintf('%04d-12-31', $year);
            
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => array($start_date, $end_date),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            );
        } elseif (!empty($filter_month)) {
            // Only month specified - need to check across all years
            $month = str_pad(intval($filter_month), 2, '0', STR_PAD_LEFT);
            $meta_query[] = array(
                'key'     => 'custom_date',
                'value'   => '-'.$month.'-',
                'compare' => 'LIKE'
            );
        }
    }

    // Single optimized query - more reliable than two-step approach
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => $page,
        'meta_query' => $meta_query,
        'orderby' => 'date',
        'order' => 'DESC',
        // Performance optimizations
        'update_post_term_cache' => false, // Don't update term cache
        'no_found_rows' => false // We need found_posts for pagination
    );

    // Single query to get all post data
    $workshop_query = new WP_Query($args);
    $total_found = $workshop_query->found_posts;
    
    // Calculate if there are more posts after this page
    // Page 1 shows posts 1-8, Page 2 shows posts 9-16, etc.
    // Total loaded after this page = page * per_page (if full page) or less (if last page)
    $posts_on_this_page = $workshop_query->post_count;
    
    // Calculate total loaded: (page - 1) * per_page + actual posts on this page
    $total_loaded_so_far = ($page - 1) * $per_page + $posts_on_this_page;
    
    // There are more posts if total_found is greater than what we've loaded so far
    $has_more = ($total_found > $total_loaded_so_far);
    
    // Debug logging (remove in production)
    error_log("AJAX Load More Workshop Debug - Page: $page, Posts on page: $posts_on_this_page, Total found: $total_found, Total loaded: $total_loaded_so_far, Has more: " . ($has_more ? 'true' : 'false'));

    // If no posts, return early
    if (!$workshop_query->have_posts()) {
        wp_reset_postdata();
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => $total_found,
            'loaded' => 0,
            'count' => 0
        ));
    }

    // Cache calendar icon URL
    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

    // Use output buffering for faster HTML generation
    ob_start();
    
    while ($workshop_query->have_posts()) {
        $workshop_query->the_post();
        
        // Use optimized image size instead of 'full'
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
        if (!$image_url) {
            // Try medium size as fallback
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        }
        if (!$image_url) {
            $image_url = $default_image;
        }
        
        $title = get_the_title();
        // For workshops, use custom_date if available
        $custom_date_raw = get_post_meta(get_the_ID(), 'custom_date', true);

        $date = '';
        $is_upcoming = false;

        if ($custom_date_raw) {
            $custom_timestamp = strtotime($custom_date_raw);
            $today_timestamp  = strtotime(date('Y-m-d'));

            $date = date('d M Y', $custom_timestamp);

            if ($custom_timestamp >= $today_timestamp) {
                $is_upcoming = true;
            }
        }
        $permalink = get_permalink();
        ?>
        <div class="col-lg-3 col-md-6 news-card-item">
            <a href="<?php echo esc_url($permalink); ?>" class="news-card-link">
                <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                    <?php if ($is_upcoming) : ?>
                        <span class="news-badge"><?php pll_e('Upcoming'); ?></span>
                    <?php endif; ?> 
                    <div class="news-overlay"></div>
                    <div class="news-content">
                        <div class="news-date mb-2">
                            <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                            <span><?php echo esc_html($date); ?></span>
                        </div>
                        <h3 class="news-title pt-4">
                            <?php echo esc_html($title); ?>
                        </h3>
                        <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                    </div>
                </div>
            </a>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Ensure HTML is always a string (even if empty)
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'next_page' => $page + 1,
        'total' => $total_found,
        'loaded' => $total_loaded_so_far,
        'count' => $posts_on_this_page
    ));
}

/**
 * Get badge text based on source (for AJAX handlers)
 * 
 * @param int $post_id Post ID.
 * @param string $source Badge source (taxonomy, custom_field, none).
 * @param string $taxonomy Taxonomy name.
 * @param string $custom_field Custom field name.
 * @param string $taxonomy_display Display type for taxonomy fields (name or id).
 * @return string Badge text.
 */
function omsar_get_badge_text_for_ajax($post_id, $source, $taxonomy = '', $custom_field = '', $taxonomy_display = 'name') {
    if ($source === 'none') {
        return '';
    }

    if ($source === 'taxonomy' && !empty($taxonomy)) {
        $terms = get_the_terms($post_id, $taxonomy);
        if ($terms && !is_wp_error($terms)) {
            $term = array_shift($terms);
            return $term->name;
        }
    }

    if ($source === 'custom_field' && !empty($custom_field)) {
        // Check if ACF is available and use get_field
        if (function_exists('get_field')) {
            $value = get_field($custom_field, $post_id);
        } else {
            // Fallback to get_post_meta
            $value = get_post_meta($post_id, $custom_field, true);
        }

        if (!empty($value)) {
            // Check if this is a taxonomy field
            $field_type = omsar_get_acf_field_type_for_ajax($custom_field);
            
            if ($field_type === 'taxonomy') {
                // Handle taxonomy field
                $term = null;
                
                if (is_array($value)) {
                    // Multiple terms - get first one
                    if (!empty($value[0])) {
                        if (is_object($value[0])) {
                            $term = $value[0];
                        } elseif (is_numeric($value[0])) {
                            $term = get_term($value[0]);
                        }
                    }
                } elseif (is_object($value)) {
                    // Single term object
                    $term = $value;
                } elseif (is_numeric($value)) {
                    // Term ID
                    $term = get_term($value);
                } else {
                    // Try to get term by slug or name
                    // Get field object to find taxonomy
                    if (function_exists('get_field_object')) {
                        $field_object = get_field_object($custom_field, $post_id);
                        $taxonomy_name = !empty($field_object['taxonomy']) ? $field_object['taxonomy'] : '';
                        
                        if ($taxonomy_name) {
                            $term = get_term_by('slug', $value, $taxonomy_name);
                            if (!$term) {
                                $term = get_term_by('name', $value, $taxonomy_name);
                            }
                        }
                    }
                }

                if ($term && !is_wp_error($term)) {
                    // Return name or ID based on display type
                    return $taxonomy_display === 'id' ? (string) $term->term_id : $term->name;
                }
            } else {
                // Regular custom field - return as is
                if (is_array($value)) {
                    // If array, join with comma
                    return implode(', ', array_filter($value));
                } elseif (is_object($value)) {
                    // If object, try to get a string representation
                    if (isset($value->name)) {
                        return $value->name;
                    } elseif (isset($value->post_title)) {
                        return $value->post_title;
                    }
                    return (string) $value;
                }
                return (string) $value;
            }
        }
    }

    return '';
}

/**
 * Get recruitment status from opening/closing dates.
 *
 * @param mixed $opening_date Opening date value, or post ID for backward compatibility.
 * @param mixed $closing_date Closing date value.
 * @param int   $post_id      Post ID (optional).
 * @return string Status: upcoming, open, or closed.
 */
function omsar_calculate_recruitment_status_from_dates($opening_date = null, $closing_date = null, $post_id = null) {
    if (!$post_id && $opening_date && is_numeric($opening_date) && $opening_date > 0 && $opening_date < 999999999) {
        $test_post = get_post($opening_date);
        if ($test_post && $test_post->post_type === 'recruitments') {
            $post_id = intval($opening_date);
        }
    }

    if (!$post_id) {
        $post_id = get_the_ID();
    }
    if (!$post_id && isset($GLOBALS['post']) && $GLOBALS['post']) {
        $post_id = $GLOBALS['post']->ID;
    }

    if ($post_id && function_exists('omsar_get_recruitment_status_from_acf')) {
        return omsar_get_recruitment_status_from_acf($post_id);
    }

    if (function_exists('omsar_compute_recruitment_status_from_date_values')) {
        return omsar_compute_recruitment_status_from_date_values($opening_date, $closing_date);
    }

    return 'upcoming';
}

/**
 * Filter recruitment posts by computed date-based status.
 */
function omsar_filter_recruitments_by_status($posts, $status_filter) {
    if ($status_filter === 'all' || empty($status_filter)) {
        return $posts;
    }

    $status_filter  = strtolower(trim($status_filter));
    $filtered_posts = array();

    foreach ($posts as $post) {
        $post_status = function_exists('omsar_get_recruitment_status_from_acf')
            ? omsar_get_recruitment_status_from_acf($post->ID)
            : 'upcoming';

        if ($post_status === $status_filter) {
            $filtered_posts[] = $post;
        }
    }

    return $filtered_posts;
}

/**
 * Helper function to sort recruitments by status priority and post date
 * 
 * Priority order: Open (1) > Upcoming (2) > Closed (3)
 * Within each status group, sort by post date descending (newest first)
 * 
 * @param array $posts Array of WP_Post objects
 * @return array Sorted array of posts
 */
function omsar_sort_recruitments_by_status_priority($posts) {
    // Status priority mapping: lower number = higher priority
    $status_priority = array(
        'open' => 1,
        'upcoming' => 2,
        'closed' => 3,
    );
    
    // Get status from ACF field for each post
    $posts_with_data = array();
    foreach ($posts as $post) {
        // Get status from ACF field
        if (function_exists('omsar_get_recruitment_status_from_acf')) {
            $status = omsar_get_recruitment_status_from_acf($post->ID);
        } else {
            $status = 'upcoming';
        }
        
        // Use post date for sorting (newest first = descending)
        $post_timestamp = strtotime($post->post_date);
        
        $posts_with_data[] = array(
            'post' => $post,
            'status' => $status,
            'status_priority' => isset($status_priority[$status]) ? $status_priority[$status] : 999,
            'post_timestamp' => $post_timestamp,
        );
    }
    
    // Sort by status priority first, then by post date descending (newest first)
    usort($posts_with_data, function($a, $b) {
        // First sort by status priority (lower number = higher priority)
        if ($a['status_priority'] !== $b['status_priority']) {
            return $a['status_priority'] - $b['status_priority'];
        }
        
        // Within same status, sort by post date descending (newest first)
        return $b['post_timestamp'] - $a['post_timestamp'];
    });
    
    // Extract posts from sorted array
    $sorted_posts = array();
    foreach ($posts_with_data as $item) {
        $sorted_posts[] = $item['post'];
    }
    
    return $sorted_posts;
}

/**
 * AJAX handler for loading more recruitments posts
 */
add_action('wp_ajax_load_more_recruitments', 'omsar_load_more_recruitments');
add_action('wp_ajax_nopriv_load_more_recruitments', 'omsar_load_more_recruitments');

function omsar_load_more_recruitments() {
    // Verify nonce for security
    check_ajax_referer('load_more_recruitments_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 9;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'meta_value';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $columns = isset($_POST['columns']) ? sanitize_text_field($_POST['columns']) : '3';
    $background_type = isset($_POST['background_type']) ? sanitize_text_field($_POST['background_type']) : 'image';
    $background_image = isset($_POST['background_image']) ? esc_url_raw($_POST['background_image']) : '';
    $background_overlay = isset($_POST['background_overlay']) && $_POST['background_overlay'] === 'yes';
    $status_filter = isset($_POST['status_filter']) ? sanitize_text_field($_POST['status_filter']) : 'all';

    // IMPORTANT: Only use hardcoded default if background_image is truly empty
    // The background_image from POST should contain the configured default from the widget
    // If it's empty, it means no custom image was configured, so use the control's default
    // This matches the widget's default fallback logic
    if ($background_type === 'image' && empty($background_image)) {
        // Only fall back to hardcoded default if no image was configured
        $background_image = get_template_directory_uri() . '/assets/images/project-2.png';
    }

    // Build query args - fetch all posts to sort by status priority
    $args = array(
        'post_type' => 'recruitments',
        'posts_per_page' => -1, // Get all posts for proper sorting
        'post_status' => 'publish',
        'update_post_term_cache' => false,
        'no_found_rows' => true // Don't count for performance since we're getting all
    );

    $query = new WP_Query($args);
    
    // Sort all posts by status priority (Open > Upcoming > Closed) and then by opening_date descending
    $all_posts = $query->posts;
    if (!empty($all_posts)) {
        $all_posts = omsar_sort_recruitments_by_status_priority($all_posts);
    }
    
    // Filter by status if needed
    if ($status_filter !== 'all') {
        $all_posts = omsar_filter_recruitments_by_status($all_posts, $status_filter);
    }
    
    // Paginate the sorted (and optionally filtered) results
    $offset = ($page - 1) * $per_page;
    $paginated_posts = array_slice($all_posts, $offset, $per_page);
    
    // Replace query posts with sorted, filtered, and paginated posts
    $query->posts = $paginated_posts;
    $query->post_count = count($paginated_posts);
    
    // Calculate totals based on sorted (and optionally filtered) results
    $total_found = count($all_posts);
    $posts_on_this_page = count($paginated_posts);
    $total_loaded_so_far = $offset + $posts_on_this_page;
    $has_more = ($total_found > $total_loaded_so_far);

    // If no posts, return appropriate empty-state message
    if (!$query->have_posts()) {
        wp_reset_postdata();
        if ($status_filter === 'open') {
            $no_results_text = function_exists('pll__') ? pll__('There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.') : __('There are currently no open positions. Check upcoming opportunities and sign-up to be notified when they become available.', 'omsar');
            $no_results_html = '<div class="omsar-recruitment-item omsar-recruitment-empty-state omsar-recruitment-no-open-positions" data-status="open"><p class="omsar-recruitment-empty-message">' . esc_html($no_results_text) . '</p></div>';
        } else {
            $no_results_text = function_exists('pll__') ? pll__('No results found.') : __('No results found.', 'omsar');
            $no_results_html = '<div class="omsar-recruitment-item"><p>' . esc_html($no_results_text) . '</p></div>';
        }
        wp_send_json_success(array(
            'html' => $no_results_html,
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => isset($total_found) ? $total_found : 0,
            'loaded' => 0,
            'count' => 0
        ));
    }

    // Check if current language is Arabic
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
    $is_arabic = ($current_lang === 'ar');

    // Get labels
    $open_label      = function_exists('pll__') ? pll__('Open') : 'Open';
    $closed_label    = function_exists('pll__') ? pll__('Closed') : 'Closed';
    $upcoming_label  = function_exists('pll__') ? pll__('Upcoming') : 'Upcoming';
    $apply_label     = function_exists('pll__') ? pll__('Apply') : 'Apply';
    $notify_me_label = function_exists('pll__') ? pll__('Notify me') : 'Notify me';

    // Use output buffering for faster HTML generation
    ob_start();
    
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        
        // Get post data - use Arabic fields if Arabic, otherwise use English
        if ($is_arabic) {
            $job_title_ar = get_field('job_title_ar');
            $entity_ar = get_field('entity_ar');
            // Use Arabic fields if they exist, otherwise fallback to English
            $job_title = !empty($job_title_ar) ? $job_title_ar : get_the_title();
            $entity = !empty($entity_ar) ? $entity_ar : get_field('entity');
        } else {
            $job_title = get_the_title();
            $entity = get_field('entity');
        }
        
        // Get featured image or use configured default
        $item_background_image = $background_image;
        if ($background_type === 'image') {
            if (has_post_thumbnail($post_id)) {
                $item_background_image = get_the_post_thumbnail_url($post_id, 'large');
            } elseif (!empty($background_image)) {
                $item_background_image = $background_image;
            }
        }
        
        $opening_date = get_field('opening_date');
        $closing_date = get_field('closing_date');
        $job_link = get_field('job_link');
        
        // Format dates
        $opening_date_formatted = '';
        $closing_date_formatted = '';
        $opening_date_obj       = null;
        $closing_date_obj       = null;
        
        if ($opening_date) {
            if (is_numeric($opening_date) && strlen($opening_date) == 8) {
                $date_obj = DateTime::createFromFormat('Ymd', $opening_date);
                if ($date_obj) {
                    $opening_date_obj       = $date_obj;
                    $opening_date_formatted = $date_obj->format('d/m/Y');
                }
            } else {
                $date_obj = DateTime::createFromFormat('Y-m-d', $opening_date);
                if (!$date_obj) {
                    $date_obj = DateTime::createFromFormat('d/m/Y', $opening_date);
                }
                if ($date_obj) {
                    $opening_date_obj       = $date_obj;
                    $opening_date_formatted = $date_obj->format('d/m/Y');
                } else {
                    $opening_date_formatted = $opening_date;
                }
            }
        }
        
        if ($closing_date) {
            if (is_numeric($closing_date) && strlen($closing_date) == 8) {
                $closing_date_obj = DateTime::createFromFormat('Ymd', $closing_date);
                if ($closing_date_obj) {
                    $closing_date_formatted = $closing_date_obj->format('d/m/Y');
                }
            } else {
                $closing_date_obj = DateTime::createFromFormat('Y-m-d', $closing_date);
                if (!$closing_date_obj) {
                    $closing_date_obj = DateTime::createFromFormat('d/m/Y', $closing_date);
                }
                if ($closing_date_obj) {
                    $closing_date_formatted = $closing_date_obj->format('d/m/Y');
                } else {
                    $closing_date_formatted = $closing_date;
                }
            }
        }
        
        // Get recruitment status from ACF field (use helper function if available)
        if (function_exists('omsar_get_recruitment_status_from_acf')) {
            $status_type = omsar_get_recruitment_status_from_acf(get_the_ID());
        } else {
            // Fallback: direct ACF field read - check both field name variations
            // Try typo version first (the actual field name: recruitement_status)
            $status = get_field('recruitement_status', get_the_ID());
            if (empty($status) || $status === false) {
                $status = get_field('recruitment_status', get_the_ID());
            }
            if (is_array($status)) {
                $status = reset($status);
            }
            if (!empty($status) && $status !== false) {
                $status_type = strtolower(trim($status));
                if (!in_array($status_type, array('open', 'closed', 'upcoming'))) {
                    $status_type = 'closed';
                }
            } else {
                $status_type = 'closed';
            }
        }

        if ('upcoming' === $status_type) {
            $status_badge = $upcoming_label;
        } elseif ('open' === $status_type) {
            $status_badge = $open_label;
        } else {
            $status_badge = $closed_label;
        }

        $status_class = 'status-' . $status_type;
        ?>
        <div class="omsar-recruitment-item">
            <div class="omsar-recruitment-card <?php echo $is_arabic ? 'rtl-card' : ''; ?><?php echo $background_overlay ? ' has-overlay' : ''; ?>"
                <?php if ($is_arabic) : ?> dir="rtl"<?php endif; ?>
                <?php if ($background_type === 'image' && !empty($item_background_image)) : ?>
                    style="background-image: url('<?php echo esc_url($item_background_image); ?>');"
                <?php endif; ?>>
                <div class="omsar-recruitment-content">
                    <div class="omsar-recruitment-header">
                        <h3 class="omsar-recruitment-title"><?php echo esc_html($job_title); ?></h3>
                        <div class="omsar-recruitment-badge <?php echo esc_attr($status_class); ?>">
                            <?php echo esc_html($status_badge); ?>
                        </div>
                    </div>
                    
                    <div class="omsar-recruitment-details">
                        <?php if ($entity) : ?>
                            <p class="omsar-recruitment-entity">
                                <i class="bi bi-building" aria-hidden="true"></i>
                                <span class="detail-value"><?php echo esc_html($entity); ?></span>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($opening_date_formatted || $closing_date_formatted) : ?>
                            <p class="omsar-recruitment-dates">
                                <i class="bi bi-calendar3" aria-hidden="true"></i>
                                <?php if ($opening_date_formatted && $closing_date_formatted) : ?>
                                    <span class="detail-value"><?php echo esc_html($opening_date_formatted); ?> - <?php echo esc_html($closing_date_formatted); ?></span>
                                <?php elseif ($opening_date_formatted) : ?>
                                    <span class="detail-value"><?php echo esc_html($opening_date_formatted); ?></span>
                                <?php elseif ($closing_date_formatted) : ?>
                                    <span class="detail-value"><?php echo esc_html($closing_date_formatted); ?></span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ('open' === $status_type && !empty($job_link)) : ?>
                        <div class="omsar-recruitment-actions">
                            <a href="<?php echo esc_url($job_link); ?>" class="omsar-recruitment-apply-btn" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($apply_label); ?>
                            </a>
                        </div>
                    <?php elseif ('upcoming' === $status_type) : ?>
                        <div class="omsar-recruitment-actions">
                            <button type="button" class="omsar-recruitment-apply-btn omsar-recruitment-notify-btn" data-recruitment-id="<?php echo esc_attr(get_the_ID()); ?>">
                                <?php echo esc_html($notify_me_label); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Ensure HTML is always a string (even if empty)
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'next_page' => $page + 1,
        'total' => $total_found,
        'loaded' => $total_loaded_so_far,
        'count' => $posts_on_this_page
    ));
}

/**
 * Filter procurement notices by computed status.
 */
function omsar_filter_procurements_by_status($posts, $status_filter) {
    if ($status_filter === 'all' || empty($status_filter)) {
        return $posts;
    }

    $filtered_posts = array();

    foreach ($posts as $post) {
        $post_status = function_exists('omsar_get_procurement_status')
            ? omsar_get_procurement_status($post->ID)
            : 'closed';

        if ($post_status === $status_filter) {
            $filtered_posts[] = $post;
        }
    }

    return $filtered_posts;
}

/**
 * Helper function to sort procurements by status priority and publication_custom_date
 * 
 * Priority order: Open (1) > Cancelled (2) > Closed (3)
 * Within each status group, sort by publication_custom_date descending (newest first)
 * 
 * @param array $posts Array of WP_Post objects
 * @return array Sorted array of posts
 */
function omsar_sort_procurements_by_status_priority($posts) {
    // Status priority mapping: lower number = higher priority
    $status_priority = array(
        'open' => 1,
        'cancelled' => 2,
        'closed' => 3,
    );
    
    // Parse dates and calculate status for each post
    $posts_with_data = array();
    foreach ($posts as $post) {
        $publication_custom_date = get_field('publication_custom_date', $post->ID);
        $status = function_exists('omsar_get_procurement_status')
            ? omsar_get_procurement_status($post->ID)
            : 'closed';

        $publication_timestamp = function_exists('omsar_get_procurement_publication_timestamp')
            ? omsar_get_procurement_publication_timestamp($publication_custom_date)
            : 0;
        
        $posts_with_data[] = array(
            'post' => $post,
            'status' => $status,
            'status_priority' => isset($status_priority[$status]) ? $status_priority[$status] : 999,
            'publication_timestamp' => $publication_timestamp,
        );
    }
    
    // Sort by status priority first, then by publication_custom_date descending
    usort($posts_with_data, function($a, $b) {
        // First sort by status priority (lower number = higher priority)
        if ($a['status_priority'] !== $b['status_priority']) {
            return $a['status_priority'] - $b['status_priority'];
        }
        
        // Within same status, sort by publication_custom_date descending (newest first)
        return $b['publication_timestamp'] - $a['publication_timestamp'];
    });
    
    // Extract posts from sorted array
    $sorted_posts = array();
    foreach ($posts_with_data as $item) {
        $sorted_posts[] = $item['post'];
    }
    
    return $sorted_posts;
}

/**
 * AJAX handler for loading filtered procurement notices
 */
add_action('wp_ajax_load_more_procurements', 'omsar_load_more_procurements');
add_action('wp_ajax_nopriv_load_more_procurements', 'omsar_load_more_procurements');

function omsar_load_more_procurements() {
    // Verify nonce for security
    check_ajax_referer('load_more_procurements_nonce', 'nonce');

    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 6;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $columns = isset($_POST['columns']) ? sanitize_text_field($_POST['columns']) : '3';
    $default_image = isset($_POST['default_image']) ? esc_url_raw($_POST['default_image']) : '';
    $gradient_enabled = isset($_POST['gradient_enabled']) && $_POST['gradient_enabled'] === 'yes';
    $status_filter = isset($_POST['status_filter']) ? sanitize_text_field($_POST['status_filter']) : 'all';

    // Build query args - fetch all posts to sort by status priority
    $args = array(
        'post_type' => 'procurement_notices',
        'posts_per_page' => -1, // Get all posts for proper sorting
        'post_status' => 'publish',
        'update_post_term_cache' => false,
        'no_found_rows' => true // Don't count for performance since we're getting all
    );

    $query = new WP_Query($args);
    
    // Sort all posts by status priority (Open > Cancelled > Closed) and then by publication_custom_date descending
    $all_posts = $query->posts;
    if (!empty($all_posts)) {
        $all_posts = omsar_sort_procurements_by_status_priority($all_posts);
    }
    
    // Filter by status if needed
    if ($status_filter !== 'all') {
        $all_posts = omsar_filter_procurements_by_status($all_posts, $status_filter);
    }
    
    // Paginate the sorted (and optionally filtered) results
    $offset = ($page - 1) * $per_page;
    $paginated_posts = array_slice($all_posts, $offset, $per_page);
    
    // Replace query posts with sorted, filtered, and paginated posts
    $query->posts = $paginated_posts;
    $query->post_count = count($paginated_posts);
    
    // Calculate totals based on sorted (and optionally filtered) results
    $total_found = count($all_posts);
    $posts_on_this_page = count($paginated_posts);
    $total_loaded_so_far = $offset + $posts_on_this_page;
    // Show Load More whenever there are remaining posts (including last partial page)
    $has_more = (bool) ( $total_found > $total_loaded_so_far );

    // If no posts, return early
    if (!$query->have_posts()) {
        wp_reset_postdata();
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => isset($total_found) ? $total_found : 0,
            'loaded' => 0,
            'count' => 0
        ));
    }

    if ( ! function_exists( 'omsar_bd_render_procurement_notice_card' ) ) {
        $render_file = get_template_directory() . '/inc/breakdance-widgets/includes/procurement-notices-render.php';
        if ( file_exists( $render_file ) ) {
            require_once $render_file;
        }
    }

    $card_args = array(
        'default_image'    => $default_image,
        'gradient_enabled' => $gradient_enabled,
        'labels'           => function_exists( 'omsar_bd_procurement_notices_labels' ) ? omsar_bd_procurement_notices_labels() : array(),
    );

    ob_start();

    while ( $query->have_posts() ) {
        $query->the_post();
        if ( function_exists( 'omsar_bd_render_procurement_notice_card' ) ) {
            omsar_bd_render_procurement_notice_card( get_the_ID(), $card_args );
        }
    }
    
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // Ensure HTML is always a string (even if empty)
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => (bool) $has_more,
        'next_page' => $page + 1,
        'total' => (int) $total_found,
        'loaded' => (int) $total_loaded_so_far,
        'count' => (int) $posts_on_this_page
    ));
}

/**
 * AJAX handler for loading more Knowledge and Resources
 */
add_action('wp_ajax_load_more_knowledge_resources', 'omsar_load_more_knowledge_resources');
add_action('wp_ajax_nopriv_load_more_knowledge_resources', 'omsar_load_more_knowledge_resources');

function omsar_load_more_knowledge_resources() {
    check_ajax_referer('load_more_knowledge_resources_nonce', 'nonce');

    $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
    $per_page = isset($_POST['per_page']) ? (int) $_POST['per_page'] : 6;
    $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    $default_image = isset($_POST['default_image']) ? esc_url_raw($_POST['default_image']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
    $cards_clickable = isset($_POST['cards_clickable']) && ( $_POST['cards_clickable'] === '1' || $_POST['cards_clickable'] === 'yes' );

    $args = array(
        'post_type' => 'knowledge_resources',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'paged' => $page,
        'orderby' => $orderby,
        'order' => $order,
        'update_post_term_cache' => false,
        'no_found_rows' => false,
    );

    if (function_exists('pll_current_language')) {
        $args['lang'] = pll_current_language();
    }

    if ($search !== '') {
        $args['s'] = $search;
    }

    $query = new WP_Query($args);
    $total_found = $query->found_posts;
    $posts_on_this_page = $query->post_count;
    $total_loaded_so_far = ($page - 1) * $per_page + $posts_on_this_page;
    $has_more = $total_found > $total_loaded_so_far;

    if (!$query->have_posts()) {
        wp_reset_postdata();
        $no_results_msg = $search !== ''
            ? (function_exists('pll__') ? pll__('No results found.') : __('No results found.', 'omsar'))
            : (function_exists('pll__') ? pll__('No knowledge and resources are available at this time.') : __('No knowledge and resources are available at this time.', 'omsar'));
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false,
            'next_page' => $page + 1,
            'total' => $total_found,
            'loaded' => $total_loaded_so_far,
            'count' => 0,
            'no_results' => true,
            'no_results_message' => $no_results_msg,
        ));
    }

    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
    $is_rtl = ($current_lang === 'ar' || is_rtl());
    $downloads_label = function_exists('pll__') ? pll__('Downloads') : __('Downloads', 'omsar');

    if ( ! function_exists( 'omsar_bd_get_knowledge_resource_card_html' ) ) {
        $render_file = get_template_directory() . '/inc/breakdance-widgets/includes/knowledge-resources-render.php';
        if ( file_exists( $render_file ) ) {
            require_once $render_file;
        }
    }

    $card_settings = function_exists( 'omsar_bd_normalize_knowledge_resources_settings' )
        ? omsar_bd_normalize_knowledge_resources_settings(
            array(
                'default_image'   => $default_image,
                'cards_clickable' => $cards_clickable,
            )
        )
        : array(
            'default_image'   => $default_image,
            'cards_clickable' => $cards_clickable,
        );

    $html_parts = array();
    while ($query->have_posts()) {
        $query->the_post();
        if ( function_exists( 'omsar_bd_get_knowledge_resource_card_html' ) ) {
            $html_parts[] = omsar_bd_get_knowledge_resource_card_html(
                get_the_ID(),
                $card_settings,
                $downloads_label
            );
        } else {
            require_once get_template_directory() . '/inc/elementor-widgets/knowledge-and-resources-widget.php';
            $html_parts[] = OMSAR_Knowledge_And_Resources_Widget::get_card_html(
                get_the_ID(),
                $default_image,
                $downloads_label,
                $is_rtl,
                $cards_clickable
            );
        }
    }
    wp_reset_postdata();

    $html = implode('', $html_parts);
    if (!is_string($html)) {
        $html = '';
    }

    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'next_page' => $page + 1,
        'total' => $total_found,
        'loaded' => $total_loaded_so_far,
        'count' => $posts_on_this_page,
        'no_results' => false,
        'no_results_message' => '',
    ));
}

/**
 * Get ACF field type (for AJAX handlers)
 * 
 * @param string $field_name Field name.
 * @return string Field type.
 */
function omsar_get_acf_field_type_for_ajax($field_name) {
    if (!function_exists('acf_get_field_groups') || empty($field_name)) {
        return '';
    }

    $field_groups = acf_get_field_groups();

    foreach ($field_groups as $field_group) {
        $fields = acf_get_fields($field_group['ID']);
        
        if ($fields) {
            foreach ($fields as $field) {
                if ($field['name'] === $field_name) {
                    return !empty($field['type']) ? $field['type'] : '';
                }
            }
        }
    }

    return '';
}
