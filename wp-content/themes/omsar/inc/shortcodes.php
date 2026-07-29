<?php

/**
 * Shortcodes for OMSAR Theme
 * 
 * This file contains all custom shortcodes for the theme.
 */

// Shortcode: [projects_carousel]
add_shortcode('projects_carousel', function() {

    // Query projects
    $args = array(
        'post_type' => 'projects',
        'posts_per_page' => 6, // all projects
        'post_status' => 'publish'
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No projects found.</p>';
    }

    // Start output buffering
    ob_start();
    ?>
    <div class="row">
        <div class="col-12 projects-carousel-wrapper" >
            <!-- Clickable shadow overlays -->
            <div class="carousel-shadow-overlay carousel-shadow-left" data-direction="prev"></div>
            <div class="carousel-shadow-overlay carousel-shadow-right" data-direction="next"></div>
            <div class="owl-carousel owl-theme projects-carousel">
                <?php while ($query->have_posts()): $query->the_post(); 
                    $title = get_the_title();
                    $content = get_the_content();
                    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                    // Fallback to default image if no featured image exists
                    if (!$image_url) {
                        $image_url = get_template_directory_uri() . '/assets/images/project-2.png';
                    }
                    $pillar_id = get_field('pillar_option');

                    $pillars = $pillar_id ? esc_html(get_term($pillar_id)->name) : '';
                ?>
                    <div class="item">
                        <div class="project-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                            <div class="project-content">
                                <h3 class="project-title"><?php echo esc_html($title); ?></h3>
                                <span class="project-subtitle"><?php echo $pillars; ?></span>
                                <p class="project-desc">
                                    <?php
                                    if ( has_excerpt() ) {
                                        echo wp_kses_post( get_the_excerpt() );
                                    }
                                    ?>
                                </p>
                                <a href="<?php the_permalink(); ?>" class="btn btn-light rounded-pill px-4 fw-medium"><?php pll_e('Read More'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
    <?php

    return ob_get_clean();
});

// Display the shortcode saved in meta key 'latest_projects_shortcode'
function display_latest_projects_carousel($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }

    $shortcode = get_post_meta($post_id, 'latest_projects_shortcode', true);

    if (!empty($shortcode)) {
        // Execute the shortcode stored in the meta key
        echo do_shortcode($shortcode);
    }
}

// Shortcode: [latest_news_carousel]
add_shortcode('latest_news_carousel', function() {

    // Radio option value for news
    $post_type_value = 'news';
    
    // Query posts with custom field post_type_radio = news
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key'     => 'post_type_option',
                'value'   => $post_type_value,
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>' . pll__('No news found.') . '</p>';
    }

    // Start output buffering
    ob_start();
    ?>
    <div class="owl-carousel owl-theme news-carousel">
        <?php while ($query->have_posts()): $query->the_post(); 
            $title = get_the_title();
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
             // Fallback to default image if no featured image exists
            if (!$image_url) {
                $image_url = get_template_directory_uri() . '/assets/images/default-image.png';
            }
            // Check current language and format date accordingly
            // $publish_date = get_the_date('d M Y');
            $publish_date = get_the_date('d-m-Y');

            $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
        ?>
            <div class="item">
                <a href="<?php the_permalink(); ?>" class="news-card-link">
                    <div class="news-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                        <div class="news-overlay"></div>
                        <div class="news-content">
                            <div class="news-date mb-2">
                                <img src="<?php echo esc_url($calendar_icon); ?>" alt="Calendar" class="news-date-icon">
                                <span><?php echo $publish_date; ?></span>
                            </div>
                            <h3 class="news-title pt-4">
                                <?php echo esc_html($title); ?>
                            </h3>
                            <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php

    return ob_get_clean();
});

// Display the shortcode saved in meta key 'latest_news_shortcode'
function display_latest_news_carousel($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }

    $shortcode = get_post_meta($post_id, 'latest_news_shortcode', true);

    if (!empty($shortcode)) {
        // Execute the shortcode stored in the meta key
        echo do_shortcode($shortcode);
    }
}

