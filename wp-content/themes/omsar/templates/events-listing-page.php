<?php
/*
Template Name: Events Listing Page
*/
?>

<?php
get_header();

?>

        <!-- Events Section -->

<section class="media-page-section">
            <div class="container static-height">

            <?php
            // Radio option value for events
            $post_type_value = 'events';
            
            // Get unique years from published events posts using custom_date field
            // custom_date format: "19/12/2025 7:05 am" (d/m/Y g:i a)
            $years_args = array(
                'post_type' => 'post',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key'     => 'post_type_option',
                        'value'   => $post_type_value,
                        'compare' => '='
                    )
                ),
                'fields' => 'ids',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'no_found_rows' => true
            );
            $years_query = new WP_Query($years_args);
            $available_years = array();
            
            if ($years_query->have_posts()) {
                foreach ($years_query->posts as $post_id) {
                    $custom_date_raw = get_post_meta($post_id, 'custom_date', true);
                    if ($custom_date_raw) {
                        // Parse date format: "19/12/2025 7:05 am" (d/m/Y g:i a)
                        $year = null;
                        
                        // Try DateTime with various format patterns
                        $date_formats = array(
                            'd/m/Y g:i a',  // "19/12/2025 7:05 am"
                            'd/m/Y g:i A',  // "19/12/2025 7:05 AM"
                            'd/m/Y H:i',    // "19/12/2025 07:05" (24-hour)
                            'd/m/Y',        // "19/12/2025" (date only)
                        );
                        
                        $date_obj = false;
                        foreach ($date_formats as $format) {
                            $date_obj = DateTime::createFromFormat($format, $custom_date_raw);
                            if ($date_obj !== false) {
                                break;
                            }
                        }
                        
                        if ($date_obj !== false) {
                            $year = $date_obj->format('Y');
                        } else {
                            // Fallback: try strtotime (convert d/m/Y to d-m-Y for better compatibility)
                            $date_for_strtotime = str_replace('/', '-', $custom_date_raw);
                            $timestamp = strtotime($date_for_strtotime);
                            if ($timestamp !== false) {
                                $year = date('Y', $timestamp);
                            } else {
                                // Last resort: extract 4-digit year using regex
                                if (preg_match('/\b(\d{4})\b/', $custom_date_raw, $matches)) {
                                    $year = $matches[1];
                                }
                            }
                        }
                        
                        if ($year && !in_array($year, $available_years)) {
                            $available_years[] = $year;
                        }
                    }
                }
            }
            wp_reset_postdata();
            
            // Sort years in descending order (newest first)
            rsort($available_years);
            ?>

            <!-- Filter Dropdowns -->
                <div class="row media-filters-row">
                    <div class="col-12 d-flex justify-content-start gap-3">
                        <div class="media-filter-wrapper">
                            <!-- <label for="year-filter" class="media-filter-label">Year</label> -->
                            <select id="year-filter" class="media-filter-select">
                                <option value=""><?php pll_e('Year'); ?></option>
                                <?php if (!empty($available_years)) : ?>
                                    <?php foreach ($available_years as $year) : ?>
                                        <option value="<?php echo esc_attr($year); ?>"><?php echo esc_html($year); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="media-filter-wrapper">
                            <!-- <label for="month-filter" class="media-filter-label">Month</label> -->
                            <select id="month-filter" class="media-filter-select">
                                <option value=""><?php pll_e('Month'); ?></option>
                                <option value="01"><?php pll_e('January'); ?></option>
                                <option value="02"><?php pll_e('February'); ?></option>
                                <option value="03"><?php pll_e('March'); ?></option>
                                <option value="04"><?php pll_e('April'); ?></option>
                                <option value="05"><?php pll_e('May'); ?></option>
                                <option value="06"><?php pll_e('June'); ?></option>
                                <option value="07"><?php pll_e('July'); ?></option>
                                <option value="08"><?php pll_e('August'); ?></option>
                                <option value="09"><?php pll_e('September'); ?></option>
                                <option value="10"><?php pll_e('October'); ?></option>
                                <option value="11"><?php pll_e('November'); ?></option>
                                <option value="12"><?php pll_e('December'); ?></option>
                            </select>
                        </div>
                        
                    </div>
                </div>
                
                
                <!-- Events Cards Grid -->
                <div class="row g-4" id="events-cards-container">
                    <?php

                    // Optimized count query - only get IDs for faster counting
                    // Use same meta_query format as AJAX handler
                    $count_args = array(
                        'post_type' => 'post',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key'     => 'post_type_option',
                                'value'   => $post_type_value,
                                'compare' => '='
                            )
                        ),
                        'fields' => 'ids',
                        'update_post_meta_cache' => false,
                        'update_post_term_cache' => false,
                        'no_found_rows' => false
                    );
                    $count_query = new WP_Query($count_args);
                    $total_posts = $count_query->found_posts;
                    wp_reset_postdata();

                    // Initial query - show only 8 posts (optimized)
                    // Use same meta_query format as AJAX handler
                    $args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 8,
                        'post_status' => 'publish',
                        'meta_query' => array(
                            array(
                                'key'     => 'post_type_option',
                                'value'   => $post_type_value,
                                'compare' => '='
                            )
                        ),
                        'meta_key' => 'custom_date',
                        'orderby' => 'meta_value',
                        'order' => 'DESC',
                        // Performance optimizations
                        'update_post_term_cache' => false
                    );
                    $events_query = new WP_Query($args);

                    // Cache calendar icon URL
                    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
                    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

                    if ($events_query->have_posts()) :
                        while ($events_query->have_posts()) : $events_query->the_post();
                            $title = get_the_title();
                            // Use optimized image size instead of 'full'
                            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'news-card-thumb');
                            if (!$image_url) {
                                // Try medium size as fallback
                                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                            }
                            // Fallback to default image if no featured image exists
                            if (!$image_url) {
                                $image_url = $default_image;
                            }
                            $custom_date_raw = get_post_meta(get_the_ID(), 'custom_date', true);

                            $date = '';
                            $is_upcoming = false;

                            if ($custom_date_raw) {
                                $custom_timestamp = strtotime($custom_date_raw);
                                $today_timestamp  = strtotime(date('Y-m-d'));

                                $date = date('d-m-Y', $custom_timestamp);

                                if ($custom_timestamp >= $today_timestamp) {
                                    $is_upcoming = true;
                                }
                            }

                    ?>
                    <div class="col-lg-3 col-md-6 news-card-item">
                        <a href="<?php the_permalink(); ?>" class="news-card-link">
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
                                    <h3 class="news-title">
                                        <?php echo esc_html($title); ?>
                                    </h3>
                                    <span class="btn rounded-pill px-4 fw-medium read-more-button"><?php pll_e('Read More'); ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                    <div class="col-12">
                        <p><?php echo pll_e("No events found."); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Load More Button -->
                <?php if ($total_posts > 8) : ?>
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <button 
                            type="button" 
                            class="btn btn-load-more-projects" 
                            id="load-more-events-btn"
                            data-page="1"
                            data-post-type="<?php echo esc_attr($post_type_value); ?>"
                            data-total="<?php echo esc_attr($total_posts); ?>"
                            data-per-page="8"
                            data-filter-month=""
                            data-filter-year="">
                            <?php echo pll__('Load More') ?>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        

    <?php get_footer(); ?>
    

