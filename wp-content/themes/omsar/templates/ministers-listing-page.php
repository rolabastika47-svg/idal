<?php
/*
Template Name: Ministers Listing Page
*/
get_header();
?>

<section class="ministers-listing-section">
    <div class="container">
        <!-- Ministers Grid -->
        <?php
        // Query former_ministers post type
        // Exclude ministers with is_current = 1 (current ministers)
        // ACF checkbox fields are stored as serialized arrays: a:1:{i:0;s:1:"1";}
        $args = array(
            'post_type' => 'former_ministers',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'meta_value',
            'meta_key' => 'assignment_period',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'is_current',
                    'value' => 's:1:"1"',
                    'compare' => 'NOT LIKE'
                ),
                array(
                    'key' => 'is_current',
                    'compare' => 'NOT EXISTS'
                )
            )
        );

        $ministers_query = new WP_Query($args);

        // Default image fallback
        $default_image = get_template_directory_uri() . '/assets/images/default-image.png';
        
        // Get the single minister page URL (page that uses single-minister.php template)
        $single_minister_page = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'single-minister.php',
            'number' => 1,
            'post_status' => 'publish'
        ));
        
        $single_minister_page_url = '';
        if (!empty($single_minister_page)) {
            $single_minister_page_url = get_permalink($single_minister_page[0]->ID);
        }
        ?>
        <div class="row g-4 ministers-grid">
            <?php if ($ministers_query->have_posts()) : ?>
                <?php while ($ministers_query->have_posts()) : $ministers_query->the_post(); 
                    // Get current language
                    $current_lang = function_exists('pll_current_language') ? pll_current_language() : (is_rtl() ? 'ar' : 'en');
                    
                    // Get post data
                    $minister_id = get_the_ID();
                    // Get minister name based on current language
                    if ($current_lang === 'ar') {
                        // For Arabic, use minister_ar_name field if available, otherwise use post title
                        $minister_title = get_field('minister_ar_name', $minister_id);
                        if (empty($minister_title)) {
                            $minister_title = get_the_title();
                        }
                    } else {
                        // For English or other languages, use post title
                        $minister_title = get_the_title();
                    }
                    // $government = get_field('government');
                    if ($current_lang === 'ar') {
                        $government = get_field('government_ar');
                    } else {
                        $government = get_field('government');
                    }
                    $assignment_period = get_field('assignment_period');
                    $end_assignment_period = get_field('end_assignment_period');
                    $description = get_field('description');
                    
                    // Get featured image
                    $minister_image = get_the_post_thumbnail_url($minister_id, 'full');
                    if (!$minister_image) {
                        $minister_image = $default_image;
                    }
                    
                    // Format dates (d/m/Y format)
                    $assignment_period_formatted = '';
                    $end_assignment_period_formatted = '';
                    $period_display = '';
                    
                    if ($assignment_period) {
                        // ACF date picker returns date in YYYYMMDD format or as string
                        // Try to parse and format it
                        if (is_numeric($assignment_period) && strlen($assignment_period) == 8) {
                            // Format: YYYYMMDD
                            $date_obj = DateTime::createFromFormat('Ymd', $assignment_period);
                            if ($date_obj) {
                                $assignment_period_formatted = $date_obj->format('d/m/Y');
                            }
                        } else {
                            // Try to parse as string
                            $date_obj = DateTime::createFromFormat('Y-m-d', $assignment_period);
                            if (!$date_obj) {
                                $date_obj = DateTime::createFromFormat('d/m/Y', $assignment_period);
                            }
                            if ($date_obj) {
                                $assignment_period_formatted = $date_obj->format('d/m/Y');
                            } else {
                                $assignment_period_formatted = $assignment_period;
                            }
                        }
                    }
                    
                    if ($end_assignment_period) {
                        // ACF date picker returns date in YYYYMMDD format or as string
                        if (is_numeric($end_assignment_period) && strlen($end_assignment_period) == 8) {
                            // Format: YYYYMMDD
                            $date_obj = DateTime::createFromFormat('Ymd', $end_assignment_period);
                            if ($date_obj) {
                                $end_assignment_period_formatted = $date_obj->format('d/m/Y');
                            }
                        } else {
                            // Try to parse as string
                            $date_obj = DateTime::createFromFormat('Y-m-d', $end_assignment_period);
                            if (!$date_obj) {
                                $date_obj = DateTime::createFromFormat('d/m/Y', $end_assignment_period);
                            }
                            if ($date_obj) {
                                $end_assignment_period_formatted = $date_obj->format('d/m/Y');
                            } else {
                                $end_assignment_period_formatted = $end_assignment_period;
                            }
                        }
                    }
                    
                    // Combine period dates for display
                    if ($assignment_period_formatted && $end_assignment_period_formatted) {
                        $period_display = $assignment_period_formatted . ' - ' . $end_assignment_period_formatted;
                    } elseif ($assignment_period_formatted) {
                        $period_display = $assignment_period_formatted;
                    }
                    
                    // Build link to single minister page with minister ID parameter
                    if ($single_minister_page_url) {
                        $minister_link = add_query_arg('minister_id', $minister_id, $single_minister_page_url);
                    } else {
                        // Fallback to regular permalink if single minister page not found
                        $minister_link = get_permalink($minister_id);
                    }
                ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-5-item">
                        <a href="<?php echo esc_url($minister_link); ?>" class="minister-card-link">
                            <div class="minister-card">
                                <div class="minister-image-wrapper">
                                    <img src="<?php echo esc_url($minister_image); ?>" alt="<?php echo esc_attr($minister_title); ?>" class="minister-image">
                                </div>
                                <div class="minister-info">
                                    <h3 class="minister-name"><?php echo esc_html($minister_title); ?></h3>
                                    <div class="minister-details">
                                        <?php if ($government) : ?>
                                            <p class="minister-government">
                                                <span class="detail-label"><?php echo pll__('Government'); ?>:</span>
                                                <span class="detail-value"><?php echo esc_html($government); ?></span>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($period_display) : ?>
                                            <p class="minister-period">
                                                <span class="detail-label"><?php echo pll__('Assignment Period'); ?>:</span>
                                                <span class="detail-value"><?php echo esc_html($period_display); ?></span>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; 
                wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-12">
                    <p>No ministers found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
