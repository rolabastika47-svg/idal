<?php
/*
Template Name: News Listing Page
*/
?>

<?php
get_header();

?>

        <!-- Media Section -->
        <section class="media-page-section">
            <div class="container static-height">

             <?php
             // Radio option value for news
             $post_type_value = 'news';
             
             // Get unique years from published news posts
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
                 'orderby' => 'date',
                 'order' => 'DESC',
                 'update_post_meta_cache' => false,
                 'update_post_term_cache' => false,
                 'no_found_rows' => true
             );
             $years_query = new WP_Query($years_args);
             $available_years = array();
             
             if ($years_query->have_posts()) {
                 foreach ($years_query->posts as $post_id) {
                     $year = get_the_date('Y', $post_id);
                     if ($year && !in_array($year, $available_years)) {
                         $available_years[] = $year;
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
                

                <!-- News Cards Grid -->
                <div class="row g-4" id="news-cards-container">
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
                        'orderby' => 'date',
                        'order' => 'DESC',
                        // Performance optimizations
                        'update_post_term_cache' => false
                    );
                    $news_query = new WP_Query($args);

                    // Cache calendar icon URL
                    $calendar_icon = get_template_directory_uri() . '/assets/images/calendar.svg';
                    $default_image = get_template_directory_uri() . '/assets/images/default-image.png';

                    if ($news_query->have_posts()) :
                        while ($news_query->have_posts()) : $news_query->the_post();
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
                            $publish_date = get_the_date('d-m-Y');
                    ?>
                    <div class="col-lg-3 col-md-6 news-card-item">
                        <a href="<?php the_permalink(); ?>" class="news-card-link">
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
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                    <div class="col-12">
                        <p><?php echo pll_e("No news found."); ?></p>
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
                            id="load-more-news-btn"
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


