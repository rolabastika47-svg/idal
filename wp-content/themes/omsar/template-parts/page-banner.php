<?php
/**
 * Template Part: Page Banner
 * 
 * Displays the page banner with title and optional subtitle
 * 
 * @param string $title - Page title (optional, defaults to post title)
 * @param string $subtitle - Page subtitle (optional)
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Get title - use provided title or fallback to post title
$page_title = isset($args['title']) ? $args['title'] : get_the_title();

// Get subtitle - use provided subtitle, or check page excerpt
$page_subtitle = '';
// Check if subtitle was explicitly set (use array_key_exists to distinguish between null/not-set and empty string)
if (array_key_exists('subtitle', $args)) {
    // Subtitle was explicitly passed - use it (even if empty string to prevent fallback)
    $page_subtitle = $args['subtitle'];
} else {
    // Subtitle was not passed - fall back to page excerpt for pages
    // For single posts and custom post types, don't show excerpt in banner
    // Only show excerpt for pages
    if (is_page()) {
        // Check if page has manual excerpt only (no fallback to content)
        global $post;
        if ($post && !empty($post->post_excerpt)) {
            $excerpt = $post->post_excerpt;
            
            // Clean up the excerpt
            $excerpt = strip_shortcodes($excerpt);
            $excerpt = wp_strip_all_tags($excerpt);
            $excerpt = trim($excerpt);
            
            if (!empty($excerpt)) {
                $page_subtitle = $excerpt;
            }
        }
    }
}

?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <!-- Banner Content -->
        <div class="row">
            <div class="col-12 col-md-8">
                <h1 class="page-banner-title"><?php echo esc_html($page_title); ?></h1>
                <?php if (!empty($page_subtitle)) : ?>
                    <p class="page-banner-subtitle"><?php echo esc_html($page_subtitle); ?></p>
                <?php endif; ?>
            </div>
            <?php 
            // Allow plugins to add content to banner row (e.g., share button)
            $banner_row_extra = apply_filters('omsar_page_banner_row_content', '');
            if (!empty($banner_row_extra)) : ?>
                <div class="col-12 col-md-4 d-flex justify-content-end align-items-start">
                    <?php echo $banner_row_extra; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Breadcrumb Space -->
        <?php 
        // Check if breadcrumb should be displayed
        if (function_exists('omsar_should_display_breadcrumb') && omsar_should_display_breadcrumb()) {
            if (function_exists('omsar_get_breadcrumb')) {
                $breadcrumb = omsar_get_breadcrumb();
                if (!empty($breadcrumb)) {
                    echo '<div class="breadcrumb-space">' . $breadcrumb . '</div>';
                }
            }
        }
        ?>
        <!-- Back Button (under breadcrumb, not aligned with it) -->
        <?php 
        if (function_exists('omsar_should_display_back_button') && omsar_should_display_back_button()) {
            if (function_exists('omsar_get_back_button')) {
                $back_button = omsar_get_back_button();
                if (!empty($back_button)) {
                    echo '<div class="back-button-space">' . $back_button . '</div>';
                }
            }
        }
        ?>

    </div>
</section>

