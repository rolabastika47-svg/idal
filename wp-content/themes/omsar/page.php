<?php
/**
 * The template for displaying all pages
 *
 * This is the default template for pages in a WordPress theme.
 *
 * @package OMSAR
 */

get_header(); // Include header.php

// Check if page is built with Elementor
$is_elementor_page = false;
if (did_action('elementor/loaded') && class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance) && isset(\Elementor\Plugin::$instance->db)) {
    $post_id = get_the_ID();
    try {
        $is_elementor_page = \Elementor\Plugin::$instance->db->is_built_with_elementor($post_id);
    } catch (Exception $e) {
        $is_elementor_page = false;
    }
}

// Check if sidebar should be displayed (works for both Elementor and standard pages)
$show_sidebar = omsar_should_display_sidebar();
?>

<div id="primary" class="content-area">
    <div class="container-fluid static-height">
        <div class="row g-0 g-md-1 g-lg-2">
            <?php if ($show_sidebar): ?>
                <!-- Sidebar Column -->
                <aside class="col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <?php omsar_display_page_sidebar(); ?>
                </aside>
                <!-- Content Column -->
                <main id="main" class="site-main col-lg-9 col-md-8">
            <?php else: ?>
                <!-- Full Width Content -->
                <main id="main" class="site-main col-12">
            <?php endif; ?>

                <?php
                while ( have_posts() ) :
                    the_post();

                    // Elementor pages will render their content through the_content()
                    // Elementor's CSS and styles are automatically loaded by Elementor
                    the_content();

                endwhile; // End of the loop.
                ?>

            </main><!-- #main -->
        </div><!-- .row -->
    </div><!-- .container-fluid -->
</div><!-- #primary -->

<?php
get_footer();  // Include footer.php
?>