// Shortcode: [latest_recruitments_carousel]
add_shortcode('latest_recruitments_carousel', function() {

    // Query all published recruitments (ordering applied after status/opening_date sort)
    $args = array(
        'post_type' => 'recruitments',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'no_found_rows' => true,
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>No recruitments found.</p>';
    }

    // Order: Open (latest first) → Upcoming (latest first) → Closed (latest first), all by opening_date DESC
    $sorted_posts = function_exists('omsar_sort_recruitments_by_status_priority')
        ? omsar_sort_recruitments_by_status_priority($query->posts)
        : $query->posts;
    $carousel_posts = array_slice($sorted_posts, 0, 6);
    $carousel_ids = wp_list_pluck($carousel_posts, 'ID');

    if (empty($carousel_ids)) {
        return '<p>No recruitments found.</p>';
    }

    // Run a second query with post__in + orderby post__in so the loop preserves our order
    $query = new WP_Query(array(
        'post_type' => 'recruitments',
        'post__in' => $carousel_ids,
        'posts_per_page' => 6,
        'orderby' => 'post__in',
        'post_status' => 'publish',
    ));

    // Check if current language is Arabic
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';
    $is_arabic = ($current_lang === 'ar');

    // Start output buffering
    ob_start();
    ?>
    <div class="row">
        <div class="col-12 projects-carousel-wrapper">
            <!-- Clickable shadow overlays -->
            <div class="carousel-shadow-overlay carousel-shadow-left" data-direction="prev"></div>
            <div class="carousel-shadow-overlay carousel-shadow-right" data-direction="next"></div>
            <div class="owl-carousel owl-theme projects-carousel">
                <?php while ($query->have_posts()): $query->the_post();
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
                    
                    $opening_date = get_field('opening_date');
                    $closing_date = get_field('closing_date');
                    // $job_link = get_field('job_link');
                    $job_link = $current_lang === 'ar' ? get_field( 'job_link_ar' ) : get_field( 'job_link' );

                    
                    // Format dates and determine open/closed status
                    $opening_date_formatted = '';
                    $closing_date_formatted = '';
                    $closing_date_obj = null;
                    
                    if ($opening_date) {
                        if (is_numeric($opening_date) && strlen($opening_date) == 8) {
                            $date_obj = DateTime::createFromFormat('Ymd', $opening_date);
                            if ($date_obj) {
                                $opening_date_formatted = $date_obj->format('d/m/Y');
                            }
                        } else {
                            $date_obj = DateTime::createFromFormat('Y-m-d', $opening_date);
                            if (!$date_obj) {
                                $date_obj = DateTime::createFromFormat('d/m/Y', $opening_date);
                            }
                            if ($date_obj) {
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
                    $open_label    = function_exists('pll__') ? pll__('Open') : 'Open';
                    $closed_label  = function_exists('pll__') ? pll__('Closed') : 'Closed';
                    $upcoming_label = function_exists('pll__') ? pll__('Upcoming') : 'Upcoming';
                    $notify_me_label = function_exists('pll__') ? pll__('Notify me') : 'Notify me';
                    if ($status_type === 'upcoming') {
                        $status_badge = $upcoming_label;
                    } elseif ($status_type === 'open') {
                        $status_badge = $open_label;
                    } else {
                        $status_badge = $closed_label;
                    }
                    $status_class = 'status-' . $status_type;
                    
                    // Get featured image or use fallback
                    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                    if (!$image_url) {
                        $image_url = get_template_directory_uri() . '/assets/images/project-2.png';
                    }
                ?>
                    <div class="item">
                        <div class="project-card" style="background-image: url('<?php echo esc_url($image_url); ?>');">
                            <div class="project-content">
                                <h3 class="project-title"><?php echo esc_html($job_title); ?></h3>
                                <span class="project-subtitle <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_badge); ?></span>
                                <p class="project-desc">
                                    <?php if ($entity): ?>
                                        <span class="date-row">
                                            <i class="bi bi-building" aria-hidden="true"></i>
                                            <span class="detail-value"><?php echo esc_html($entity); ?></span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($opening_date_formatted || $closing_date_formatted): ?>
                                        <span class="date-row">
                                            <i class="bi bi-calendar3" aria-hidden="true"></i>
                                            <?php if ($opening_date_formatted && $closing_date_formatted): ?>
                                                <span class="detail-value"><?php echo esc_html($opening_date_formatted); ?> - <?php echo esc_html($closing_date_formatted); ?></span>
                                            <?php elseif ($opening_date_formatted): ?>
                                                <span class="detail-value"><?php echo esc_html($opening_date_formatted); ?></span>
                                            <?php elseif ($closing_date_formatted): ?>
                                                <span class="detail-value"><?php echo esc_html($closing_date_formatted); ?></span>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($status_type === 'open' && !empty($job_link)) : ?>
                                    <a href="<?php echo esc_url($job_link); ?>" class="btn btn-light rounded-pill px-4 fw-medium" target="_blank" rel="noopener noreferrer"><?php pll_e('Apply'); ?></a>
                                <?php elseif ($status_type === 'upcoming') : ?>
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium omsar-recruitment-apply-btn omsar-recruitment-notify-btn" data-recruitment-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        <?php echo esc_html($notify_me_label); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
    <?php
    // Output Recruitment Notify Me Modal once per page (shared with Recruitment widget)
    if (empty($GLOBALS['omsar_recruitment_notify_modal_printed'])) {
        $GLOBALS['omsar_recruitment_notify_modal_printed'] = true;
    ?>
    <!-- Recruitment Notify Me Modal -->
    <div id="omsar-recruitment-notify-modal" class="omsar-recruitment-notify-modal" role="dialog" aria-labelledby="omsar-recruitment-notify-modal-title" aria-hidden="true">
        <div class="omsar-recruitment-notify-modal-overlay"></div>
        <div class="omsar-recruitment-notify-modal-content">
            <button type="button" class="omsar-recruitment-notify-modal-close" aria-label="<?php echo esc_attr(function_exists('pll__') ? pll__('Close') : 'Close'); ?>">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="omsar-recruitment-notify-modal-body">
                <h2 id="omsar-recruitment-notify-modal-title" class="omsar-recruitment-notify-modal-title">
                    <?php echo esc_html(function_exists('pll__') ? pll__('Notify me') : 'Notify Me'); ?>
                </h2>
                <p class="omsar-recruitment-notify-modal-message">
                    <?php echo esc_html(function_exists('pll__') ? pll__('Share your email to be notified when this position becomes available.') : 'Share your email to be notified when this position becomes available.'); ?>
                </p>
                <form id="omsar-recruitment-notify-form" class="omsar-recruitment-notify-form">
                    <input type="hidden" name="recruitment_id" id="omsar-recruitment-notify-recruitment-id" value="">
                    <div class="omsar-recruitment-notify-form-group">
                        <label for="omsar-recruitment-notify-email" class="omsar-recruitment-notify-label">
                            <?php echo esc_html(function_exists('pll__') ? pll__('Email Address') : 'Email Address'); ?>
                        </label>
                        <input type="email" id="omsar-recruitment-notify-email" name="email" class="omsar-recruitment-notify-input" required aria-required="true" aria-invalid="false" aria-describedby="omsar-recruitment-notify-email-error">
                        <span id="omsar-recruitment-notify-email-error" class="omsar-recruitment-notify-error" role="alert" aria-live="polite"></span>
                    </div>
                    <div class="omsar-recruitment-notify-form-actions">
                        <button type="submit" class="omsar-recruitment-notify-submit-btn">
                            <span class="omsar-recruitment-notify-submit-text"><?php echo esc_html(function_exists('pll__') ? pll__('Submit') : 'Submit'); ?></span>
                            <span class="omsar-recruitment-notify-submit-loader" style="display: none;">
                                <span class="spinner"></span>
                                <?php echo esc_html(function_exists('pll__') ? pll__('Submitting...') : 'Submitting...'); ?>
                            </span>
                        </button>
                    </div>
                    <div id="omsar-recruitment-notify-success" class="omsar-recruitment-notify-success" role="alert" aria-live="polite" style="display: none;"></div>
                </form>
            </div>
        </div>
    </div>
    <?php
    }
    return ob_get_clean();
});

// Display the shortcode saved in meta key 'latest_recruitments_shortcode'
function display_latest_recruitments_carousel($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }

    $shortcode = get_post_meta($post_id, 'latest_recruitments_shortcode', true);

    if (!empty($shortcode)) {
        // Execute the shortcode stored in the meta key
        echo do_shortcode($shortcode);
    }
}

