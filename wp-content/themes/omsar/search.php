<?php
/**
 * The template for displaying search results
 *
 * @package OMSAR
 */

get_header();
?>

<div id="primary" class="content-area">
    <div class="search-results-section">
        <div class="container py-5">
            
            <!-- Search Header -->
            <div class="search-header mb-5">
                <div class="row">
                    <div class="col-12">
                        <h1 class="search-title mb-3">
                            <?php
                            if (have_posts()) {
                                global $wp_query;
                                $search_query = get_search_query();
                                $results_count = $wp_query->found_posts;
                                printf(
                                    pll__('Search Results for: %s'),
                                    '<span class="search-query">' . esc_html($search_query) . '</span>'
                                );
                                echo '<span class="search-count"> (' . number_format_i18n($results_count) . ' ' . _n(pll__('result'), pll__('results'), $results_count, 'omsar') . ')</span>';
                            } else {
                                printf(
                                    esc_html__('No Results Found for: "%s"', 'omsar'),
                                    '<span class="search-query">' . esc_html(get_search_query()) . '</span>'
                                );
                            }
                            ?>
                        </h1>
                        
                        <!-- Search Form -->
                        <div class="search-form-wrapper mb-4">
                            <form role="search" method="get" class="search-results-form" action="<?php echo esc_url(omsar_get_search_url()); ?>">
                                <div class="search-input-group">
                                    <input 
                                        type="search" 
                                        class="search-results-input" 
                                        placeholder="<?php echo function_exists('pll__') ? esc_attr(pll__('Search...')) : esc_attr__('Search again...', 'omsar'); ?>" 
                                        value="<?php echo esc_attr(get_search_query()); ?>" 
                                        name="s" 
                                        autocomplete="off"
                                        aria-label="<?php esc_attr_e('Search input', 'omsar'); ?>"
                                    />
                                    <button type="button" class="search-clear-btn" aria-label="<?php esc_attr_e('Clear search', 'omsar'); ?>">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <button type="submit" class="search-results-submit" aria-label="<?php esc_attr_e('Submit search', 'omsar'); ?>">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            <?php if (have_posts()) : ?>
                <div class="search-results-container">
                    <div class="row g-4">
                        <?php
                        while (have_posts()) :
                            the_post();
                            $post_id = get_the_ID();
                            $post_type = get_post_type();
                            
                            // Get post type label
                            $post_type_obj = get_post_type_object($post_type);
                            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : ucfirst($post_type);
                            
                            // Translate post type label using Polylang
                            if (function_exists('pll__')) {
                                $post_type_label = pll__($post_type_label);
                            }
                        ?>
                            <div class="col-lg-6 col-md-6 search-result-item">
                                <article class="search-result-card">
                                    <a href="<?php the_permalink(); ?>" class="search-result-link">
                                        <div class="search-result-content">
                                            <span class="search-result-type"><?php echo esc_html($post_type_label); ?></span>
                                            <h3 class="search-result-title">
                                                <?php echo esc_html(get_the_title()); ?>
                                            </h3>
                                        </div>
                                    </a>
                                </article>
                            </div>
                        <?php
                        endwhile;
                        ?>
                    </div>

                    <!-- Pagination -->
                    <div class="search-pagination-wrapper mt-5">
                        <?php
                        // Detect RTL direction
                        $is_rtl = is_rtl() || (function_exists('pll_current_language') && pll_current_language() === 'ar');
                        
                        // Set pagination text based on direction
                       if ($is_rtl) {
                            // RTL: Previous is on the right, Next is on the left
                            $prev_text = pll__('Previous') . ' <i class="bi bi-chevron-right"></i>';
                            $next_text = '<i class="bi bi-chevron-left"></i> ' . pll__('Next');
                        } else {
                            // LTR: Previous is on the left, Next is on the right
                            $prev_text = '<i class="bi bi-chevron-left"></i> ' . pll__('Previous');
                            $next_text = pll__('Next') . ' <i class="bi bi-chevron-right"></i>';
                        }

                        
                        the_posts_pagination(array(
                            'mid_size'  => 2,
                            'prev_text' => $prev_text,
                            'next_text' => $next_text,
                            'class'     => 'search-pagination',
                        ));
                        ?>
                    </div>
                </div>
            <?php else : ?>
                <!-- No Results -->
                <div class="search-no-results">
                    <div class="no-results-content">
                        <div class="no-results-icon mb-4">
                            <i class="bi bi-search"></i>
                        </div>
                        <h2 class="no-results-title mb-3">
                            <?php echo function_exists('pll__') ? pll__('No Results Found.') : esc_html__('No Results Found.', 'omsar'); ?>
                        </h2>
                        <p class="no-results-text mb-4">
                            <?php echo function_exists('pll__') ? pll__('Sorry, but nothing matched your search terms. Please try again with different keywords.') : esc_html__('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'omsar'); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var clearBtn = document.querySelector('.search-clear-btn');
    var searchInput = document.querySelector('.search-results-input');
    if (clearBtn && searchInput) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
        });
    }
});
</script>
